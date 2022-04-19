<?php
//deals with SQL commands to login user 
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

// Only continue if there is exactly 1 entry for username given
if ($cnt==1) {
    $stmt = $mysqli->prepare("select user_id, password from users where username=?");
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
    $stmt->bind_result($user_id, $pwd_hash);
    $stmt->fetch();
    $stmt->close();

    if(password_verify($password,$pwd_hash)){
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 
    
        echo json_encode(array(
            "success" => true,
            "token" => $_SESSION['token']
        ));
        exit;
    }else{
        echo json_encode(array(
            "success" => false,
            "message" => "Incorrect Username or Password"
        ));
        exit;
    }
} 
echo json_encode(array(
    "success" => false,
    "message" => "Incorrect Username or Password"
));
exit;

?>
