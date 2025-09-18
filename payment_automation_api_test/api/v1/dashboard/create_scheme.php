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

$scheme_data['schemeid'] = 0;//isset($_POST['UserID']) ? $_POST['UserID'] : '';
$scheme_data['ischemeCategory'] = isset($_POST['schemeCategory']) ? $_POST['schemeCategory'] : '';//str
$scheme_data['ischemecode'] = isset($_POST['schemeCode']) ? $_POST['schemeCode'] : '';//str
$scheme_data['ischemename'] = isset($_POST['schemeName']) ? $_POST['schemeName'] : '';//str
$scheme_data['isubschemename'] = isset($_POST['subSchemeName']) ? $_POST['subSchemeName'] : '';//str
$scheme_data['idepartment'] = isset($_POST['dep']) ? $_POST['dep'] : '';//int
$scheme_data['isubdepartment'] = isset($_POST['sub_dep']) ? $_POST['sub_dep'] : '';//int
$scheme_data['ipaymentcycle'] = isset($_POST['paymentCycle']) ? $_POST['paymentCycle'] : '';//int
$scheme_data['iinstallments'] = isset($_POST['installment']) ? $_POST['installment'] : '';//int
$scheme_data['ipaymentmethod'] = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : '';//int
$scheme_data['ibudgetcode'] = isset($_POST['budgetCode']) ? $_POST['budgetCode'] : '';
$scheme_data['iaccounthead'] = isset($_POST['acHead']) ? $_POST['acHead'] : '';
$scheme_data['inodalbankname'] = isset($_POST['nodalBank']) ? $_POST['nodalBank'] : '';
$scheme_data['inodalbankaccountno'] = isset($_POST['acNo']) ? $_POST['acNo'] : '';
$scheme_data['inodalbankbranchname'] = isset($_POST['bankBranchName']) ? $_POST['bankBranchName'] : '';
$scheme_data['inodalbankiinno'] = isset($_POST['nodalBankIinNo']) ? $_POST['nodalBankIinNo'] : '';
$scheme_data['inodalbankusernumber'] = isset($_POST['nodalBankUserNo']) ? $_POST['nodalBankUserNo'] : '';
$scheme_data['icreatedby'] = isset($_POST['UserID']) ? $_POST['UserID'] : 0;//login user
$scheme_data['idepartmentname'] = isset($_POST['department_text']) ? $_POST['department_text'] : '';
$scheme_data['isubdepartmentname'] = isset($_POST['sub_department_text']) ? $_POST['sub_department_text'] : '';




$arrUatAndProdunction = [];

if (!empty($_POST['uat_ftp_host'])) {
    $environment = 0;
    $arrUatAndProdunction[] = [
        'environment' => $environment,
        'ftp_host' => !empty($_POST['uat_ftp_host']) ? $_POST['uat_ftp_host'] : '',
        'ftp_port' => !empty($_POST['uat_ftp_port']) ? $_POST['uat_ftp_port'] : '',
        'ftp_request_file_path' => !empty($_POST['uat_ftp_request_path']) ? $_POST['uat_ftp_request_path'] : '',
        'ftp_response_file_path' => !empty($_POST['uat_ftp_response_path']) ? $_POST['uat_ftp_response_path'] : '',
        'ftp_username' => !empty($_POST['uat_ftp_username']) ? $_POST['uat_ftp_username'] : '',
        'ftp_password' => !empty($_POST['uat_ftp_password']) ? $_POST['uat_ftp_password'] : '',
        'ftp_response_ack_file_path'=> !empty($_POST['uat_ftp_response_ack_path']) ? $_POST['uat_ftp_response_ack_path'] : '',
        'ftp_response_fc_file_path'=> !empty($_POST['uat_ftp_response_fl_path']) ? $_POST['uat_ftp_response_fl_path'] : '',
    ];
}

if (!empty($_POST['production_ftp_host'])) {
    $environment = 1;
    $arrUatAndProdunction[] = [
        'environment' => $environment,
        'ftp_host' => !empty($_POST['production_ftp_host']) ? $_POST['production_ftp_host'] : '',
        'ftp_port' => !empty($_POST['production_ftp_port']) ? $_POST['production_ftp_port'] : '',
        'ftp_request_file_path' => !empty($_POST['production_ftp_request_path']) ? $_POST['production_ftp_request_path'] : '',
        'ftp_response_file_path' => !empty($_POST['production_ftp_response_path']) ? $_POST['production_ftp_response_path'] : '',
        'ftp_username' => !empty($_POST['production_ftp_username']) ? $_POST['production_ftp_username'] : '',
        'ftp_password' => !empty($_POST['production_ftp_password']) ? $_POST['production_ftp_password'] : '',
        'ftp_response_ack_file_path'=> !empty($_POST['production_ftp_response_ack_path']) ? $_POST['production_ftp_response_ack_path'] : '',
        'ftp_response_fc_file_path'=> !empty($_POST['production_ftp_response_fl_path']) ? $_POST['production_ftp_response_fl_path'] : '',
    ];
}

$scheme_data['ienvironment'] = json_encode($arrUatAndProdunction,true);
    
    $sql = "CALL public.sp_scheme_create(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        try {
            $stmt = $write_db->prepare($sql);
            $stmt->bindParam(1, $schemeid, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(2, $scheme_data['ischemeCategory']);
            $stmt->bindParam(3, $scheme_data['ischemecode']);
            $stmt->bindParam(4, $scheme_data['ischemename']);
            $stmt->bindParam(5, $scheme_data['isubschemename']);
            $stmt->bindParam(6, $scheme_data['idepartment']);
            $stmt->bindParam(7, $scheme_data['isubdepartment']);
            $stmt->bindParam(8, $scheme_data['ipaymentcycle']);
            $stmt->bindParam(9, $scheme_data['iinstallments']);
            $stmt->bindParam(10, $scheme_data['ipaymentmethod']);
            $stmt->bindParam(11, $scheme_data['ibudgetcode']);
            $stmt->bindParam(12, $scheme_data['iaccounthead']);
            $stmt->bindParam(13, $scheme_data['inodalbankname']);
            $stmt->bindParam(14, $scheme_data['inodalbankaccountno']);
            $stmt->bindParam(15, $scheme_data['inodalbankbranchname']);
            $stmt->bindParam(16, $scheme_data['inodalbankiinno']);
            $stmt->bindParam(17, $scheme_data['inodalbankusernumber']);
            $stmt->bindParam(18, $scheme_data['icreatedby']);
            $stmt->bindParam(19, $scheme_data['idepartmentname']);
            $stmt->bindParam(20, $scheme_data['isubdepartmentname']);
            $stmt->bindParam(21, $scheme_data['ienvironment']);
            
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(200);
            $response = array('success' => 1, "message" => "Scheme Successfully Registered","data"=>$result);
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
            log_api($scheme_data['ischemecode'],$response_status_code,'',$payload,$write_db,'Scheme','POST','paymet_process_scheme_register',$request_dt,$response_dt,$response_time_ms,$splitted_error);

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