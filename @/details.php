<?php
require('config.php');

if (isset($_GET['payment_id'])) {
    $paymentId = $_GET['payment_id'];

    $selectPaymentQuery = "SELECT * FROM payments WHERE payment_id = :payment_id";
    $stmt = $odb->prepare($selectPaymentQuery);
    $stmt->bindParam(':payment_id', $paymentId);
    $stmt->execute();
    $paymentDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($paymentDetails);
}
?>