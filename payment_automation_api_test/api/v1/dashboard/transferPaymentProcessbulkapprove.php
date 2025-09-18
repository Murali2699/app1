<?php

require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $approveStatus = isset($_POST['approveStatus']) ? $_POST['approveStatus'] : 0;
    $district = isset($_POST['district']) ? $_POST['district'] : 0;
    $taluk = isset($_POST['taluk']) ? $_POST['taluk'] : 0;
    $trackingid = isset($_POST['trackingID']) ? $_POST['trackingID'] : 0;
    $updatedby = isset($_POST['user_id']) ? $_POST['user_id'] : '0';

        $action ='';

                $defultIntValue = 0;
                $updatestatus = false;

                // echo "CALL sp_kmut_updatebeificiaries(false, $district,$taluk,$trackingid, $updatedby)";exit;

                $write_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $write_db->prepare("CALL sp_kmut_updatebeificiaries(:updatestatus, :district,:taluk,:trackingid, :updatedby)");
                
                $stmt->bindParam(':updatestatus', $updatestatus, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);
                $stmt->bindParam(':district', $district, PDO::PARAM_INT);
                $stmt->bindParam(':taluk', $taluk, PDO::PARAM_INT);
                $stmt->bindParam(':trackingid', $trackingid, PDO::PARAM_INT);
                $stmt->bindParam(':updatedby', $updatedby, PDO::PARAM_STR);

                $result = $stmt->execute();

                if($result){
                    $data = array(
                    "success" => 1, 
                    "message" => "Payment Pending Bulk Approve initiated", 
                    'data' => $result
                    );
                }else{
                    $data = array(
                        "success" => 0, 
                        "message" => "Payment Pending Not Bulk Approve Successfully submited"
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