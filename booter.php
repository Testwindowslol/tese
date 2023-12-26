<?php
include("@/header.php");
$paginaname = 'Booter Review';

$booters = array();
$SQLGetFAQ = $odb->query("SELECT * FROM `booter`");
while ($getInfo = $SQLGetFAQ->fetch(PDO::FETCH_ASSOC)) {
    $booters[] = $getInfo;
}

$premiumBooters = array();
$verifiedBooters = array();
$unverifiedBooters = array();
$scammerBooters = array();

foreach ($booters as $booter) {
    if ($booter['status'] === 'Premium') {
        $premiumBooters[] = $booter;
    } elseif ($booter['status'] === 'Verified') {
        $verifiedBooters[] = $booter;
    } elseif ($booter['status'] === 'Unverified') {
        $unverifiedBooters[] = $booter;
    } elseif ($booter['status'] === 'Scammer') {
        $scammerBooters[] = $booter;
    }
}

function compareStresser($a, $b) {
    return strcmp($a['stresser'], $b['stresser']);
}

usort($premiumBooters, 'compareStresser');
usort($verifiedBooters, 'compareStresser');
usort($unverifiedBooters, 'compareStresser');
usort($scammerBooters, 'compareStresser');
?>

<!DOCTYPE html>
<html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
  <script src="../assets/js/spinner.js"></script>
  <div class="page-wrapper">
     <div class="page-content">
    <div class="alert alert-fill-primary">    
          <span data-feather="layers" class="icon-md text-light mr-2"></span>
       <span><?php echo htmlspecialchars($paginaname); ?></span>
       </div>
       <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
              <div class="card">                                   
                        <div class="block-content tab-content">
                          <table class="table table-bordered">
                            <tr>
                              <th>Stresser</th>
                              <th>Descriptions</th>
                              <th>Owner</th>
                              <th>Free Plans</th>
                              <th>Layer 3/4</th>
                              <th>Layer 7</th>
                              <th>Logs Free</th>
                              <th>APIs</th>
                              <th>Rating</th>
                              <th>Status</th>
                              <th>Payement Accept</th>
                            </tr>
                            <tbody>
<?php
function printBooters($booters) {
    foreach ($booters as $booter) {
        $id = $booter['id'];
        $stresser = $booter['stresser'];
        $description = $booter['description'];
        $owner = $booter['owner'];
        $free = $booter['free'];
        $layer34 = $booter['layer34'];
        $layer7 = $booter['layer7'];
        $logs = $booter['logs'];
        $api = $booter['api'];
        $rating = $booter['rating'];
        $status = $booter['status'];
        $payement = $booter['payement'];

        echo '<tr>';
        echo '<td>' . htmlspecialchars($stresser) . '</td>';

        echo '<td>';
        $maxCharsDescription = 40;
        if (strlen($description) > $maxCharsDescription) {
            echo wordwrap(htmlspecialchars($description), $maxCharsDescription, "<br>", true);
        } else {
            echo htmlspecialchars($description);
        }
        echo '</td>';

        echo '<td>' . htmlspecialchars($owner) . '</td>';

        echo '<td>' . (($free === 'Yes') ? '✔️' : '❌') . '</td>';

        $layer34Stars = "";
        for ($i = 1; $i <= 5; $i++) {
            $layer34Stars .= ($i <= $layer34) ? "⭐" : "☆";
        }
        echo '<td>' . $layer34Stars . '</td>';

        $layer7Stars = "";
        for ($i = 1; $i <= 5; $i++) {
            $layer7Stars .= ($i <= $layer7) ? "⭐" : "☆";
        }
        echo '<td>' . $layer7Stars . '</td>';

        echo '<td>' . (($logs === 'Yes') ? '✔️' : '❌') . '</td>';

        echo '<td>' . (($api === 'Yes') ? '✔️' : '❌') . '</td>';

        $ratingStars = "";
        for ($i = 1; $i <= 5; $i++) {
            $ratingStars .= ($i <= $rating) ? "⭐" : "☆";
        }
        echo '<td>' . $ratingStars . '</td>';

        if ($status === 'Premium') {
            echo '<td><span style="color: yellow;">Premium ⭐</td>';
        } elseif ($status === 'Verified') {
            echo '<td><span style="color: green;">Verified ✔️</span></td>';
        } elseif ($status === 'Unverified') {
            echo '<td><span style="color: red;">Unverified ✘</span></td>';
        } elseif ($status === 'Scammer') {
            echo '<td><span style="color: violet;">Scammer 🤡</span></td>';
        } else {
            echo '<td>' . htmlspecialchars($status) . '</td>';
        }

        echo '<td>';
        $maxCharsPayement = 21;
        if (strlen($payement) > $maxCharsPayement) {
            echo wordwrap(htmlspecialchars($payement), $maxCharsPayement, "<br>", true);
        } else {
            echo htmlspecialchars($payement);
        }
        echo '</td>';

        echo '</tr>';
    }
}

// Print Premium booters
printBooters($premiumBooters);

// Print Verified booters
printBooters($verifiedBooters);

// Print Unverified booters
printBooters($unverifiedBooters);

// Print Scammer booters
printBooters($scammerBooters);
?>
</tbody>
</table>
</div>
</div>
</div>
</div>

<div data-clickadilla-banner="376322"></div>
</div>