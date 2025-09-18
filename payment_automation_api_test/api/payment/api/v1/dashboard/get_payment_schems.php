<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $get_schem = "select * from fn_scheme_getactiveschemes()";
    $schem_stmt = $read_db->prepare($get_schem);
    $schem_stmt->execute();
    $schem_count = $schem_stmt->rowCount();
    $opts2='';
        if ($schem_count >= 1) {
                    $schemResult = $schem_stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($schemResult as $row2){
                        // $opts2=$opts2.'<option value="'.$row2['pds_shop_code']."|".$row2['revenue_village'].'">'.$row2['pds_shop_code'].'/'.$row2['pds_shop_name'].'</option>'; 
                        $opts2=$opts2.'<option value="'.$row2['id'].'">'.$row2['scheme_name'].'</option>'; 
                    }
                
            $schemList='<option value="0">--All--</option>'.$opts2.'';
            // $schemList=$opts2.'';
            $complaintResult['schemList'] = $schemList;

            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            $read_db=null;
            die();
            
        }else{
            http_response_code(200);
            $data = array(
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
    }else {
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
