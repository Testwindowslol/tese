<?php

include_once __DIR__ . "/../../v2/php/config.php";
include_once __DIR__ . "/../../v2/php/crypto.php";
global $db;

session_start();

if (!isset($_SESSION['panel_auth_key'])) {
    if (isset($_GET['auto_auth'])) {
        header("Location: auth.php?auto_auth_key=" . $_GET['auto_auth']);
        exit();
    }
    header("Location: auth.php");
    exit();
}

$authKey = $_SESSION['panel_auth_key'];
if ($authKey > time()) {
    unset($_SESSION['panel_auth_key']);
    header("Location: auth.php");
    exit();
}

if ($authKey != 1685829533) {
    unset($_SESSION['panel_auth_key']);
    header("Location: auth.php");
    exit();
}