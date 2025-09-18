<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');


error_reporting(E_ALL);
ini_set('display_errors',1);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    /*form filter datas*/
        $rawString = str_replace('"','',$_POST['fileters']);
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
    

        $sqlQuery = "select * from fn_payment_summary_getmaindashboarddetails($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod);";
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
        $write_db=null;
    }else{
        http_response_code(200);
        $data = array(
            "success" => 0, 
            "message" => "Use the application filter"
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