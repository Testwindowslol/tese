<?php

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 */

include_once "@/header.php";
include_once __DIR__ . "/v2/php/config.php";
include_once __DIR__ . "/v2/php/api_access.php";

global $db;

$paginaname = 'API Access';

$userID = $_SESSION['ID'];

$hasAPIAccess = \APIAccess\hasApiAccess($userID);
if (!$hasAPIAccess) {
    header("Location: /plans.php");
    exit();
}

$apiKey = \APIAccess\getApiKey($userID);

?>

<!--
<!DOCTYPE html>
<html>
-->

<link rel="stylesheet" href="/v2/css/classes.css?r=<?php echo rand(0, 9999); ?>">
<link rel="stylesheet" href="/v2/css/api_access.css?r=<?php echo rand(0, 9999); ?>">

<script type="text/javascript" src="/v2/js/api_access.js?r=<?php echo rand(0, 9999); ?>"></script>

<script src="../assets/js/spinner.js"></script>

<!-- Alert Popup -->
<div class="background-cover" id="background-cover"></div>
<div class="alert-popup" id="alert-popup">
    <div class="alert-popup-title mb-8">
        Warning !
    </div>
    <div class="alert-popup-content">
        Regenerating your API key will make your current API key useless.<br><br>
        You will need to replace your old API key by the new one in your programs.
    </div>
    <div class="alert-popup-buttons">
        <input class="input button-input alert-popop-button background-danger mr-4" id="alert-button-no" type="button" value="Cancel">
        <input class="input button-input alert-popop-button background-success" id="alert-button-yes" type="button" value="I understand">
    </div>
</div>

<!-- Notification Popup -->
<div class="notification-popup" id="notification-popup">
    <div class="notification-popup-icon mr-8" id="notification-popup-icon-success">
        <span data-feather="check-circle" class="mr-16"></span>
    </div>
    <div class="notification-popup-icon mr-8" id="notification-popup-icon-error">
        <span data-feather="x-circle"></span>
    </div>
    <div class="notification-popup-message mr-16" id="notification-popup-message"></div>
</div>

<div class="page-wrapper">
    <div class="page-content">
        <div class="alert alert-fill-primary">
            <span data-feather="globe" class="icon-md text-light mr-2"></span>
            <span><?php echo htmlspecialchars($paginaname); ?></span>
        </div>
        <div class="box mb-12">
            <div class="box-title">
                <span data-feather="globe" class="icon-md box-title-icon"></span>
                API Request Link
            </div>
            <div class="box-content">
                <div class="flex mb-4">
                    <input class="input text-input mr-2" id="api-request-link" type="text" value="https://hexstresser.org/api/v1/attack?api_key=[API_KEY]&host=[HOST]&port=[PORT]&time=[TIME]&method=[METHOD]" readonly>
                    <input class="input button-input" id="button-copy-request-link" type="button" value="Copy">
                </div>
                <div class="label">
                    API Key :
                </div>
                <div class="flex">
                    <!-- 356a192b7913b04c.356a192b7913b04c.356a192b7913b04c54574d18c28d46e6395428ab -->
                    <!-- [user_hash:16].[key_hash:16].[secret:40] = 74 chars -->
                    <input class="input text-input mr-2" id="api-key" type="text" value="<?php echo $apiKey; ?>" readonly>
                    <input class="input button-input mr-2" id="button-copy-api-key" type="button" value="Copy">
                    <input class="input button-input" id="button-regenerate-api-key" type="button" value="Regenerate">
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-title">
                <span data-feather="globe" class="icon-md box-title-icon"></span>
                Methods
            </div>
            <div class="box-content">
                <table class="table">
                    <colgroup>
                        <col span="1" style="width: 50px;">
                        <col span="1" style="">
                        <col span="1" style="">
                        <col span="1" style="">
                        <col span="1" style="width: 100px;">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>API Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    $stmt = $db->prepare("SELECT id, name, fullname, type FROM methods");
                    //$stmt = $db->prepare("SELECT methods.id, methods.name, methods.fullname, faq.description FROM methods JOIN faq ON methods.name = faq.method");
                    $stmt->execute();

                    $id = 1;
                    while ($methodData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $type = $methodData["type"];

                        switch ($type) {
                            case 1:
                                $type = "Layer 4 - Low Protection";
                                break;
                            case 2:
                                $type = "Layer 4 - High Protection";
                                break;
                            case 3:
                                $type = "Layer 4 - Premium Methods";
                                break;
                            case 4:
                                $type = "Layer 7 - Low Protection";
                                break;
                            case 5:
                                $type = "Layer 7 - High Protection";
                                break;
                            case 6:
                                $type = "Layer 7 - Premium Methods";
                                break;
                            default:
                                $type = "Unknown (" . $type . ")";
                                break;
                        }

                        ?>
                        <tr>
                            <!--<td><?php echo $methodData["id"]; ?></td>-->
                            <td><?php echo $id; ?></td>
                            <td><?php echo $methodData["name"]; ?></td>
                            <td><?php echo $methodData["fullname"]; ?></td>
                            <td><?php echo $type; ?></td>
                            <td><div class="status online">Online</div></td>
                        </tr>
                        <?php
                        $id++;
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div data-clickadilla-banner="376322"></div>
    </div>
</div>

</body>
</html>