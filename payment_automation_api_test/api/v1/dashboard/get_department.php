<?php
require_once('../../../helper/header.php');
// error_reporting(E_ALL);
// ini_set('display_errors',1);

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // $url = "https://tngis.tnega.org/generic_api/v1/department";
        $url = "https://tngis.tn.gov.in/apps/generic_api/v1/department";


        $data = $_POST;

        if($_POST['type']=="sub_department" && $_POST['department_code']!=0){
            $data = 'type=sub_department&department_code='.$_POST['department_code'];

        }elseif($_POST['type']="department"){
            $data = 'type=department';
        }

        // echo $data;exit;
        

        // Dynamically set headers based on the incoming request
        $headers = array(
            'X-APP-NAME: dBt$#&1@',
            'Content-Type: application/x-www-form-urlencoded'
          );

        $ch = curl_init($url);
        //'type=department'

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        http_response_code(200);
        echo $response;
    } else {
        http_response_code(405);
        $data = array("success" => 0, "message" => "Method Not Allowed");
        echo json_encode($data);
        die();
    }
} catch (Exception $e) {
    http_response_code(500);
    $error = array("success" => 0, "message" => "Internal Server Error: " . $e->getMessage());
    echo json_encode($error);
}

