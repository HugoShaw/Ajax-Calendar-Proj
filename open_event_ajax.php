<?php
//deals with SQL command to query information for a single event 
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

$stmt = $mysqli->prepare("select category_id, event_title, event_date, event_time, event_description from events where user_id=? and event_id=?");
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

//bind result
$stmt->bind_result($cat_id, $title, $date, $time, $description);
$stmt->fetch();
$stmt->close();

$data = array(
    "category_id" => $cat_id,
    "title" => $title,
    "date" => $date,
    "time" => $time,
    "description" => $description
);

echo json_encode(array(
    "success" => true,
    "events" => $data
));
exit;



?>