<?php
// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

    ini_set("session.cookie_httponly", 1);
    session_start();
    $previous_ua = @$_SESSION['useragent'];
    $current_ua = $_SERVER['HTTP_USER_AGENT'];

    if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
        die("Session hijack detected");
    }else{
        $_SESSION['useragent'] = $current_ua;
    }
    header("Content-Type: application/json");
    
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);

    $token = $json_obj['token'];

    if(isset($_SESSION['token']) && !hash_equals($_SESSION['token'], $token)){
        die("Request forgery detected");
    }

    $_SESSION = array();
    //code citation: https://www.php.net/manual/en/function.session-destroy.php
    //deletes the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    echo json_encode(array(
        "success" => true 
    ));
    exit;
?>