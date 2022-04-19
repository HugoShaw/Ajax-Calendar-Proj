<?php
//deals with SQL commands to pull all the categories from the DB
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

$token = $json_obj['token'];
$user_id = $_SESSION['user_id'];
$data = array();

if(!isset($user_id)) {
    die("Request forgery detected");
}

if(isset($_SESSION['token']) && !hash_equals($_SESSION['token'], $token)){
    die("Request forgery detected");
}

$stmt = $mysqli->prepare("select category_id, category, color from categories where user_id=?");
if(!$stmt){
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: {$mysqli->error}"
    ));
    exit;
}

//bind params 
$stmt->bind_param('i', $user_id);
$stmt->execute();


$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($data, array(
                "category_id" => $row['category_id'],
                "category" => $row['category'],
                "color" => $row['color']
            )  
        );
    }
}
echo json_encode(array(
    "success" => true,
    "categories" => $data
));
exit;



?>