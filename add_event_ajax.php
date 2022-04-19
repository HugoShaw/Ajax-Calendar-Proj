<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

//deals with SQL statement to add event to DB
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

$title = $json_obj['title'];
$date = $json_obj['date'];
$time = $json_obj['time'];
$category = $json_obj['category'];
$description = $json_obj['description'];
$token = $json_obj['token'];
$user_id = $_SESSION['user_id'];

if(!isset($user_id)) {
    die("Request forgery detected");
}

if (!isset($title) || !isset($date) || !isset($time) || !isset($category) || !isset($token) || empty($title) || empty($date) || empty($time) || empty($category)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Required values could not be accessed. Make sure all required fields are filled."
    ));
    exit;
}

if(isset($_SESSION['token']) && !hash_equals($_SESSION['token'], $token)){
    die("Request forgery detected");
}

//check event_title length
if (strlen($title) > 250) {
    echo json_encode(array(
        "success" => false,
        "message" => "Event title is too long."
    ));
    exit;
}

//properly format date
$formatted_date = date('Y-m-d', strtotime($date));

//properly format time
$formatted_time = date('H:i:s', strtotime($time));

$formatted_category = (int) substr($category,3);
 
$stmt = $mysqli->prepare("insert into events (user_id, category_id, event_title, event_date, event_time, event_description) values (?, ?, ?, ?, ?, ?)");
if(!$stmt){
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: {$mysqli->error}"
    ));
    exit;
}

//bind params 
$stmt->bind_param('iissss', $user_id, $formatted_category, $title, $formatted_date, $formatted_time, $description);
$stmt->execute();
$stmt->close();

echo json_encode(array(
    "success" => true
));
exit;



?>