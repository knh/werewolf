<?php
require_once('config.php');
if(!defined("CONFIG_LOADED") || CONFIG_LOADED != 'yes')
	die("Incorrect server configuration. Died.");
	
if(defined("DEBUG")){
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
}

global $mysqli;
$mysqli=new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($mysqli->connect_errno) {
	printf("Connect failed: %s\n", $mysqli->connect_error);
	exit();
}

// Utf-8 header
header('Content-type: text/html; charset=utf-8'); 