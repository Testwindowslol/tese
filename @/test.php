<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'dev.php';

if (!($user->LoggedIn()) || !($user->notBanned($odb))) {
    die();
}

$attackLogger = new \DevTools\AttackLogger();
$attackData = new \DevTools\AttackData();

$attackData->setUser($user);
$attackData->setUsername("Hello World !");
$attackData->setHost("1.1.1.1");
$attackData->setPort(80);
$attackData->setBootTime(40);
$attackData->setMethod("Hello !");
$attackData->setType("Hey !");

$attackLogger->record($attackData);