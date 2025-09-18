<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["script"])) {
    $scriptName = $_GET["script"];

    if ($scriptName === "generate_payment_file_cnrt" || $scriptName === "transfer_payment_file" || $scriptName === "final_recive_file1") {

        $venvPath = "/root/dbt/venv/bin/activate";
        $activateCommand = "bash -c 'source $venvPath'";
        shell_exec($activateCommand);
        $python_executable = "/root/dbt/venv/bin/python";
        $result = '';
    
        if ($scriptName === "generate_payment_file_cnrt") {

            $rawString = str_replace('"','',$_GET['fileters']);
            parse_str($rawString, $filters);
            $benificiaryid = !empty($filters['ddl_beeificiary']) ? $filters['ddl_beeificiary']:0;
            $schemecode = !empty($filters['ddl_scheme_code']) ? $filters['ddl_scheme_code']:"";
            $schemename = !empty($filters['ddl_scheme_name']) ? $filters['ddl_scheme_name']:"";
            $subschemename = !empty($filters['ddl_sub_scheme']) ? $filters['ddl_sub_scheme']:"";
            $idepartment = !empty($filters['ddl_department']) ? $filters['ddl_department']:0;
            $isubdepartment = !empty($filters['ddl_sub_department']) ? $filters['ddl_sub_department']:0;
            $districtcode = !empty($filters['ddl_district']) ? $filters['ddl_district']:0;
            $talukcode = !empty($filters['ddl_taluk']) ? $filters['ddl_taluk']:0;
            $jurisdictionlayercode = "";//!empty($filters['ddl_jurisdiction']) ? $filters['ddl_jurisdiction']:"''";
            $iyearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";
            $fromdate = !empty($filters['from_date']) ? $filters['from_date']:'01/01/2000';
            $todate = !empty($filters['to_date']) ? $filters['to_date']:'01/01/2000';
            $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
            $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;
            $user_id = isset($_POST['user_id']) ? $_POST['user_id']:0;
            // echo $approvalstatus;exit;
            
            /* get scheme id*/
            $getSchemeIDsqlQuery = "select id from fn_scheme_getschemes() where scheme_code like '".$schemecode."' and scheme_name like '".$schemename."' and subscheme_name like '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;
            $schemeID = 0;
            $getSchemeIDqueryStmt = $write_db->prepare($getSchemeIDsqlQuery);
            $getSchemeIDqueryStmt->execute();
            $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
    
            if ($schemeIDprepost_count >= 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
            }

            // $schemeID = 1;
            $command = "$python_executable /root/dbt/{$scriptName}.py {$schemeID} {$user_id} {$districtcode} {$talukcode} {$iyearcode} 2>&1";
            $result = shell_exec($command);
        } else {
            $command = "$python_executable /root/dbt/{$scriptName}.py {$schemeID} {$user_id} {$districtcode} {$talukcode} {$iyearcode} 2>&1";
            $result = shell_exec($command);
        }
        // echo $result;
            if($scriptName === "generate_payment_file_cnrt"){
            $data = array(
            "success" => 1,
            "message" => "Files Generated Initiated",
            "data"=>$result
            );
            }
            if($scriptName === "transfer_payment_file"){
            $data = array(
            "success" => 1,
            "message" => "Files transfer Initiated",
            "data"=>$result
            );
            }
            echo json_encode($data);
        die();
    }    
} else {
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}