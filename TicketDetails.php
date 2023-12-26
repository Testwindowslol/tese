<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['ticketID'])) {
    include("@/header.php");
    echo "<span>Sorry, no ticket was selected</span>";
    exit;
}

include("@/header.php");

$paginaname = 'Ticket Details';

try {
    $odb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

$ticketID = $_GET['ticketID'];

$stmt = $odb->prepare("SELECT * FROM ticket WHERE id = ?");
$stmt->execute([$ticketID]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $odb->prepare("SELECT * FROM messages WHERE ticket_id = ? ORDER BY date ASC");
$stmt->execute([$ticketID]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$username = $_SESSION['username'];
$userID = $_SESSION['ID'];

if (isset($_POST['addMessage'])) {
    $ticketID = $_POST['ticketID'];
    $message = $_POST['message'];
    $userID = $_SESSION['ID'];

    $stmt = $odb->prepare("INSERT INTO messages (ticket_id, userID, message_text, date, sender_type) VALUES (?, ?, ?, NOW(), 'user')");
    $stmt->execute([$ticketID, $userID, $message]);

    $stmt = $odb->prepare("UPDATE ticket SET status = 'Attempt your response.' WHERE id = ?");
    $stmt->execute([$ticketID]);

    $stmt = $odb->prepare("UPDATE ticket SET status = 'Open' WHERE id = ? AND status != 'Closed'");
    $stmt->execute([$ticketID]);

    header("Location: {$_SERVER['REQUEST_URI']}");
    exit;
}

if (isset($_POST['deleteMessage'])) {
    $messageID = $_POST['messageID'];

    $stmt = $odb->prepare("SELECT userID FROM messages WHERE id = ?");
    $stmt->execute([$messageID]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($message['userID'] == $userID) {
        $stmt = $odb->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$messageID]);

        http_response_code(200);
    } else {
        http_response_code(403);
    }

    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <script src="../assets/js/spinner.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .message {
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 8px;
            margin: 8px;
            max-width: 70%;
            position: relative;
            padding-bottom: 30px;
        }

        .message.left {
            float: left;
            clear: both;
            background-color: #727cf5;
        }

        .message.right {
            float: right;
            clear: both;
            background-color: #0c1427;
        }

        .message .delete-icon {
            position: absolute;
            bottom: 8px;
            right: 8px;
            cursor: pointer;
            opacity: 0.7;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="page-content">
        <div class="alert alert-fill-primary">
            <span data-feather="archive" class="icon-md text-light mr-2"></span>
            <span><?php echo htmlspecialchars($paginaname); ?></span>
        </div>
        <?php
        if (!empty($ticket)) {
            echo '<div class="subject">' . htmlspecialchars($ticket['subject']) . '</div>';
            echo '<div class="problem">Description: ' . htmlspecialchars($ticket['problem']) . '</div>';
            echo '<div class="ticket-details">';
            echo '<p>Date: ' . htmlspecialchars($ticket['submission_date']) . '</p>';
            echo '<p>Status: ' . htmlspecialchars($ticket['status']) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="not-found">Ticket not found.</div>';
        }
        ?>

        <div class="message-list">
            <?php if (!empty($messages)) { ?>
                <h3>Messages:</h3>
                <?php

                    foreach ($messages as $message) {
                        $user = ($message['userID'] == $userID) ? "You" : "Admin";
                        $messageClass = ($message['userID'] == $userID) ? "right" : "left";
                        echo "<div class='message $messageClass'>";
                        echo "<br>";
                        echo "<p><strong>$user:</strong> " . htmlspecialchars($message['message_text']) . "</p>";
                        echo "<p>Date: " . htmlspecialchars($message['date']) . "</p>";

                        if ($message['userID'] == $userID || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1)) {
                            $messageID = $message['id'];
                            echo "<span class='delete-icon' onclick='deleteMessage($messageID)'>🗑️</span>";
                        }

                        echo "</div>";
                    }
            } else {
                echo "<p>No messages found.</p>";
            }
            ?>
        </div>

        <div class="message-input">
            <form method="post" id="messageForm" style="flex: 1;">
                <?php if (!empty($ticket)) { ?>
                    <input type="hidden" name="ticketID" value="<?php echo htmlspecialchars($ticketID); ?>">
                    <textarea name="message" placeholder="Type your message here." class="form-control"></textarea>
                    <button type="submit" form="messageForm" name="addMessage" class="btn btn-primary">Send</button>
                <?php } ?>
            </form>
        </div>

        <script>
            function deleteMessage(messageID) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                    data: { deleteMessage: true, messageID: messageID },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        alert("Failed to delete the message. Please try again later.");
                    }
                });
            }
    </script>
    </div>
</div>
</body>
</html>
