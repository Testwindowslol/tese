<?php

include_once __DIR__ . "/../../v2/php/config.php";
include_once __DIR__ . "/../../v2/php/api_access.php";

global $db;

// Config

const NUMBER_OF_DAYS = 60;
const REFRESH_RATE = 5 * 60; // Refresh cache every X seconds

// End config

header("Content-Type: application/json");

// Check API key
if (!isset($_GET['api_key'])) {
    http_response_code(400);
    echo json_encode([
        "error" => "invalid_request",
        "error_message" => "Invalid request !"
    ]);
    exit();
}

$apiKey = $_GET['api_key'];

// Check fields
if (empty($apiKey)) {
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

$apiKeyData = \APIAccess\getApiKeyData($apiKey);
$userId = $apiKeyData["user_id"];

// Data API is only available locally
if ($userId != 0) {
    http_response_code(403);
    echo json_encode([
       "error" => "invalid_api_access_level",
       "error_message" => "Invalid API key access level !"
    ]);
    exit();
}

$stmt = $db->prepare("SELECT `name` FROM `methods`");
$stmt->execute();
$methods = $stmt->fetchAll(PDO::FETCH_COLUMN);

$now = time();

$date = getdate($now);
$timestamp = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
$date = getdate($timestamp);

$from = $timestamp - 86400 * (NUMBER_OF_DAYS - 1);
$to = $timestamp + 86400; // Include today in stats

$days = [];

$data = [];

$currentTimestamp = $from;
for ($i = 0; $i < NUMBER_OF_DAYS; $i++) {
    $attacks = [];

    $attacksFrom = $currentTimestamp;
    $attacksTo = $currentTimestamp + 86399; // almost 1 day, 86400 = 1 day

    $today = ($now >= $attacksFrom && $now <= $attacksTo);

    $attackDate = getdate($attacksFrom);

    $day = $attackDate["weekday"] . " " . $attackDate["mday"] . "/" . $attackDate["mon"];
    $dayDate = $attackDate["mday"] . "/" . $attackDate["mon"] . "/" . $attackDate["year"];

    $days[] = $day;

    // Check if cached
    if ($today) {
        // Partial day cache
        $part = round(($now - $from) / REFRESH_RATE); // Refresh cache every REFRESH_RATE seconds

        // Keep Filename V1 and V2, very useful to simply rename files
        $cacheFilename = __DIR__ . "/cache/data/" . sha1($dayDate . "/" . $part) . ".temp";
        $cacheFilenameV2 = __DIR__ . "/cache/data/" . sha1($dayDate . "/" . $part) . ".temp";
    } else {
        // Day cache
        // Keep Filename V1 and V2, very useful to simply rename files
        $cacheFilename = __DIR__ . "/cache/data/" . sha1($dayDate) . ".cache";
        $cacheFilenameV2 = __DIR__ . "/cache/data/" . sha1($dayDate);
    }

    if (file_exists($cacheFilename)) {
        // Read data from cache

        $attackData = json_decode(file_get_contents($cacheFilename));

        rename($cacheFilename, $cacheFilenameV2);

        $data[] = $attackData;
    } else if (file_exists($cacheFilenameV2)) {
        // Read data from cache V2

        $attackData = json_decode(file_get_contents($cacheFilenameV2));

        $data[] = $attackData;
    } else {
        // Fetch data from DB

        foreach ($methods as $method) {
            $stmt = $db->prepare("SELECT COUNT(*) AS total FROM `logs` WHERE `method` = ? AND `date` >= ? AND `date` <= ?");
            $stmt->execute([
                $method,
                $attacksFrom,
                $attacksTo
            ]);
            $attacks[$method] = intval($stmt->fetchColumn());
        }

        $attackData = [
            "today" => $today,
            "from" => $attacksFrom,
            "to" => $attacksTo, // 1 day
            "weekday" => $day,
            "attacks" => $attacks
        ];

        // Is a temp file ?
        if ($today) {
            // Delete old .temp files
            // Before writing new one to cache
            $path = __DIR__ . "/cache/data/*.temp";
            array_map('unlink', glob($path));
        }

        // Write to cache
        file_put_contents($cacheFilenameV2, json_encode($attackData));

        $data[] = $attackData;
    }

    $currentTimestamp += 86400;
}

$db->prepare("SELECT * FROM logs");

echo json_encode([
    "now" => time(),
    "from" => $from,
    "to" => $to,
    "methods" => $methods,
    "days" => $days,
    "data" => $data
]);