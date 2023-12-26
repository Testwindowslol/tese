<?php
function generateRandomString($length = 24) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

const PASSWORD = "MTX57ers59RHkkvM3rXJfvXW";
const TIMESTAMP = 1685829533;

session_start();

// Auto auth
if (isset($_GET['auto_auth_key'])) {
	$autoAuthPassword = $_GET['auto_auth_key'];

	if ($autoAuthPassword == PASSWORD) {
		$_SESSION['panel_auth_key'] = TIMESTAMP;
		header("Location: index.php");
		exit();
	}
}

if (isset($_GET['request_token_v1'])) {
	header("Content-Type: application/json");

	$_SESSION['request_token_v1'] = generateRandomString();

	echo $_SESSION['request_token_v1'];

	exit();
}

// Check if the user want to authenticate
if (isset($_POST['password'])) {
	$password = $_POST['password'];

	if (!isset($_POST['request_token_v1'], $_SESSION['request_token_v1'], $_SESSION['request_token_v2'])) {
		echo "Invalid request. Error code : " . __LINE__;
		echo "<br>";
		echo "<a href='auth.php'>Refresh</a>";
		exit();
	}

	// Security, anti "Script Kiddies" bots
	if ($_POST['request_token_v1'] != $_SESSION['request_token_v1']) {
		echo "Invalid request. Error code : " . __LINE__;
		echo "<br>";
		echo "<a href='auth.php'>Refresh</a>";
		exit();
	}

	// Security, anti "Script Kiddies" bots
	if ($_COOKIE['request_token_v2'] != $_SESSION['request_token_v2']) {
		echo "Invalid request. Error code : " . __LINE__;
		echo "<br>";
		echo "<a href='auth.php'>Refresh</a>";
		exit();
	}

	if ($password != PASSWORD) {
		echo "Invalid credentials.";
		echo "<br>";
		echo "<a href='auth.php'>Refresh</a>";
		exit();
	}

	$_SESSION['panel_auth_key'] = TIMESTAMP; // Unix timestamp, change it if you suspect someone to be logged in with an exploit
	header("Location: index.php");
	exit();
} else {
	// or set the cookie for security
	$_SESSION['request_token_v2'] = generateRandomString();
	setcookie('request_token_v2', $_SESSION['request_token_v2'], time() + 600); // 10 min time limit
}
?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Panel - Auth</title>
</head>
<body>
	<form method="POST">
		<input type="hidden" name="request_token_v1">
		<input type="password" name="password" placeholder="Password">
		<input type="submit" id="submit" value="Submit" disabled>
	</form>

	<script type="text/javascript">
		let xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (this.readyState === this.DONE && this.status === 200) {
				document.getElementsByName("request_token_v1")[0].value = this.responseText;
				document.getElementById("submit").disabled = false;
			}  
		};
		xhr.open("GET", "https://hexstresser.org/panel/auth.php?request_token_v1");
		xhr.send();
	</script>
</body>
</html>