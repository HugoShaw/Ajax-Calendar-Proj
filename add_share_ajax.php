<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

// ini_set("session.cookie_httponly", 1);
// session_start();
// $previous_ua = @$_SESSION['useragent'];
// $current_ua = $_SERVER['HTTP_USER_AGENT'];

// if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
// 	die("Session hijack detected");
// } else{
// 	$_SESSION['useragent'] = $current_ua;
// }

// require 'database.php';

// header("Content-Type: application/json");

// $json_str = file_get_contents('php://input');
// $json_obj = json_decode($json_str, true);

// if(!isset($json_obj)) {
//     echo json_encode(array(
//         "success" => false,
//         "message" => "Values could not be accessed."
//     ));
//     exit;
// }

// $friend = $json_obj['friend'];
// $token = $json_obj['token'];
// $user_id = $_SESSION['user_id'];

// if(!isset($user_id)) {
//     die("Request forgery detected");
// }

// if (!isset($friend) || !isset($token) || empty($friend)) {
//     echo json_encode(array(
//         "success" => false,
//         "message" => "Required values could not be accessed. Make sure all required fields are filled."
//     ));
//     exit;
// }

// if(isset($_SESSION['token']) && !hash_equals($_SESSION['token'], $token)){
//     die("Request forgery detected");
// }

// $stmt = $mysqli->prepare("select COUNT(*) from users where username=?");
// if(!$stmt){
//     echo json_encode(array(
//         "success" => false,
//         "message" => "Query Prep Failed: {$mysqli->error}"
//     ));
//     exit;
// }

// $stmt->bind_param('s', $friend);
// $stmt->execute();

// $stmt->bind_result($cnt);
// $stmt->fetch();
// $stmt->close();

// if ($cnt==1) {

//     $stmt = $mysqli->prepare("select user_id from users where username=?");
//     if(!$stmt){
//         echo json_encode(array(
//             "success" => false,
//             "message" => "Query Prep Failed: {$mysqli->error}"
//         ));
//         exit;
//     }
//     $stmt->bind_param('s', $friend);
//     $stmt->execute();
    
//     $stmt->bind_result($friend_id);
//     $stmt->fetch();
//     $stmt->close();

//     $stmt = $mysqli->prepare("select friends from users where user_id=?");
//     if(!$stmt){
//         echo json_encode(array(
//             "success" => false,
//             "message" => "Query Prep Failed: {$mysqli->error}"
//         ));
//         exit;
//     }

//     //bind params 
//     $stmt->bind_param('i', $friend_id);
//     $stmt->execute();

//     $stmt->bind_result($friendString);
//     $stmt->fetch();
//     $stmt->close();

//     $friendArray = explode(',', $friendString);
//     if (in_array($user_id, $friendArray)) {
//         echo json_encode(array(
//             "success" => false,
//             "message" => "You have already shared your calendar with this user."
//         ));
//         exit;
//     }

//     $updatedFriends = $friendString . "," . $user_id;

//     $stmt = $mysqli->prepare("update users set friends=? where user_id=?");
//     if(!$stmt){
//         echo json_encode(array(
//             "success" => false,
//             "message" => "Query Prep Failed: {$mysqli->error}"
//         ));
//         exit;
//     }

//     //bind params 
//     $stmt->bind_param('si', $updatedFriends, $friend_id);
//     $stmt->execute();
//     $stmt->close();

//     echo json_encode(array(
//         "success" => true
//     ));
//     exit;
// } 
// echo json_encode(array(
//     "success" => false,
//     "message" => "Could not share calendar with user."
// ));
// exit;



?>