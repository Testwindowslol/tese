<?php

include_once __DIR__ . "/../php/api_access.php";

header("Content-Type: application/json");

session_start();

if (!isset($_SESSION['username'], $_SESSION['ID'])) {
    http_response_code(400);
    echo json_encode([
        "error" => "user_disconnected_error",
        "error_message" => "User is disconnected."
    ]);
    exit();
}

$userId = $_SESSION['ID'];

$hasAPIAccess = \APIAccess\hasApiAccess($userId);

if (!$hasAPIAccess) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_access",
        "error_message" => "User has not enough privileges to have access to this feature."
    ]);
    exit();
}

if (isset($_GET['regenerate'])) {
    // Generate a new api key

    $oldApiKey = \APIAccess\getApiKey($userId);
    $newApiKey = \APIAccess\generateApiKey($userId);

    echo json_encode([
        "old_api_key" => $oldApiKey,
        "new_api_key" => $newApiKey
    ]);
    exit();
}

// Show api key

$apiKey = \APIAccess\getApiKey($userId);

echo json_encode([
    "api_key" => $apiKey
]);
exit();

