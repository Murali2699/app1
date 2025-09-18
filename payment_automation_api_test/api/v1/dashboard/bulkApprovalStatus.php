<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');


error_reporting(E_ALL);
ini_set('display_errors',1);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        $trackingId = !empty($_POST['trackingId']) ? $_POST['trackingId'] : 0;
        $sqlQuery ="select COALESCE(total_count, 0) as total_count,COALESCE(updated_count, 0) as updated_count,COALESCE(status, 1) as status from fn_payment_getbulkapprovalstatus('".$trackingId."');";
        $action ='';

        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        if ($prepost_count >= 1) {
            $bulkApprovalStatus = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $bulkApprovalStatus
            );
            echo json_encode($data);
            die();
        $write_db=null;
        }else{
        $bulkApprovalStatus[0]  = array(
            "total_count"=>0,
            "updated_count"=>0,
            "initiated_ts"=>0,
            "status"=>2
        );
        http_response_code(200);
        $data = array(
            "success" => 0, 
            "message" => "Data not found",
            'data' => $bulkApprovalStatus
        );
        echo json_encode($data);
        die();
        }
}
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}