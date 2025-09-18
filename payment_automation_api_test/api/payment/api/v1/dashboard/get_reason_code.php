<?php
// echo'<pre>';
// print_r($_POST);exit;
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');

// require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);
// print_r($_POST);
// exit;

//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $payment_mode_id = isset($_POST['payment_mode_id']) ? $_POST['payment_mode_id'] : 0;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;


        $sqlQuery = "select * from fn_payment_getrejectionreasons($payment_mode_id);";
        //$ord limit ".$rowperpage." offset ".$row.";";
    // echo $sqlQuery;exit;
        $action ='';

        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);


            
            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $write_db=null;
}
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}