<?php 
/*
ob_start();
 #Session Start
 session_start();

 # Selects the database

 
  if($_SERVER['HTTP_HOST']=="localhost"){
 
	 DEFINE ('DB_USER', 'root');
	 DEFINE ('DB_PASSWORD', '');
	 DEFINE ('DB_HOST', 'localhost');
	 DEFINE ('DB_NAME', 'apitv');
 
  }else
  {
 	 DEFINE ('DB_USER', 'apitvusr');
 	 DEFINE ('DB_PASSWORD', '***4dm1n***');
 	 DEFINE ('DB_HOST', 'localhost');
 	 DEFINE ('DB_NAME', 'apitv'); 
 }

 $mysqli = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to MySQL');
 @mysql_select_db (DB_NAME) OR die ('Could not select the database');
*/

$connect = new PDO('mysql:host=localhost;dbname=apitvserver;charset=utf8mb4', 'root', '');
//$connect = new PDO('mysql:host=localhost;dbname=apitv;charset=utf8mb4', 'root', 'grips29.gT');

?>
