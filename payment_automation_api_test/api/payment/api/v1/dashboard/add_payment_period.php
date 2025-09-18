<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
$request_dt = date("Y-m-d H:i:s.u");
$start_time = microtime(true);
$payload = $_POST;
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
require_once('log_api.php');
$curl = curl_init();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
// print_r($_POST);exit;

    $scheme_data['createdby'] = !empty($_POST['user_id']) ? $_POST['user_id'] : 0;
    $scheme_data['ischemeid'] = !empty($_POST['scheme_id']) ? $_POST['scheme_id'] : 0;
    $scheme_data['ipaymentcycle'] = !empty($_POST['payment_cycle']) ? $_POST['payment_cycle'] : 0;
    $scheme_data['ipaymentperiod'] = !empty($_POST['addedPaymentPeriod']) ? $_POST['addedPaymentPeriod'] : -1;
    $scheme_data['iinstallment'] = !empty($_POST['addedInstallment']) ? $_POST['addedInstallment'] : $_POST['default_installment'];
    $scheme_data['monthly_list'] = !empty($_POST['monthly_list']) ? $_POST['monthly_list'] : 0;
    $scheme_data['year_list'] = isset($_POST['year_list']) ? $_POST['year_list'] : 0;

    $yyyymm = $scheme_data['year_list'].$scheme_data['monthly_list'];
    $yyyymmConvertNum = (int)$yyyymm;
    $scheme_data['iyyyymm'] = isset($yyyymmConvertNum) ? $yyyymmConvertNum : 0;
    $scheme_data['iyyyymm'] = isset($yyyymmConvertNum) ? $yyyymmConvertNum : 0;

    $paymentid = 0;

    // $payload = "CALL public.sp_scheme_createpayment($paymentid,{$scheme_data['ischemeid']}, {$scheme_data['ipaymentcycle']}, {$scheme_data['ipaymentperiod']}, {$scheme_data['iinstallment']}, {$scheme_data['iyyyymm']},{$scheme_data['createdby']})";

    // print_r($scheme_data);exit;

    // echo $payload;exit;

    $sql = "CALL public.sp_scheme_createpayment(?, ?, ?, ?, ?, ?, ?)";

        try {
            $stmt = $write_db->prepare($sql);
            $stmt->bindParam(1, $paymentid, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(2, $scheme_data['ischemeid']);
            $stmt->bindParam(3, $scheme_data['ipaymentcycle']);
            $stmt->bindParam(4, $scheme_data['ipaymentperiod']);
            $stmt->bindParam(5, $scheme_data['iinstallment']);
            $stmt->bindParam(6, $scheme_data['iyyyymm']);
            $stmt->bindParam(7, $scheme_data['createdby']);
           
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(200);
            $response = array('success' => 1, "message" => "Payment Period Added Successfully","data"=>$result);
            echo json_encode($response);
            $write_db = null;
            die();
        }
        catch (PDOException $err) 
        {
            $data =[];
            $error = $err->errorInfo;
            $splitted_error = substr($error[2], 0, strpos($error[2], "\n"));
            http_response_code(200);
            array_push($data, array("success" => 0, "message" => $splitted_error,'Error' => $splitted_error));

            echo json_encode($data);

            $response_status_code = http_response_code(200);
            $response_dt = date("Y-m-d H:i:s.u");
            $response_time_ms = microtime(true) - $start_time;
            // log_api(json_encode($scheme_data),$response_status_code,'',$payload,$write_db,'Scheme','POST','paymet_process_scheme_register',$request_dt,$response_dt,$response_time_ms,$splitted_error);

            $write_db = null;
            die();
        }
    
} 
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    $write_db = null;
    die();
}