<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include("@/header.php");

$paginaname = 'Tickets Creator';

function getDiscussionURL($ticketID) {
    return "TicketDetails.php?ticketID=" . urlencode($ticketID);
}

if (isset($_POST['submit'])) {
    $subject = $_POST['subject'];
    $problem = $_POST['problem'];
    $userID = $_SESSION['ID'];
    $submissionDate = date('Y-m-d H:i:s');

    try {
        $pdo = $odb;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO ticket (userID, subject, problem, submission_date) VALUES (:userID, :subject, :problem, :submission_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'userID' => $userID,
            'subject' => $subject,
            'problem' => $problem,
            'submission_date' => $submissionDate,
        ]);

        $successMessage = "Ticket created successfully.";
    } catch (PDOException $e) {
        $errorMessage = "Error creating ticket: " . $e->getMessage();
    }
}

function getTickets($pdo) {
    $userID = $_SESSION['ID'];

    try {
        $sql = "SELECT * FROM ticket WHERE userID = :userID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userID' => $userID]);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $tickets;
    } catch (PDOException $e) {
        die("Error fetching tickets: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <script src="../assets/js/spinner.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="page-wrapper">
    <div class="page-content">
        <div class="alert alert-fill-primary">
            <span data-feather="archive" class="icon-md text-light mr-2"></span>
            <span><?php echo htmlspecialchars($paginaname); ?></span>
        </div>
        <form method="post">
            <div class="form-group">
                <label class="col-md-3 control-label">Subject:</label>
                <div class="col-md-12">
                    <input type="text" name="subject" placeholder="Enter the subject" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Describe your problem:</label>
                <div class="col-md-12">
                    <textarea name="problem" placeholder="Type your problem here." class="form-control"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <button type="submit" name="submit" class="btn btn-primary">Submit Ticket</button>
                </div>
            </div>
        </form>

        <div class="ticket-list">
            <h3>Tickets List:</h3>
            <?php
            $tickets = getTickets($odb);
            if (!empty($tickets)) {
                foreach ($tickets as $ticket) {
                    $ticketID = $ticket['id'];
                    $discussionURL = getDiscussionURL($ticketID);

                    echo "<div class='ticket'>";
                    echo "<br>";
                    echo "<h5><a href='$discussionURL'>Subject: " . htmlspecialchars($ticket['subject']) . "</a></h5>";
                    echo "<p>Description: " . htmlspecialchars($ticket['problem']) . "</p>";

                    if (isset($ticket['submission_date'])) {
                        echo "<p>Date: " . htmlspecialchars($ticket['submission_date']) . "</p>";
                    } else {
                        echo "<p>Date: N/A</p>";
                    }

                    echo "<p>Status: " . htmlspecialchars($ticket['status']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No tickets found.</p>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>