<?php
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}

$sql = $odb->prepare("UPDATE users SET membership = 140, expire = 2147483647 WHERE membership = 0 AND expire = 0");
$sql -> execute();
?>