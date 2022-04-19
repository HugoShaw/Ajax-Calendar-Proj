<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

//deals with SQL commands to register a user 
ini_set("session.cookie_httponly", 1);
session_start();
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
}else{
	$_SESSION['useragent'] = $current_ua;
}

require 'database.php';

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

if(!isset($json_obj)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Values could not be accessed."
    ));
    exit;
}

$username = $json_obj['username'];
$password = $json_obj['password'];

if(!isset($username) || !isset($password)) {
    echo json_encode(array(
        "success" => false,
        "message" => "Values could not be accessed."
    ));
    exit;
}

//check if username and password are valid in content/size
if (!preg_match('/^[\w_\-]+$/', $username) || strlen($username) > 50){
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid username."
    ));
    exit;
} else if (strlen($password) > 50){
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid password."
    ));
    exit;
} 

// Pull number of entries for username given 
$stmt = $mysqli->prepare("select COUNT(*) from users where username=? group by username");
if(!$stmt){
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: {$mysqli->error}"
    ));
    exit;
}
//bind params 
$stmt->bind_param('s', $username);
$stmt->execute();

//bind result
$stmt->bind_result($cnt);
$stmt->fetch();
$stmt->close();

echo $cnt;

//Continue only if no previous entries for username selected
if ($cnt==0) {
    $hash_pass = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $mysqli->prepare("insert into users (username, password) values (?,?)");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Query Prep Failed: {$mysqli->error}"
        ));
        exit;
    }
    $stmt->bind_param('ss', $username, $hash_pass);
    $stmt->execute();
    $stmt->close();

    //need the user_id to add a new category 
    $stmt = $mysqli->prepare("select user_id from users where username=?");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Query Prep Failed: {$mysqli->error}"
        ));
        exit;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();

    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    //adding a new category so that events can be attached to one, this field cannot be null
    $stmt = $mysqli->prepare("insert into categories (user_id, category, color) values (?,?,?)"); 
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Query Prep Failed: {$mysqli->error}"
        ));
        exit;
    }
    $cat_name = "My Calendar";
    
    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
    
    $color = rand_color();
    $stmt->bind_param('iss', $user_id, $cat_name, $color);
    $stmt->execute();

    echo json_encode(array(
        "success" => true 
    ));
    exit;
} 
echo json_encode(array(
    "success" => false,
    "message" => "Username already exists."
));
exit;

?>
