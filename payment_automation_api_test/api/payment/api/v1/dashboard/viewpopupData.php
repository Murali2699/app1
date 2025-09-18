<?php
// echo'<pre>';
// print_r($_POST);exit;
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
// require_once('../../../helper/read_database_two.php');

// require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);
// print_r($_POST);
// exit;

//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $listID = isset($_POST['list_id']) ? $_POST['list_id'] : 0;

    if ($listID!=0) {
        $sqlQuery = "select * from fn_payment_getbenificiarydetailsbyid($listID);";
        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        

        $sqlQuery2 = "select * from fn_payment_getbenificiarytimelinebyid($listID);";
        $queryStmt2 = $write_db->prepare($sqlQuery2);
        $queryStmt2->execute();
        $prepost_count2 = $queryStmt2->rowCount();
        
        if ($prepost_count >= 1) {
            $listData = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            $listData['timeline'] = $queryStmt2->fetchAll(PDO::FETCH_ASSOC);

            
            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $listData
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

    }else{
        http_response_code(200);
        $data = array(
            "success" => 0, 
            "message" => "Use the application filter"
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

// function getDepartment() {
//     try {
//         $curl = curl_init();

//         curl_setopt_array($curl, array(
//             CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_SSL_VERIFYPEER=>false,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'POST',
//             CURLOPT_POSTFIELDS => 'type=department',
//             CURLOPT_HTTPHEADER => array(
//                 'X-APP-NAME: dBt$#&1@',
//                 'Content-Type: application/x-www-form-urlencoded'
//             ),
//         ));

//         $response = curl_exec($curl);

//         // Check for cURL errors
//         if ($response === false) {
//             throw new Exception('cURL error: ' . curl_error($curl));
//         }

//         curl_close($curl);

//         $arrDep = json_decode($response);

//         // Check if the API call was successful
//         if ($arrDep[0]->success == 1) {
//             return $arrDep[0]->data;
//         } else {
//             throw new Exception('API call unsuccessful: ' . $arrDep[0]->message);
//         }
//     } catch (Exception $e) {
//         // Handle exceptions (log, report, etc.) or rethrow if necessary
//         return array('error' => $e->getMessage());
//     }
// }


// function getSubDepartment($department_id) {
//     try {
//         $curl = curl_init();
//         curl_setopt_array($curl, array(
//             CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_SSL_VERIFYPEER=>false,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'POST',
//             CURLOPT_POSTFIELDS => 'type=sub_department&department_code=' . $department_id,
//             CURLOPT_HTTPHEADER => array(
//                 'X-APP-NAME: dBt$#&1@',
//                 'Content-Type: application/x-www-form-urlencoded'
//             ),
//         ));

//         $response = curl_exec($curl);

//         // Check for cURL errors
//         if ($response === false) {
//             throw new Exception('cURL error: ' . curl_error($curl));
//         }

//         curl_close($curl);

//         $arrSubDep = json_decode($response);

//         // Check if the API call was successful
//         if ($arrSubDep[0]->success == 1) {
//             return $arrSubDep[0]->data;
//         } else {
//             throw new Exception('API call unsuccessful: ' . $arrSubDep[0]->message);
//         }
//     } catch (Exception $e) {
//         // Handle exceptions (log, report, etc.) or rethrow if necessary
//         return array('error' => $e->getMessage());
//     }
// }