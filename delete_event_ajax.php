<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

//deals with SQL command to delete events 
ini_set("session.cookie_httponly", 1);
session_start();
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
} else{
	$_SESSION['useragent'] = $current_ua;
}

require 'database.php';

header("Content-Type: application/json");

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

if(!isset($json_obj)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Values could not be accessed."
    ));
    exit;
}

$event_id = $json_obj['event_id'];
$token = $json_obj['token'];
$user_id = $_SESSION['user_id'];


if(!isset($user_id)) {
    die("Request forgery detected");
}

if(!isset($event_id) || !isset($token)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Values could not be accessed."
    ));
    exit;
}

if(isset($_SESSION['token']) && !hash_equals($_SESSION['token'], $token)){
    die("Request forgery detected");
}

$stmt = $mysqli->prepare("delete from events where user_id=? and event_id=?");
if(!$stmt){
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: {$mysqli->error}"
    ));
    exit;
}

//bind params 
$stmt->bind_param('ii', $user_id, $event_id);
$stmt->execute();

$stmt->close();

echo json_encode(array(
    "success" => true
));
exit;



?>