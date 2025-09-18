<?php

require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
require_once('../../../helper/read_database.php');

// require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

// print_r(implode(',',$_POST['groupOfarray']));
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
        $approvedy = isset($_POST['user_id']) ? $_POST['user_id']:0;
        $user_id = isset($_POST['user_id']) ? $_POST['user_id']:0;
        $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date']:'2000-01-01';

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
        $getSchemeIDqueryStmt = $read_db->prepare($getSchemeIDsqlQuery);
        $getSchemeIDqueryStmt->execute();
        $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
        
        if ($schemeIDprepost_count >= 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
        }


        $limit = count($_POST['groupOfarray']);
        $whereIn_Ids = implode(',',$_POST['groupOfarray']);
        
        

        $action ='';


            $requiredField = array('id','schemeid','yyyymm','benificiaryid','installmentid');
            $encryptionKey = 'jfEqTcM_BP0!/<Kc0)W}|Ap$_q(nyu21';
            $groupOfArr = [];
            $groupOfArrCombine = [];

           

            foreach ($_POST['groupOfarray'] as $key => $encryptedValue) {
                // Decrypt the value
                $decryptedValue = decryptString($encryptedValue, $encryptionKey);
                $stringToArr = explode(",", $decryptedValue);
                $groupOfArrCombine[$key] = array_combine($requiredField, $stringToArr);
            }

            // print_r($groupOfArrCombine);exit;


            $jsonData = json_encode($groupOfArrCombine,true);

                // $approvedy = 0;
                $updatestatus = false;

                $write_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $write_db->prepare("CALL sp_payment_reinitiateselected(:updatestatus, :benificiarydata, :approvedy,:paymentdate)");

                // echo "CALL sp_payment_reinitiateselected($updatestatus, $jsonData, $approvalstatus, $approvedy)";exit;
                // echo $stmt;exit;
                $stmt->bindParam(':updatestatus', $updatestatus, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);
                $stmt->bindParam(':benificiarydata', $jsonData, PDO::PARAM_STR);
                // $stmt->bindParam(':approvalstatus', $approvalstatus, PDO::PARAM_INT);
                $stmt->bindParam(':approvedy', $approvedy, PDO::PARAM_INT);
                $stmt->bindParam(':paymentdate', $payment_date, PDO::PARAM_STR);

                

                // Execute the stored procedure
                $result = $stmt->execute();

                if($result){
                    http_response_code(200);
                    $data = array(
                    "success" => 1, 
                    "message" => "Payment Reinitiate Successfully", 
                    'data' => $result
                    );
                }else{
                    $data = array(
                        "success" => 0, 
                        "message" => "Payment Reinitiate Not Successfully"
                    );
                }
               
            echo json_encode($data);
            die();
        
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

function decryptString($encryptedString, $encryptionKey) {
    list($encryptedData, $iv) = explode('::', base64_decode($encryptedString), 2);
    return openssl_decrypt($encryptedData, 'aes-256-cbc', $encryptionKey, 0, $iv);
}