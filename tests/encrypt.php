<?php

require __DIR__ . "/../v2/php/config.php";
require __DIR__ . "/../v2/php/crypto.php";

global $db;

//echo urlencode(encrypt("Hello World !", "hi"));
//echo strlen(encrypt("Hi !", "a"));

//echo "<hr>";

//echo decrypt($_GET['data'], "hi");

//echo getStaticKey("Hello World !");

$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];

echo getStaticKey("ip_logs");

echo "<hr>";

echo $ip;

echo "<hr>";

echo encrypt($ip, getStaticKey("ip_logs"));

echo "<hr>";

$userId = $_GET['user_id'];

$stmt = $db->prepare("SELECT * FROM ip_logs WHERE user_id=?");
$stmt->execute([
   $userId
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$ip = $data['ip'];

echo decrypt($ip, getStaticKey("ip_logs"));