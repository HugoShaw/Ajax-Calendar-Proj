<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

//deals with SQL statement to add new category to DB
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

$name = $json_obj['name'];
$token = $json_obj['token'];
$user_id = $_SESSION['user_id'];

if(!isset($user_id)) {
    die("Request forgery detected");
}

if (!isset($name) || !isset($token) || empty($name)) {
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
if (strlen($name) > 50) {
    echo json_encode(array(
        "success" => false,
        "message" => "Category name is too long."
    ));
    exit;
}
//make sure the category has not already been added
$stmt = $mysqli->prepare("select COUNT(*) from categories where user_id=? and category=?");
if(!$stmt){
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: {$mysqli->error}"
    ));
    exit;
}

//bind params 
$stmt->bind_param('is', $user_id, $name);
$stmt->execute();

$stmt->bind_result($cnt);
$stmt->fetch();
$stmt->close();

if ($cnt==0) {
    $stmt = $mysqli->prepare("insert into categories (user_id, category, color) values (?, ?, ?)");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Query Prep Failed: {$mysqli->error}"
        ));
        exit;
    }

    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
    
    $color = rand_color();

    //bind params 
    $stmt->bind_param('iss', $user_id, $name, $color);
    $stmt->execute();
    $stmt->close();

    echo json_encode(array(
        "success" => true
    ));
    exit;
} 
echo json_encode(array(
    "success" => false,
    "message" => "Category already exists."
));
exit;



?>