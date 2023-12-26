<?php

include_once __DIR__ . "/php/core.php";
global $db;

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == "find") {
        if (!isset($_GET['user_id'])) {
            header("Location: ip_logs.php");
            exit();
        }

        $userId = $_GET['user_id'];

        $stmt = $db->prepare("SELECT * FROM ip_logs WHERE user_id=?");
        $stmt->execute([
           $userId
        ]);

        if ($stmt->rowCount() == 0) {
            echo "User not found !";
        } else {
            ?>
            <table>
                <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        User ID
                    </th>
                    <th>
                        IP
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $data['id']; ?>
                        </td>
                        <td>
                            <?php echo $data['user_id']; ?>
                        </td>
                        <td>
                            <?php echo decrypt($data['ip'], getStaticKey("ip_logs")); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php
        }

        echo "<hr>";
    } else if ($action == "show_all") {
        $stmt = $db->prepare("SELECT * FROM ip_logs");
        $stmt->execute();

        ?>
        <table>
            <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    User ID
                </th>
                <th>
                    IP
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td>
                        <?php echo $data['id']; ?>
                    </td>
                    <td>
                        <?php echo $data['user_id']; ?>
                    </td>
                    <td>
                        <?php echo decrypt($data['ip'], getStaticKey("ip_logs")); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        echo "<hr>";
    }
}

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel - IP Logs</title>

    <style rel="stylesheet" href="css/ip_logs.css"></style>
</head>
<body>
    <form method="get" action="ip_logs.php">
        <input type="hidden" name="action" value="find">
        <label for="user-id">User ID :</label>
        <input type="number" name="user_id" id="user-id">
        <input type="submit" value="Find">
    </form>
    <hr>
    <form method="get" action="ip_logs.php">
        <input type="hidden" name="action" value="show_all">
        <input type="submit" value="Show all records">
    </form>
</body>
</html>
