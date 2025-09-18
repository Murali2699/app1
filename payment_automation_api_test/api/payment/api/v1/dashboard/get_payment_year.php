<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schemID = isset($_POST['schem_id']) ? $_POST['schem_id'] : 0;

    $get_year_month = "select * from fn_payment_getpaymentmonths(".$schemID.")";
    
    $year_month_stmt = $read_db->prepare($get_year_month);
    $year_month_stmt->execute();
    $year_month_count = $year_month_stmt->rowCount();
    $opts2='';
        if ($year_month_count >= 1) {
                    $yearMonthResult = $year_month_stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($yearMonthResult as $row2){
                        $opts2=$opts2.'<option value="'.$row2['yyyymmvalue'].'">'.$row2['yyyymmtext'].'</option>'; 
                    }

            $yearMonthList='<option value="0">--All--</option>'.$opts2.'';
            // $yearMonthList=$opts2.'';
            $complaintResult['yearMonthList'] = $yearMonthList;

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
