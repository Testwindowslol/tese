<?php

include("header.php");

try {
    $odb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT id FROM users WHERE id NOT IN (SELECT user_id FROM user_balance)";
    $stmt = $odb->query($query);

    $insertQuery = "INSERT INTO user_balance (user_id, credits) VALUES (?, 0)";

    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $user_id = $user['id'];

        $insertStmt = $odb->prepare($insertQuery);
        $insertStmt->execute([$user_id]);
    }

    $sql = "SELECT user_id, amount FROM payments WHERE status = 'Accepted'";
    $stmt = $odb->query($sql);

    $odb->beginTransaction();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $user_id = $row["user_id"];
        $amount = $row["amount"];

        $update_sql = "UPDATE user_balance SET credits = credits + :amount WHERE user_id = :user_id";
        $updateStmt = $odb->prepare($update_sql);
        $updateStmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $updateStmt->execute();

        $delete_sql = "DELETE FROM payments WHERE user_id = :user_id AND status = 'Accepted'";
        $deleteStmt = $odb->prepare($delete_sql);
        $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $deleteStmt->execute();
    }

    $odb->commit();

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>