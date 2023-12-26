<?php

if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}

require '@/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "UPDATE recent_attacks SET count = count + 1";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Invalid request";
}
?>