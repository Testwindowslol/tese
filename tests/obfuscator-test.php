<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . "/JSObfuscator.php";

header("Content-Type: text/javascript");

$js = file_get_contents(__DIR__ . "/main.js");

$jsObfuscator = new JSObfuscator($js);

$jsObfuscator->addCustomSymbol("_a", "null");
$jsObfuscator->addCustomSymbol("_b", "new XMLHttpRequest()");
$jsObfuscator->addCustomSymbol("_c", "\"ajax/hub.php\"");
$jsObfuscator->addCustomSymbol("_d", "$");
$jsObfuscator->addCustomSymbol("_e", "eval");
$jsObfuscator->addCustomSymbol("_f", "setTimeout");

$jsObfuscator->replaceSymbol("xmlhttp", "_._c._a");
$jsObfuscator->replaceSymbol("var _._c._a;", "");
$jsObfuscator->replaceSymbol("new XMLHttpRequest()", "_._c._b");
$jsObfuscator->replaceSymbol("\"ajax/hub.php", "_._c._c + \"");
$jsObfuscator->replaceSymbol("$", "_._c._d");
$jsObfuscator->replaceSymbol("eval", "_._c._e");
$jsObfuscator->replaceSymbol("setTimeout", "_._c._f");

echo $jsObfuscator->obfuscate();