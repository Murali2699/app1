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

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    /*form filter datas*/
        $rawString = str_replace('"','',$_POST['fileters']);
        parse_str($rawString, $filters);
        $benificiaryid = !empty($filters['ddl_beeificiary']) ? $filters['ddl_beeificiary']:0;
        $schemecategory = !empty($filters['ddl_scheme_cate']) ? $filters['ddl_scheme_cate']:"";
        $schemecode = !empty($filters['ddl_scheme_code']) ? $filters['ddl_scheme_code']:"";
        $schemename = !empty($filters['ddl_scheme_name']) ? $filters['ddl_scheme_name']:"";
        $schemename = str_replace("'","''",$schemename);
        $subschemename = !empty($filters['ddl_sub_scheme']) ? $filters['ddl_sub_scheme']:"";
        $subschemename = str_replace("'","''",$subschemename);
        $idepartment = !empty($filters['ddl_department']) ? $filters['ddl_department']:0;
        $isubdepartment = !empty($filters['ddl_sub_department']) ? $filters['ddl_sub_department']:0;
        $districtcode = !empty($filters['ddl_district']) ? $filters['ddl_district']:0;
        $talukcode = !empty($filters['ddl_taluk']) ? $filters['ddl_taluk']:0;
        $jurisdictionlayercode = "";//!empty($filters['ddl_jurisdiction']) ? $filters['ddl_jurisdiction']:"''";
        $paymnetyearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";
        $credityearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";

        $paymentfromdate = !empty($filters['from_date']) ? $filters['from_date']:'2000-01-01';
        $paymenttodate = !empty($filters['to_date']) ? $filters['to_date']:'2000-01-01';
        $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        
        $creditfromdate = !empty($filters['credited_from_date']) ? $filters['credited_from_date']:'2000-01-01';
        $credittodate = !empty($filters['credited_to_date']) ? $filters['credited_to_date']:'2000-01-01';
        $creditperiod = !empty($filters['crediteddayWeekMonthFilter']) ? $filters['crediteddayWeekMonthFilter']:0;

        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;
        $rowperpage = isset($_POST['total_list_count']) ? $_POST['total_list_count']:0;
        $taluk_enable = isset($_POST['taluk_enable']) ? $_POST['taluk_enable']:0;
        
        $row = 0;
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        

        $schemeID = 0;

        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

        $sqlQuery = $getSchemeIDsqlQuery;

        $queryStmt = $write_db->prepare($sqlQuery);
        // Bind parameters
        $queryStmt->bindParam(':schemecategory', $schemecategory, PDO::PARAM_STR);
        $queryStmt->bindParam(':schemecode', $schemecode, PDO::PARAM_STR);
        $queryStmt->bindParam(':schemename', $schemename, PDO::PARAM_STR);
        $queryStmt->bindParam(':subschemename', $subschemename, PDO::PARAM_STR);
        $queryStmt->bindParam(':idepartment', $idepartment, PDO::PARAM_INT);
        $queryStmt->bindParam(':isubdepartment', $isubdepartment, PDO::PARAM_INT);

        // Execute the query
        $queryStmt->execute();

        // Fetch the result
        $getSchemeIDqueryStmt = $queryStmt->fetch(PDO::FETCH_ASSOC);
        
        // $getschemeCount = count($getSchemeIDqueryStmt);
        // print_r($getSchemeIDqueryStmt);exit;

        if ($getSchemeIDqueryStmt !== false) {
            $GetschemeID = $getSchemeIDqueryStmt;
            $schemeID = $GetschemeID['id'];
        }

        if($taluk_enable == 1){
            $sqlQuery = "select * from fn_payment_getpaymentreport($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$paymnetyearcode."','".$paymentfromdate."','".$paymenttodate."',$paymentperiod,'".$credityearcode."','".$creditfromdate."','".$credittodate."',$creditperiod,$rowperpage,$row);";
        }else{
            $sqlQuery = "select scheme_category,scheme_code,scheme_name,subscheme_name,department,subdepartment,department_name,subdepartment_name,district_code,district_name,yyyymm,installment_id,received_dt,approved_dt,payment_date,sum(apb_beificiaries_count) AS apb_beificiaries_count,sum(apb_inprogress_count) AS apb_inprogress_count,sum(apb_success_count) AS apb_success_count,sum(apb_failure_count) AS apb_failure_count,sum(apb_beificiaries_amount) AS apb_beificiaries_amount,sum(apb_inprogress_amount) AS apb_inprogress_amount,sum(apb_success_amount) AS apb_success_amount,sum(apb_failure_amount) AS apb_failure_amount,sum(ach_beificiaries_count) AS ach_beificiaries_count,sum(ach_inprogress_count) AS ach_inprogress_count,sum(ach_success_count) AS ach_success_count,sum(ach_failure_count) AS ach_failure_count,sum(ach_beificiaries_amount) AS ach_beificiaries_amount,sum(ach_inprogress_amount) AS ach_inprogress_amount,sum(ach_success_amount) AS ach_success_amount,sum(ach_failure_amount) AS ach_failure_amount,sum(total_beificiaries_count) AS total_beificiaries_count,sum(total_inprogress_count) AS total_inprogress_count,sum(total_success_count) AS total_success_count,sum(total_failure_count) AS total_failure_count,sum(total_beificiaries_amount) AS total_beificiaries_amount,sum(total_inprogress_amount) AS total_inprogress_amount,sum(total_success_amount) AS total_success_amount,sum(total_failure_amount) AS total_failure_amount from fn_payment_getpaymentreport($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$paymnetyearcode."','".$paymentfromdate."','".$paymenttodate."',$paymentperiod,'".$credityearcode."','".$creditfromdate."','".$credittodate."',$creditperiod,$rowperpage,$row) group by district_code,scheme_category,scheme_code,scheme_name,subscheme_name,department,subdepartment,department_name,subdepartment_name,district_name,yyyymm,installment_id,received_dt,approved_dt,payment_date;";
        }

        // echo $sqlQuery; exit;

       

        $action ='';

        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
    
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);

            $totalCount[0]['total_benificiaries']= 500000;
            
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

function maskPhoneNumber($phoneNumber) {
  // Remove non-numeric characters from the phone number
  $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

  // Check if the phone number is at least 10 digits long
  if (strlen($phoneNumber) >= 10) {
      // Mask all digits except the 1st, 5th, and 10th digits
      $maskedNumber = substr($phoneNumber, 0, 1) . str_repeat('*', strlen($phoneNumber) - 7) . substr($phoneNumber, 4, 1) . str_repeat('*', strlen($phoneNumber) - 6) . substr($phoneNumber, 9);
      return $maskedNumber;
  } else {
      // If the phone number doesn't have at least 10 digits, return it as is
      return $phoneNumber;
  }
}