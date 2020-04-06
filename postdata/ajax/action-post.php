<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "../config/define.php";
	include_once "../include/db.php";
	
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$session_id = session_id();
	$user_id = $_SESSION['auth']['user_id'];
	$dest_id = $_POST['to'];
	$message = $_POST['msg'];
	
	$data = array(
		"#id" => "DEFAULT",
		"code" => $_POST['code'],
		"#humidity" => $_POST['hum'],
		"#temp" => $_POST['tmp'],
		"#created" => "NOW()"
	);
	$dbc->Insert("farmedges_records",$data);
	$result = array(
		"success" => true,
		"server_time" => time()
	);
	
	echo json_encode($result);
	
	$dbc->Close();
?>

