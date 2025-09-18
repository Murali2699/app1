<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Calcutta');

if (isset($_SERVER['HTTP_X_APP_KEY']) && !empty($_SERVER['HTTP_X_APP_KEY']) && isset($_SERVER['HTTP_X_APP_NAME']) && !empty($_SERVER['HTTP_X_APP_NAME'])) {
    $app_key = $_SERVER['HTTP_X_APP_KEY'];
    $allowedApps = array('km$ut_verific@tion','km$ut_dashbo@rd','km$ut_paym$nt','km$ut_webp0rt@l');
    if (!in_array($app_key, $allowedApps)) {
        http_response_code(400);
        $data = array("success" => 0, "message" => "Invalid App key");
        echo json_encode($data);
        die();
    }
} else {
    http_response_code(404);
    $data = array("success" => 0, "message" => "APP Key / APP Name Missing");
    echo json_encode($data);
    die();
}
