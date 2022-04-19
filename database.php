<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calendar');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
} 

?>