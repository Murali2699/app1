<?php
// echo'<pre>';
// print_r($_POST);exit;
require_once('../../../helper/header.php');
require_once('../../../helper/read_database_two.php');
// require_once('../../../class/pdf/pdf.php');


// require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
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
        $iyearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";
        $fromdate = !empty($filters['from_date']) ? $filters['from_date']:'2000-01-01';
        $todate = !empty($filters['to_date']) ? $filters['to_date']:'2000-01-01';
        $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;
        // echo $approvalstatus;exit;

        $yearString = !empty($filters['ddl_year']) ? $filters['ddl_year']:'';
        $mothString = !empty($filters['ddl_month']) ? $filters['ddl_month']:'';
        
        $iyyyymm = 0;
        if($paymentperiod == 3 || $paymentperiod == 2 || $paymentperiod == 1){
            // $iyyyymm = date("Ym");
            $iyyyymm = '';
        }

        if($paymentperiod == 4){
            $paymentperiod = 0;
            $iyyyymm = intval($yearString.$mothString);
        }

        /* get scheme id*/
        $getSchemeIDsqlQuery = "select id from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code like '".$schemecode."' and scheme_name like '".$schemename."' and subscheme_name like '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;
        $schemeID = 0;
        $getSchemeIDqueryStmt = $read_db_two->prepare($getSchemeIDsqlQuery);
        $getSchemeIDqueryStmt->execute();
        $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
        
        if ($schemeIDprepost_count >= 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
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
        $qry="where lower(taluk_name) like '%$searchValue%'
        OR lower(district_name) like '%$searchValue%'";  
    }


    $columnSortOrder = strtoupper($columnSortOrder);
    $ord="";
    if($columnIndex!=0){
        $ord="ORDER BY $columnName $columnSortOrder";
    }else{
        $ord = "ORDER BY id ASC";
    }


    // if ($schemeID!=0 && isset($taluk_code) && isset($shop_code)) {

        // $sqlQuery = "select * from  fn_kmut_getbenificiarysummary() $qry;";
        $sqlQuery = "select district_name,taluk_name,apb_count,apb_amount,ach_count,ach_amount,total_count,total_amount from  fn_validation_getpretransfersummary($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyyyymm."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."') $qry;";

        // $sqlQuery = "select * from  fn_validation_getpretransfersummary($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyyyymm."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."') $qry;";
        
        // echo $sqlQuery;exit;

        $action ='';

        $queryStmt = $read_db_two->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1) {
            $bulkApproveSummary = $queryStmt->fetchAll(PDO::FETCH_ASSOC);


            // create pdf 
            // $exporter = new PDFExporter();
            // $header = [
            // [null,null,null,'APB',null,'ACH',null,'Total',null],
            // ['S.no','District','Taluk','Count','Amount','Count','Amount','Count','Amount'],
            // ];
            // $data = $bulkApproveSummary;
            
            // $exporter->exportSummaryListToPDF($header,$data);
            // $exporter->outputPDF('/opt/lampp/htdocs/payment_automation/api/payment/waiting_for_approval/'.$schemename.'_'.$schemeID.'_'.$user_id.'_tranferToPaymentPorcess.pdf', 'F');

            $uniqueTimestamps = [];
            $bulkApproveSummaryAddTracking = [];

            foreach ($bulkApproveSummary as $key => $values) {
                $currentTimeStamp = generateUniqueId();
                while (in_array($currentTimeStamp, $uniqueTimestamps)) {
                    $currentTimeStamp = generateUniqueId();
                }
                $uniqueTimestamps[] = $currentTimeStamp;
                $bulkApproveSummaryAddTracking[$key] = $values;
                $bulkApproveSummaryAddTracking[$key]['trackingID'] = $currentTimeStamp;
            }

            http_response_code(200);
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $prepost_count,//$countQryStmt->rowCount(),
                'recordsFiltered' => $prepost_count,//$countQryStmt->rowCount(),
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $bulkApproveSummaryAddTracking
            );
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => 0,//$countQryStmt->rowCount(),
                'recordsFiltered' => 0,//$countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $read_db_two=null;
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


function generateUniqueId() {
    list($microseconds, $seconds) = explode(' ', microtime());
    $timestamp = $seconds * 1000 + round($microseconds * 1000);
    return $timestamp;
}



