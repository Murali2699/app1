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
        $approvedy = isset($_POST['user_id']) ? $_POST['user_id']:0;

        $trackingId = isset($_POST['trackingId']) ? $_POST['trackingId']:0;
        $paymentdate = isset($_POST['paymentDate']) ? $_POST['paymentDate']:"";
        


        $summaryDistrictCode = isset($_POST['summaryDistrictCode']) ? $_POST['summaryDistrictCode']:0;
        $summaryTalukCode = isset($_POST['summaryTalukCode']) ? $_POST['summaryTalukCode']:0;

        
        /* get scheme id*/
        $getSchemessqlQuery = "select id,installments from fn_scheme_getschemes() where scheme_code like '".$schemecode."' and scheme_name like '".$schemename."' and subscheme_name like '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;

        

        $schemeID = 0;
        $installmentID = 0;
        $getSchemesqueryStmt = $write_db->prepare($getSchemessqlQuery);
        $getSchemesqueryStmt->execute();
        $schemeIDprepost_count = $getSchemesqueryStmt->rowCount();
        
        if ($schemeIDprepost_count >= 1) {
            $Getschemes = $getSchemesqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $Getschemes[0]['id'];
            $installmentID = $Getschemes[0]['installments'];
        }

        
        // if ($schemeID!=0) {
        //  echo "CALL sp_payment_approvebulk(false,".$approvalstatus.",0,".$benificiaryid.", ".$schemeID.",'".$schemename."','".$subschemename."',".$idepartment.",".$isubdepartment.",".$summaryDistrictCode.",".$summaryTalukCode.",'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',".$paymentperiod.",".$trackingId.")";exit;
    // print_r($_POST);exit;
        $action ='';

            // $data = array(
            // "success" => 1, 
            // "message" => "Pending Payment Bulk Approve Initiated",
            // );
            // http_response_code(200);
            // echo json_encode($data);

                $defultIntValue = 0;
                $write_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $write_db->prepare("CALL sp_payment_approvebulk(:updatestatus, :approvalstatus, :approvedy, :benificiaryid, :schemeid, :schemename, :subschemename, :idepartment,:isubdepartment,:districtcode,:talukcode,:jurisdictionlayercode,:iyearcode,:fromdate,:todate,:paymentperiod,:trackingid,:paymentdate)");

                $stmt->bindParam(':updatestatus', $updatestatus, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);
                $stmt->bindParam(':approvalstatus', $approvalstatus, PDO::PARAM_INT);
                $stmt->bindParam(':approvedy', $approvedy, PDO::PARAM_INT);
                $stmt->bindParam(':benificiaryid', $benificiaryid, PDO::PARAM_INT);
                $stmt->bindParam(':schemeid', $schemeID, PDO::PARAM_INT);
                $stmt->bindParam(':schemename', $schemename, PDO::PARAM_STR);
                $stmt->bindParam(':subschemename', $subschemename, PDO::PARAM_STR);
                $stmt->bindParam(':idepartment', $idepartment, PDO::PARAM_INT);
                $stmt->bindParam(':isubdepartment', $isubdepartment, PDO::PARAM_INT);
                // $stmt->bindParam(':districtcode', $districtcode, PDO::PARAM_INT);
                // $stmt->bindParam(':talukcode', $talukcode, PDO::PARAM_INT);

                $stmt->bindParam(':districtcode', $summaryDistrictCode, PDO::PARAM_INT);
                $stmt->bindParam(':talukcode', $summaryTalukCode, PDO::PARAM_INT);

                $stmt->bindParam(':jurisdictionlayercode',$jurisdictionlayercode, PDO::PARAM_STR);
                $stmt->bindParam(':iyearcode', $iyearcode, PDO::PARAM_STR);
                $stmt->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
                $stmt->bindParam(':todate', $fromdate, PDO::PARAM_STR);
                $stmt->bindParam(':paymentperiod', $paymentperiod, PDO::PARAM_INT);
                $stmt->bindParam(':trackingid', $trackingId, PDO::PARAM_INT);
                $stmt->bindParam(':paymentdate', $paymentdate, PDO::PARAM_STR);                
                $stmt->execute();

                // $stmt->debugDumpParams();exit;
                //$result = $stmt->execute();
                // print_r($result);
                // exit;

                
              

                // if($result){
                //     $data = array(
                //     "success" => 1, 
                //     "message" => "Payment Pending Bulk Approve initiated", 
                //     'data' => $result
                //     );
                // }else{
                //     $data = array(
                //         "success" => 0, 
                //         "message" => "Payment Pending Not Bulk Approve Successfully submited"
                //     );
                // }
                
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
