<?php
include("@/header.php");
$paginaname = 'Save Target';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once __DIR__ . "/private/php/SecureJS.php";

if (!isset($_SESSION['ID'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['ID'];

function getRemainingQuota($odb, $user_id)
{
    $query = $odb->prepare("SELECT COUNT(*) as ip_count FROM saved_ips WHERE user_id = ?");
    $query->execute([$user_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return 25 - $result['ip_count'];
}

$remainingQuota = getRemainingQuota($odb, $user_id);

$quotaExceededError = '';
$ipExistsError = '';

if (isset($_POST['add_ip'])) {
    $ip_address = $_POST['ip_address'];

    if ($remainingQuota <= 0) {
        $quotaExceededError = 'Error : You\'ve reached your quota of saved IPs (25 IPs maximum).';
    } else {

        $query = $odb->prepare("SELECT COUNT(*) as ip_exists FROM saved_ips WHERE user_id = ? AND ip_address = ?");
        $query->execute([$user_id, $ip_address]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $ipExists = $result['ip_exists'];

        if ($ipExists) {
            $ipExistsError = 'Error : The selected IP address is already registered.';
        } else {
            $query = $odb->prepare("INSERT INTO saved_ips (user_id, ip_address) VALUES (?, ?)");
            $query->execute([$user_id, $ip_address]);

            $remainingQuota = getRemainingQuota($odb, $user_id);
        }
    }
}

if (isset($_GET['delete_ip'])) {
    $ip_id = $_GET['delete_ip'];

    $query = $odb->prepare("DELETE FROM saved_ips WHERE user_id = ? AND id = ?");
    $query->execute([$user_id, $ip_id]);

    $remainingQuota = getRemainingQuota($odb, $user_id);

    header('Location: target.php');
}

$query = $odb->prepare("SELECT id, ip_address FROM saved_ips WHERE user_id = ?");
$query->execute([$user_id]);
$saved_ips = $query->fetchAll(PDO::FETCH_ASSOC);

$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;
$totalPages = ceil(count($saved_ips) / $itemsPerPage);
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = $startIndex + $itemsPerPage;
$saved_ips = array_slice($saved_ips, $startIndex, $itemsPerPage);
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <script src="../assets/js/spinner.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            text-align: center;
            background: #080808;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #0c0b0c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .text-below {
            color: #ac50f5;
            margin-top: 150px;
            margin-right: 55px;
        }

        .saved-ips {
            background-color: #0c0b0c;
            padding: 20px;
            border-radius: 10px;
        }

        .list-group {
            list-style: none;
            padding: 0;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0;
        }

        .list-group-item .btn-danger {
            background-color: #d9534f;
            color: #fff;
            border: none;
        }

        .error-td {
            color: red;
        }

        .pagination {
            margin-top: 10px;
        }

        .pagination a {
            padding: 5px 10px;
            text-decoration: none;
            background-color: #337ab7;
            color: #fff;
            border-radius: 4px;
            margin: 0 5px;
        }

        .pagination a.active {
            background-color: #ac50f5;
        }
    </style>
    <script>
        function validateIPAddress() {
            const input = document.getElementById('host');
            const ip = input.value.trim();
            const values = ip.split('.');

            const errorTd = document.getElementById('ip-error');
            errorTd.style.display = 'none';

            if (values.length !== 4) {
                errorTd.innerHTML = 'Error : The IP address must contain 4 values separated by dots.';
                errorTd.style.display = 'table-cell';
                return false;
            }

            for (const value of values) {
                if (isNaN(value) || parseInt(value) < 0 || parseInt(value) > 255) {
                    errorTd.innerHTML = 'Error : IP address values must be integers between 0 and 255.';
                    errorTd.style.display = 'table-cell';
                    return false;
                }
            }

            return true;
        }

        function updateRemainingQuota(remainingQuota) {
            const remainingIpElement = document.getElementById('remaining-ip');
            remainingIpElement.textContent = `Saved IP - IP Remaining: ${remainingQuota}`;
        }
    </script>
</head>

<body>

<div class="container text-below">
    <h2>Save Target</h2>
    <form method="post" onsubmit="return validateIPAddress();">
        <div class="form-group">
            <input type="text" name="ip_address" id="host" placeholder="127.0.0.1" class="form-control">
            <small class="form-text text-muted">Save your IP</small>
            <table>
                <tr>
                    <td id="ip-error" class="error-td"></td>
                </tr>
            </table>
            <?php if (!empty($quotaExceededError)): ?>
                <tr>
                    <td class="error-td"><?php echo $quotaExceededError; ?></td>
                </tr>
            <?php elseif (!empty($ipExistsError)): ?>
                <tr>
                    <td class="error-td"><?php echo $ipExistsError; ?></td>
                </tr>
            <?php endif; ?>
        </div>
        <button type="submit" name="add_ip" class="btn btn-primary">Save IP</button>
    </form>
</div>

<div class="container text-below saved-ips">
    <h3 id="remaining-ip">Saved IP - IP Remaining: <?php echo $remainingQuota; ?></h3>
    <ul class="list-group">
        <?php
        $ipNum = $startIndex + 1;
        foreach ($saved_ips as $ip) {
            echo '<li class="list-group-item">' . $ipNum . '. ' . $ip['ip_address'] . '<a href="?delete_ip=' . $ip['id'] . '&page=' . $currentPage . '" class="btn btn-danger btn-sm">Delete</a></li>';
            $ipNum++;
        }
        ?>
    </ul>

    <div class="pagination">
        <?php
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '">&lt;</a>';
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            $class = ($i === $currentPage) ? 'active' : '';
            echo '<a href="?page=' . $i . '" class="' . $class . '">' . $i . '</a> ';
        }
        if ($currentPage < $totalPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '">&gt;</a>';
        }
        ?>
    </div>
  </div>
</div>
</body>
</html>
