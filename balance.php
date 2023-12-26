<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('@/header.php');
include('@/balance.php');

$paginaname = 'Balance';

if (!isset($_SESSION['ID'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['ID'];

try {
    $selectBalanceQuery = "SELECT credits FROM user_balance WHERE user_id = :user_id";
    $stmt = $odb->prepare($selectBalanceQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $balance = $stmt->fetchColumn();

    $balanceToAdd = 0;
    $errorMessage = "";
    $successMessage = "";

    $selectPaymentsQuery = "SELECT payment_id, amount, status FROM payments WHERE user_id = :user_id";
    $stmt = $odb->prepare($selectPaymentsQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $payments = $stmt->fetchAll();

    if (isset($_POST['add_balance'])) {
        $balanceToAdd = str_replace(',', '.', $_POST['balance']);
        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $balanceToAdd)) {
            $balanceToAdd = floatval($balanceToAdd);

            $payment_id = uniqid();
            $expiration_date = date('Y-m-d H:i:s', strtotime('+7 days'));
            $status = 'Attempt';

            $insertQuery = "INSERT INTO payments (payment_id, user_id, amount, status, expiration_date) VALUES (:payment_id, :user_id, :balanceToAdd, :status, :expiration_date)";
            $stmt = $odb->prepare($insertQuery);
            $stmt->bindParam(':payment_id', $payment_id);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':balanceToAdd', $balanceToAdd, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':expiration_date', $expiration_date);
            $stmt->execute();

            $successMessage = "Payment added successfully.";
        } else {
            $errorMessage = "Invalid amount. Please enter a valid number with up to two decimal places.";
        }
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            text-align: center;
            background: #080808;
            margin-left: 850px;
            position: relative;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #0c0b0c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin-top: 150px;
            margin-left: 0;
        }

        .colored {
            color: #ac50f5;
        }

        .text-below {
            color: #ac50f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .text-below .small-text {
            font-size: 14px;
        }

        .text-below h2 {
            background-color: #0c0b0c;
            padding: 10px;
            border-radius: 5px;
        }

        #payment-buttons {
            background-color: #0c0b0c;
            padding: 10px;
            border-radius: 5px;
        }

        #payment-buttons a, #balance, #add-balance {
            pointer-events: auto;
        }

        #payment-buttons a {
            display: block;
            margin: 5px;
            padding: 10px 20px;
            background-color: #45007b;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 100%;
            text-align: center;
        }

        #payment-buttons a:hover {
            background-color: #ac50f5;
        }

        .error-td {
            color: red;
        }

        .hud-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            z-index: 999;
            filter: blur(5px);
        }

        .colored {
            color: #ac50f5;
        }

        .center-hud {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        .hud-box {
            background-color: #0c0b0c;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 300px;
        }

        .hud-box h3 {
            color: #ac50f5;
        }

        .close-hud {
            position: absolute;
            top: -3px;
            right: 5px;
            color: #ac50f5;
            cursor: pointer;
            font-size: 24px;
        }

        .payment-history {
            background: #1f1f1f;
            border-radius: 10px;
            padding: 10px;
            text-align: left;
            margin-top: 20px;
        }

        .payment-history h3 {
            color: #ac50f5;
        }

        .payment-history table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-history th,
        .payment-history td {
            border: 1px solid #ac50f5;
            padding: 8px;
            text-align: center;
        }

        .payment-history th {
            background-color: #0c0b0c;
        }

        .payment-button {
            display: block;
            margin: 5px;
            padding: 5px 10px;
            background-color: #45007b;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 100%;
            text-align: center;
            cursor: pointer;
        }

        .payment-button:hover {
            background-color: #ac50f5;
        }

        .center-text {
            text-align: center;
        }

        .crypto {
            font-size: 10px;
        }

        .clipboard-icon {
            cursor: pointer;
            margin-left: 10px;
            font-size: 16px;
        }
        </style>
</head>
<body>

<div class="container">
    <div class="header-bg">
        <h2><span class="colored">Add Balance</span></h2>
    </div>
    <div class="text-below">
        <h2><span class="small-text"><i class='fas fa-coins'></i> Your Balance:</span> <span style="font-size: 14px;">$<?php echo number_format($balance, 2); ?></span></h2>
    </div>
    <form method="post">
        <div class="form-group">
            <input type="number" name="balance" id="balance" placeholder="5.00" class="form-control" step="1" min="5" max="500" required>
            <small class="form-text text-muted">Add amount to your balance (between $5 and $500)</small>
        </div>
        <button type="submit" name="add_balance" class="btn btn-primary" id="add-balance">Pay Now</button>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-td"><?php echo $errorMessage; ?></div>
        <?php elseif (!empty($successMessage)): ?>
            <div class="success-td" id="copy-feedback"><?php echo $successMessage; ?></div>
        <?php endif; ?>
    </form>
    <div class="payment-history">
        <h3 class="center-text">Payment History</h3><br>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($payments as $payment): ?>
                <?php if ($payment['status'] == 'Attempt'): ?>
                    <tr>
                        <td><?php echo $payment['payment_id']; ?></td>
                        <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo $payment['status']; ?></td>
                        <td><a href="javascript:void(0);" class="payment-button retry-payment" data-payment-id="<?php echo $payment['payment_id']; ?>">Retry Payment</a></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="center-hud" id="hud-overlay" style="display: none;">
    <div class="hud-box">
        <span class="close-hud" id="close-hud">&times;</span>
        <h3>Amount: $<span id="hud-amount">0.00</span></h3>
        <p>User ID: <?php echo $user_id; ?></p>
        <a href="https://paypal.me/hexstress" class="payment-button paypal">
            <i class="fab fa-paypal"></i> PayPal
        </a>
        <a class="payment-button etherum">
            <i class="fab fa-ethereum"></i> Ethereum
        </a>
        <div id="ethereum-address" class="text-below" style="display: none;">
            <h2 class="crypto">Ethereum Address: 0x06DEE92DED429D6005bBbFa3E5Feb9De1675A277
                <i class='fa fa-clipboard clipboard-icon clipboard-container' data-clipboard-text="0x06DEE92DED429D6005bBbFa3E5Feb9De1675A277"></i>
                <span class="copy-button" style="cursor: pointer;">Copy</span>
            </h2>
        </div>
        <a class="payment-button bitcoin">
            <i class="fab fa-bitcoin"></i> Bitcoin
        </a>
        <div id="bitcoin-address" class="text-below" style="display: none;">
            <h2 class="crypto">Bitcoin Address: bc1qtmfp4fzcxx60yfkgwql02yyzvg6gfs6238kpfk
                <i class='fa fa-clipboard clipboard-icon clipboard-container' data-clipboard-text="bc1qtmfp4fzcxx60yfkgwql02yyzvg6gfs6238kpfk"></i>
                <span class="copy-button" style="cursor: pointer;">Copy</span>
            </h2>
        </div>
        <a href="https://t.me/HexstresserV2" class="payment-button other">
            <i class="fab fa-telegram-plane"></i> Other
        </a>
      </div>
    </div>
  </div>
</div>
<script>
    let isHUDOpen = false; // Variable pour suivre l'état de l'HUD

    window.addEventListener('load', function () {
        <?php if (!empty($successMessage)): ?>
            document.getElementById('hud-amount').innerText = <?php echo $balanceToAdd; ?>;
            document.getElementById('hud-overlay').style.display = 'block';
            document.body.classList.add('page-disabled');
            document.getElementById('balance').disabled = true;
            document.getElementById('add-balance').disabled = true;
            disableRetryPayments();
            const copyFeedback = document.getElementById('copy-feedback');
            if (copyFeedback) {
                copyFeedback.innerText = 'Copied!';
                setTimeout(function () {
                    copyFeedback.innerText = 'Payment added successfully.';
                }, 2000);
            }
            isHUDOpen = true;
        <?php endif; ?>
    });

    $('.etherum').click(function () {
        $('#ethereum-address').show();
        $('#bitcoin-address').hide();
    });

    $('.bitcoin').click(function () {
        $('#bitcoin-address').show();
        $('#ethereum-address').hide();
    });

    function disableRetryPayments() {
        document.querySelectorAll('.retry-payment').forEach(function(button) {
            button.disabled = true;
            button.removeEventListener('click', handleRetryPaymentClick);
        });
    }

    function enableRetryPayments() {
        document.querySelectorAll('.retry-payment').forEach(function(button) {
            button.disabled = false;
            button.addEventListener('click', handleRetryPaymentClick);
        });
    }

    function handleRetryPaymentClick() {
        const paymentId = this.getAttribute('data-payment-id');
        $.ajax({
            type: 'GET',
            url: '@/details.php',
            data: { payment_id: paymentId },
            success: function (data) {
                if (data) {
                    const paymentDetails = JSON.parse(data);
                    if (paymentDetails) {
                        document.getElementById('hud-amount').innerText = paymentDetails.amount;
                        document.getElementById('hud-overlay').style.display = 'block';
                        document.body.classList.add('page-disabled');
                        document.getElementById('balance').disabled = true;
                        document.getElementById('add-balance').disabled = true;
                        disableRetryPayments();
                    }
                }
            }
        });
    }

    document.querySelectorAll('.retry-payment').forEach(function(button) {
        button.addEventListener('click', handleRetryPaymentClick);
    });

    document.getElementById('close-hud').addEventListener('click', function () {
        document.getElementById('hud-overlay').style.display = 'none';
        document.body.classList.remove('page-disabled');
        document.getElementById('balance').disabled = false;
        document.getElementById('add-balance').disabled = false;
        enableRetryPayments();
        isHUDOpen = false;
    });

    document.querySelectorAll('.copy-button').forEach(function(button) {
        button.addEventListener('click', function() {
            const textToCopy = this.parentElement.querySelector('.clipboard-container').getAttribute('data-clipboard-text');

            const textArea = document.createElement("textarea");
            textArea.value = textToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            this.innerText = "Copied!";
            setTimeout(function () {
                this.innerText = "Copy";
            }.bind(this), 2000);
        });
    });

    document.getElementById('add-balance').addEventListener('click', function(event) {
        if (isHUDOpen) {
            event.preventDefault();
        }
    });

    document.querySelectorAll('.retry-payment').forEach(function(button) {
        button.addEventListener('click', function(event) {
            if (isHUDOpen) {
                event.preventDefault();
            }
        });
    });
</script>
</body>
</html>
