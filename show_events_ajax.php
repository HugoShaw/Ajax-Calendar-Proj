<?php
//deals with SQL statements for pulling all events for a single day 
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

$date = $json_obj['date'];
$token = $json_obj['token'];
$user_id = $_SESSION['user_id'];
$data = array();

if(!isset($user_id)) {
    die("Request forgery detected");
}

if(!isset($date) || !isset($token)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Values could not be accessed."
    ));
    exit;
}

if(isset($_SESSION['token']) && !hash_equals($_SESSION['token'], $token)){
    die("Request forgery detected");
}

// $stmt = $mysqli->prepare("select friends from users where user_id=?");
// if(!$stmt){
//     echo json_encode(array(
//         "success" => false,
//         "message" => "Query Prep Failed: {$mysqli->error}"
//     ));
//     exit;
// }

// //bind params 
// $stmt->bind_param('i', $user_id);
// $stmt->execute();

// $stmt->bind_result($friends);
// $stmt->fetch();
// $stmt->close();



// $friendsSQL = " ";
// $friendArray = explode(',', $friends);
// foreach ($friendArray as &$value) {
//     if ($value != "") {
//         $friendsSQL = $friendsSQL . "or user_id=" .$value . " ";
//     }
    
// } 

// $statementQuery = "select category_id, event_id, event_title, event_date, event_time, event_description from events where user_id=? " .$friendsSQL . "and event_date=? order by event_date, event_time";

$stmt = $mysqli->prepare("select category_id, event_id, event_title, event_date, event_time, event_description from events where user_id=? and event_date=? order by event_date, event_time");
if(!$stmt){
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: {$mysqli->error}"
    ));
    exit;
}

//bind params 
$stmt->bind_param('is', $user_id, $date);
$stmt->execute();


$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($data, array(
                "category_id" => $row['category_id'],
                "event_title" => $row['event_title'],
                "event_description" => $row['event_description'],
                "event_date" => $row['event_date'],
                "event_time" => $row['event_time'],
                "event_id" => $row['event_id']
            )  
        );
    }
}
echo json_encode(array(
    "success" => true,
    "events" => $data
));
exit;



?>