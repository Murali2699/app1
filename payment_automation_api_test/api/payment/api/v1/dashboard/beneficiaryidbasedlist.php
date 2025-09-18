<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

// $data = json_decode(file_get_contents('php://input'), true);
// print_r(urldecode($_POST['fileters']));exit;



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $secretKeyHex = $_POST['secretKeyHex'];
    $ivHex = $_POST['ivHex'];
    $secretKey = hex2bin($secretKeyHex);
    $iv = hex2bin($ivHex);

    $decryptedString = openssl_decrypt(base64_decode($_POST['fileters']), 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);

    // print_r($decryptedPassword);

        $rawString = str_replace('"','',$decryptedString);
        parse_str($rawString, $filters);
        $selectType = !empty($filters['selectType']) ? $filters['selectType']:"";
        $beneficiariesid = !empty($filters['beneficiariesid']) ? $filters['beneficiariesid']:"";
        $uidNumber = !empty($filters['uidNumber']) ? $filters['uidNumber']:"";
        $user_id = !empty($filters['user_id']) ? $filters['user_id']:0;

        if(!empty($beneficiariesid)){
            $benificiaryid = $beneficiariesid;
        }elseif(!empty($uidNumber)){
            $benificiaryid = $uidNumber;
        }
        
        // print_r($filters);exit;

        $sqlQuery = "select * from fn_payment_findbeneficiary('".$benificiaryid."',$user_id);";

        $action ='';

        $queryStmt = $read_db->prepare($sqlQuery);
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
        $read_db=null;
    }else{
        http_response_code(200);
        $data = array(
            "success" => 0, 
            "message" => "Beneficiary Not Found"
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


