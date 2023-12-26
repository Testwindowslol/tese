<?php
session_start();
require_once("@/config.php");
require_once("@/init.php");

if (isset($_POST['payButton'])) {
    $id = (int)$_POST['payButton'];
    $row = $odb->query("SELECT * FROM `plans` WHERE `ID` = '$id'")->fetch();
    $user_id = $_SESSION['ID'];
    $price = $row['price'];

    $selectBalanceQuery = "SELECT credits FROM user_balance WHERE user_id = :user_id";
    $stmt = $odb->prepare($selectBalanceQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $balance = $stmt->fetchColumn();

    if ($balance >= $price) {

        $expireInSeconds = time() + (30 * 24 * 60 * 60);

        $updateBalanceQuery = "UPDATE user_balance SET credits = credits - :price WHERE user_id = :user_id";
        $stmt = $odb->prepare($updateBalanceQuery);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $updateMembershipQuery = "UPDATE users SET membership = :membership, expire = :expire WHERE ID = :user_id";
        $stmt = $odb->prepare($updateMembershipQuery);
        $stmt->bindParam(':membership', $id, PDO::PARAM_INT);
        $stmt->bindParam(':expire', $expireInSeconds, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: confirmation.php?id=" . $id);
    } else {
        echo '<div class="alert alert-danger">You do not have enough credits in your balance to make this payment.</div>';
    }
} else {
    header("Location: index.php");
}
?>