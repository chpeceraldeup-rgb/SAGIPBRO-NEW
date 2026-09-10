<?php

require_once '../../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Logout must use POST.');
}
verifyCsrf();

$_SESSION = [];
session_destroy();
header('Location: ../../login.php');
exit;
