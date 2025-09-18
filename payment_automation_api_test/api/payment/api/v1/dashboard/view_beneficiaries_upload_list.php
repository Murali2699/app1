<?php

require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');


ini_set('max_execution_time', 0);
error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    /*form filter datas*/
    
        // $rawString = str_replace('"','',$_POST['fileters']);
        // parse_str($rawString, $filters);

        $filters = json_decode($_POST['fileters'])->schemes_filters;
        
        $benificiaryid = !empty($filters->ddl_beeificiary) ? $filters->ddl_beeificiary:0;
        $schemecategory = !empty($filters->ddl_scheme_cate) ? $filters->ddl_scheme_cate:"";
        $schemecode = !empty($filters->ddl_scheme_code) ? $filters->ddl_scheme_code:"";
        $schemename = !empty($filters->ddl_scheme_name) ? $filters->ddl_scheme_name:"";
        $schemename = str_replace("'","''",$schemename);
        $subschemename = !empty($filters->ddl_sub_scheme) ? $filters->ddl_sub_scheme:"";
        $subschemename = str_replace("'","''",$subschemename);
        $idepartment = !empty($filters->ddl_department) ? $filters->ddl_department:0;
        $isubdepartment = !empty($filters->ddl_sub_department) ? $filters->ddl_sub_department:0;


        $districtcode = !empty($filters->ddl_district) ? $filters->ddl_district:0;
        $talukcode = !empty($filters->ddl_taluk) ? $filters->ddl_taluk:0;


        $idistrictcode = isset($_POST['districtCode']) ? $_POST['districtCode']:0;
        $italukcode = isset($_POST['talukCode']) ? $_POST['talukCode']:0;

        $jurisdictionlayercode = "";//!empty($filters->ddl_jurisdiction) ? $filters->ddl_jurisdiction:"''";
        $paymnetyearcode = !empty($filters->ddl_year) ? $filters->ddl_year:"";
        $credityearcode = !empty($filters->ddl_year) ? $filters->ddl_year:"";

        $paymentfromdate = !empty($filters->from_date) ? $filters->from_date:'2000-01-01';
        $paymenttodate = !empty($filters->to_date) ? $filters->to_date:'2000-01-01';
        $paymentperiod = !empty($filters->dayWeekMonthFilter) ? $filters->dayWeekMonthFilter:0;
        
        $creditfromdate = !empty($filters->credited_from_date) ? $filters->credited_from_date:'2000-01-01';
        $credittodate = !empty($filters->credited_to_date) ? $filters->credited_to_date:'2000-01-01';
        $creditperiod = !empty($filters->crediteddayWeekMonthFilter) ? $filters->crediteddayWeekMonthFilter:0;
        $uploadreferenceno = isset(json_decode($_POST['fileters'])->upload_reference_no) ? json_decode($_POST['fileters'])->upload_reference_no:0;

        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        // print_r($filters);exit;
        // echo $approvalstatus;exit;

        $schemeID = 0;

        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

        $sqlQuery = $getSchemeIDsqlQuery;

        $queryStmt = $read_db->prepare($sqlQuery);
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

    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = strtolower($_POST['search']['value']);

    $qry="";
    if($searchValue!=""){
        $qry="where lower(benificiary_name) like '%$searchValue%'
        OR lower(adhaar_no) like '%$searchValue%'
        OR lower(scheme_name) like '%$searchValue%'
        OR lower(yyyymm) like '%$searchValue%'
        OR lower(taluk_name) like '%$searchValue%'
        OR lower(district_name) like '%$searchValue%'";
    }

    $columnSortOrder = strtoupper($columnSortOrder);
    $ord="";
    if($columnIndex!=0){
        $ord="ORDER BY $columnName $columnSortOrder";
    }else{
        $ord = "ORDER BY benificiary_id ASC";
    }
    // $schemeID = 1;
    // $uploadreferenceno = 20230230001;
        $sqlQuery = "select * from fn_payment_uploadeddetails($schemeID,$uploadreferenceno,$idistrictcode,$italukcode) $qry $ord limit ".$rowperpage." offset ".$row.";";
        
            // echo $sqlQuery; exit;

        $totalCountsqlQuery = "select count(*) from fn_payment_uploadeddetails($schemeID,$uploadreferenceno,$idistrictcode,$italukcode)";


        $totalStmt = $read_db->prepare($totalCountsqlQuery);
        $totalStmt->execute();
        $totalCount = $totalStmt->fetchAll(PDO::FETCH_ASSOC);

        
        

        $action ='';

        $queryStmt = $read_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);


            
            http_response_code(200);
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            die();
        } else {
            $totalCount[0]['count'] = 0;
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $read_db=null;
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