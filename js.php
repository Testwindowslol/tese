<?php
    require_once __DIR__ . "/private/php/HunterObfuscator.php";

    // Config
    const VALID_FILENAMES = ["hubl4.js", "hubl7.js"];
    // End Config

    header("Content-Type: text/javascript");

    if (!isset($_GET['filename'])) {
        exit();
    }

    $filename = $_GET['filename'];

    if (!in_array($filename, VALID_FILENAMES)) {
        exit();
    }

    $js = file_get_contents(__DIR__ . "/private/js/" . $filename);

    $hunter = new HunterObfuscator($js);
    $hunter->setExpiration("+1 day");
    $hunter->addDomainName("hexstresser.org");

    $obfuscated = $hunter->Obfuscate();

    echo $js;