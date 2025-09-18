<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
require_once('../../../helper/read_database.php');

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
        $schemecode = !empty($filters['ddl_scheme_code']) ? $filters['ddl_scheme_code']:"";
        $schemename = !empty($filters['ddl_scheme_name']) ? $filters['ddl_scheme_name']:"";
        // $schemename = str_replace("'","''",$schemename);
        $subschemename = !empty($filters['ddl_sub_scheme']) ? $filters['ddl_sub_scheme']:"";
        // $subschemename = str_replace("'","''",$subschemename);
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
        $approvedy = isset($_POST['user_id']) ? $_POST['user_id']:0;
        $reason_code = isset($_POST['reason_code']) ? $_POST['reason_code']:0;
        

        $trackingId = isset($_POST['trackingId']) ? $_POST['trackingId']:0;
        $paymentdate = isset($_POST['paymentDate']) ? $_POST['paymentDate']:"";
        
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


        $summaryDistrictCode = isset($_POST['summaryDistrictCode']) ? $_POST['summaryDistrictCode']:0;
        $summaryTalukCode = isset($_POST['summaryTalukCode']) ? $_POST['summaryTalukCode']:0;

        $schemenameStringReplace = str_replace("'","''",$schemename);
        $subschemenameStringReplace = str_replace("'","''",$subschemename);
        /* get scheme id*/
        $getSchemessqlQuery = "select id,installments from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code like '".$schemecode."' and scheme_name like '".$schemenameStringReplace."' and subscheme_name like '".$subschemenameStringReplace."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;

        

        $schemeID = 0;
        $installmentID = 0;
        $getSchemesqueryStmt = $read_db->prepare($getSchemessqlQuery);
        $getSchemesqueryStmt->execute();
        $schemeIDprepost_count = $getSchemesqueryStmt->rowCount();
        
        if ($schemeIDprepost_count >= 1) {
            $Getschemes = $getSchemesqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $Getschemes[0]['id'];
            $installmentID = $Getschemes[0]['installments'];
        }

        
            // if ($schemeID!=0) {
            //  echo "CALL sp_payment_reinitiatebulk(false, $approvalstatus, $approvedy, $benificiaryid, $schemeID, '$schemename', '$subschemename', $idepartment, $isubdepartment, $summaryDistrictCode, $summaryTalukCode, '$jurisdictionlayercode', '$iyearcode', '$fromdate', '$todate', $paymentperiod, $trackingId, '$paymentdate','$reason_code')";exit;
            
        $action ='';

        $updatestatus=true;
        $defultIntValue = 0;
                try{
                $write_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $write_db->prepare("
                CALL sp_payment_reinitiatebulk(
                    :updatestatus::boolean, 
                    :approvalstatus::integer, 
                    :approvedy::integer, 
                    :benificiaryid::bigint, 
                    :schemeid::integer, 
                    :schemename::varchar, 
                    :subschemename::varchar, 
                    :idepartment::integer, 
                    :isubdepartment::integer, 
                    :districtcode::integer, 
                    :talukcode::integer, 
                    :jurisdictionlayercode::varchar, 
                    :iyearcode::varchar, 
                    :fromdate::date, 
                    :todate::date, 
                    :paymentperiod::integer, 
                    :trackingid::bigint, 
                    :paymentdate::date,
                    :reasoncode::varchar
                )
            ");

                $stmt->bindParam(':updatestatus', $updatestatus, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);
                $stmt->bindParam(':approvalstatus', $approvalstatus, PDO::PARAM_INT);
                $stmt->bindParam(':approvedy', $approvedy, PDO::PARAM_INT);
                $stmt->bindParam(':benificiaryid', $benificiaryid, PDO::PARAM_INT);
                $stmt->bindParam(':schemeid', $schemeID, PDO::PARAM_INT);
                $stmt->bindParam(':schemename', $schemename, PDO::PARAM_STR);
                $stmt->bindParam(':subschemename', $subschemename, PDO::PARAM_STR);
                $stmt->bindParam(':idepartment', $idepartment, PDO::PARAM_INT);
                $stmt->bindParam(':isubdepartment', $isubdepartment, PDO::PARAM_INT);
                $stmt->bindParam(':districtcode', $summaryDistrictCode, PDO::PARAM_INT);
                $stmt->bindParam(':talukcode', $summaryTalukCode, PDO::PARAM_INT);
                $stmt->bindParam(':jurisdictionlayercode', $jurisdictionlayercode, PDO::PARAM_STR);
                $stmt->bindParam(':iyearcode', $iyearcode, PDO::PARAM_STR);
                $stmt->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
                $stmt->bindParam(':todate', $todate, PDO::PARAM_STR);
                $stmt->bindParam(':paymentperiod', $paymentperiod, PDO::PARAM_INT);
                $stmt->bindParam(':trackingid', $trackingId, PDO::PARAM_INT);
                $stmt->bindParam(':paymentdate', $paymentdate, PDO::PARAM_STR);
                $stmt->bindParam(':reasoncode', $reason_code, PDO::PARAM_STR);
                
                $result = $stmt->execute();

                if($result){
                    $data = array(
                        "success" => 1, 
                        "message" => "Reinitiate Bulk Approval Successfully", 
                        'data' => $result
                        );
                }else{
                    $data = array(
                        "success" => 0, 
                        "message" => "Reinitiate Bulk Approval Not Successfully",
                        'data' => null
                    );
                }
            
                echo json_encode($data);
                die();
                $write_db=null;
                } catch (PDOException $e) {

                    http_response_code(200);
                    $data = array(
                        "success" => 0,
                        "message" => "Database Error: " . $e->getMessage(),
                        'data' => array('trackingid'=>intval($trackingId))
                    );
                    echo json_encode($data);
                } catch (Exception $e) {

                    http_response_code(200);
                    $data = array(
                        "success" => 0,
                        "message" => "Error: " . $e->getMessage(),
                        'data' => array('trackingid'=>intval($trackingId))
                    );
                    echo json_encode($data);
                } finally {
                    $write_db = null;
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


