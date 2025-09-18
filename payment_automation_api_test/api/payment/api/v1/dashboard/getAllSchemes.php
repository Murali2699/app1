<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');
error_reporting(E_ALL);
ini_set('display_errors',1);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        try {
            $query = "SELECT * FROM public.fn_scheme_getschemesbyuser(:userid)";
            $stmt = $read_db->prepare($query);
            $stmt->bindParam(':userid', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        
            $schemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($schemes);
        } catch (PDOException $e) {
            // Handle database error
            error_log("Database Error: " . $e->getMessage());
            echo json_encode(['error' => 'Database Error']);
        }
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
function getDepartment() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER=>false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'type=department',
        CURLOPT_HTTPHEADER => array(
            'X-APP-NAME: dBt$#&1@',
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $arrDep = json_decode($response);
    
    $department_list = array();
    if($arrDep[0]->success == 1){
        $department_list = $arrDep[0]->data;
    }
    return $department_list;
}

function getSubDepartment($department_id){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'type=sub_department&department_code='.$department_id,
    CURLOPT_HTTPHEADER => array(
        'X-APP-NAME: dBt$#&1@',
        'Content-Type: application/x-www-form-urlencoded'
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    
    $arrSubDep = json_decode($response);
    
    $sub_department_list = array();
    if($arrSubDep[0]->success == 1){
        $sub_department_list = $arrSubDep[0]->data;
    }
    return $sub_department_list;
}
?>