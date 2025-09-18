<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
$request_dt = date("Y-m-d H:i:s.u");
$start_time = microtime(true);
$payload = $_POST;
require_once('../../../helper/header.php');
//require_once('../../../helper/kmut_payment_db.php');
require_once('../../../helper/write_database.php');
require_once('log_api.php');
$curl = curl_init();
//$data = array(); //sp_scheme_create
if ($_SERVER["REQUEST_METHOD"] == "POST") {

 parse_str($_POST['data'], $output);

    // var_dump($output);exit;
$scheme_data['schemeid'] = isset($output['scheme_id']) ? (int)$output['scheme_id'] :0;
$scheme_data['paymentrequestfileid'] = isset($output['list_id']) ? (int)$output['list_id'] : 0;
$scheme_data['paymentmodeid'] = isset($output['paymentmodeid']) ? (int)$output['paymentmodeid'] : 0;
$scheme_data['reasoncode'] = isset($output['selected_reason']) ? $output['selected_reason'] : '';
$scheme_data['rejectionremarks'] = isset($output['reason_custom_text']) ? $output['reason_custom_text'] : '';
$scheme_data['filename'] = isset($output['filename']) ? (int)$output['filename'] : '';
$scheme_data['isreject'] = !empty($output['opration']) ? $output['opration'] : $output['opration'];
$scheme_data['paymentdate'] = isset($output['reinitiate_payment_date']) ? $output['reinitiate_payment_date'] : null;


$scheme_data['rejectedby'] = isset($output['popupuserid']) ? (int)$output['popupuserid'] : 0;
$scheme_data['filename'] = isset($output['filename']) ? (int)$output['filename'] : '';




$status = false;

// $sql = "CALL public.sp_payment_rejectorreinitiaterequestfile($status, '{$scheme_data['schemeid']}','{$scheme_data['paymentrequestfileid']}', '{$scheme_data['paymentmodeid']}', '{$scheme_data['reasoncode']}','{$scheme_data['rejectionremarks']}',{$scheme_data['isreject']},{$scheme_data['paymentdate']}','{$scheme_data['rejectedby']}')";
// call public.sp_payment_rejectorreinitiaterequestfile(
//     false,
// 1,
// 88,
// 2,
// '01'::char varying,
// 'sample reinitiate'::char varying,
// false,
// '2024-05-01'::Date,
// 1);

// echo $sql;exit;

    
    $sql = "CALL public.sp_payment_rejectorreinitiaterequestfile(?,?,?,?,?,?,?,?,?)";
    
        try {
            $stmt = $write_db->prepare($sql);
            $stmt->bindParam(1, $status, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(2, $scheme_data['schemeid'], PDO::PARAM_INT);
            $stmt->bindParam(3, $scheme_data['paymentrequestfileid'], PDO::PARAM_INT);
            $stmt->bindParam(4, $scheme_data['paymentmodeid'], PDO::PARAM_INT);
            $stmt->bindParam(5, $scheme_data['reasoncode'], PDO::PARAM_STR); // String parameter
            $stmt->bindParam(6, $scheme_data['rejectionremarks'], PDO::PARAM_STR); // String parameter
            $stmt->bindParam(7, $scheme_data['isreject'], PDO::PARAM_BOOL); // Boolean parameter
            $stmt->bindParam(8, $scheme_data['paymentdate'], PDO::PARAM_STR); // Date parameter (formatted as string)
            $stmt->bindParam(9, $scheme_data['rejectedby'], PDO::PARAM_INT);
                        
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(200);

            if($scheme_data['isreject']=='false'){
                $actions = 'Reinitiated';
            }else{
                $actions = 'Rejected';
            }
            $response = array('success' => 1, "message" => $actions." Successfully","data"=>$result);
            echo json_encode($response);
            $write_db = null;
            die();
        }
        catch (PDOException $err) 
        {
            $data =[];
            $error = $err->errorInfo;
            $splitted_error = substr($error[2], 0, strpos($error[2], "\n"));
            http_response_code(405);
            array_push($data, array("success" => 0, "message" => 'Backend Error'.$splitted_error,'Error' => $error));
            echo json_encode($data);

            $response_status_code = http_response_code(405);
            $response_dt = date("Y-m-d H:i:s.u");
            $response_time_ms = microtime(true) - $start_time;
            // log_api($scheme_data['ischemecode'],$response_status_code,'',$payload,$write_db,'Scheme','POST','paymet_process_scheme_register',$request_dt,$response_dt,$response_time_ms,$splitted_error);

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