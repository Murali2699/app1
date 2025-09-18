<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
require_once('../../../helper/read_database.php');

ini_set('max_execution_time', 0);
error_reporting(E_ALL);
ini_set('display_errors',1);

//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    /*form filter datas*/
        $rawString = isset($_POST['fileters']) ? str_replace('"','',$_POST['fileters']):'';
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
        $fromdate = !empty($filters['from_date']) ? $filters['from_date']:'01/01/2000';
        $todate = !empty($filters['to_date']) ? $filters['to_date']:'01/01/2000';
        $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;

        $uploadreferenceno = isset($_POST['uploadreferenceno']) ? $_POST['uploadreferenceno']:0;
        $icreatedby = isset($_POST['user_id']) ? $_POST['user_id']:'';
        $icreatedfrom = isset($_POST['icreatedfrom']) ? $_POST['icreatedfrom']:'';
        $case = isset($_POST['case']) ? $_POST['case']:'';


        $yearString = !empty($filters['ddl_year']) ? $filters['ddl_year']:'';
        $mothString = !empty($filters['ddl_month']) ? $filters['ddl_month']:'';
        $yyyymm = '';

        
        if($paymentperiod == 4){
            $paymentperiod = 0;
            $yyyymm = $yearString.$mothString;
        }
        

        /* get scheme id*/
        $getSchemeIDsqlQuery = "select id from fn_scheme_getschemesbyuser(".$user_id.") where scheme_category LIKE '".$schemecategory."' and scheme_code LIKE '".$schemecode."' and scheme_name LIKE '".$schemename."' and subscheme_name LIKE '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;

        $schemeID = 0;
        $getSchemeIDqueryStmt = $read_db->prepare($getSchemeIDsqlQuery);
        $getSchemeIDqueryStmt->execute();
        $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
        
        if ($schemeIDprepost_count >= 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
        }


    if($case === 'fn_validation_aadhaar'){
        $sqlQuery ="select * from fn_validation_aadhaar($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."')";
        $activeId =  'function_1';
    }elseif($case === 'fn_validation_account'){
        $sqlQuery ="select * from fn_validation_account($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."')";
        $activeId =  'function_2';
    }elseif($case === 'fn_validation_duplicatepayment'){
        $sqlQuery ="select * from fn_validation_duplicatepayment($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."')";
        $activeId =  'function_3';
    }elseif($case === 'fn_validation_obsoletebeneficiaries'){
        $sqlQuery ="select * from fn_validation_obsoletebeneficiaries($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."')";
        $activeId =  'function_4';
    }

    // print_r($sqlQuery);exit;
    
    try {
        
        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1) {
            $validationFunctions['result'] = $queryStmt->fetchAll(PDO::FETCH_ASSOC)[0][$case];
            $validationFunctions['activeId'] = $activeId;
            
            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $validationFunctions
            );
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை",
                'data' => null
            );
            echo json_encode($data);
            die();
        }

        $write_db=null;
        
    }catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage(), 0);
        http_response_code(200);
        $data = array(
            "success" => 0,
            "message" => "PDO Database Error".$e->getMessage(),
            'data' => array('activeId' => $activeId)
        );
        echo json_encode($data);
        die();
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage(), 0);
        http_response_code(200);
        $data = array(
            "success" => 0,
            "message" => "Error",
            'data' => array('activeId' => $activeId)
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



