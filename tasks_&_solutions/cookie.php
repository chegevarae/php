<?php

// Ставим COOKIE и получаем пользователя из БД
session_start();
include('../config.php');
$domen = $_SERVER['SERVER_NAME'];

if(isset($_COOKIE['token'])) $token = $_COOKIE['token'];
else {
	if(isset($_GET['page'])) {
		$token = $_GET['page'];
		setcookie('token', $token, time() + (31536000), '/','mydomen.ru');
	}
}

if(isset($token) && $_SESSION['token'] == "") {
	$_SESSION['token'] = $token;
	if($domen === 'mydomen.ru') {
		$mysqli = new mysqli($db_server, $db_user, $db_pass, $db_database);
		if ($mysqli->connect_errno) {
			echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		else {
			if ($result = $mysqli->query("SELECT first_name, email, phone FROM users WHERE status = 'on' AND code = '$token'")){ 
				$row = $result->fetch_assoc();
				$_SESSION['first_name'] = $row[first_name];
				$_SESSION['email'] = $row[email];
				$_SESSION['phone'] = $row[phone];
				$result->close(); // Освобождаем память
				// print_r($_SESSION);
				}
			else { echo "Не удалось выполнить команду: (" . $mysqli->errno . ") " . $mysqli->error; }
		}
		mysqli_close($mysqli);
	}
	header('Location: /');
}
?>