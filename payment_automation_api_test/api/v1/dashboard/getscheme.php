<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');

// require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');

error_reporting(E_ALL);
ini_set('display_errors',1);
// print_r($_POST);
// exit;

//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schemeID = isset($_POST['schemeID']) ? $_POST['schemeID'] : 0;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;

    if (isset($schemeID)) {
        $sqlQuery = "select * from fn_scheme_getschemesbyuser(".$user_id.") where id = ".$schemeID.";";
        $countQuery = "select * from fn_scheme_getschemesbyuser(".$user_id.") where id = ".$schemeID.";";

        $sqlQuerySchemeEnv = "select * from fn_scheme_getenvironmentsbyscheme(".$schemeID.");";

        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        $countQryStmt = $write_db->prepare($countQuery);
        $countQryStmt->execute();
        
        if ($prepost_count >= 1) {
            $schemeResult['schemes'] = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
                /* scheme env exc */
                $queryScheme = $write_db->prepare($sqlQuerySchemeEnv);
                $queryScheme->execute();
                $queryScheme_count = $queryScheme->rowCount();

                if($queryScheme_count >= 1){
                    $schemeResult['scheme_env'] = $queryScheme->fetchAll(PDO::FETCH_ASSOC);
                }

            $get_department_list = getDepartment();
                foreach ($schemeResult['schemes'] as $key => $scheme) {
                $matchingDepartments = array_filter($get_department_list, function($item) use ($scheme) {
                return $item->department_code == $scheme['department'];
                });
                $matchingDepartment = reset($matchingDepartments);
                $schemeResult['schemes'][$key]['department_name'] = isset($matchingDepartment->department_name) ? $matchingDepartment->department_name : null;
            }

            foreach ($schemeResult['schemes'] as $key => $scheme) {
                $get_sub_department_list = getSubDepartment($scheme['department']);
                // print_r($get_sub_department_list);exit;
                $matchingDepartments = array_filter($get_sub_department_list, function($item) use ($scheme) {
                    return $item->sub_department_code == $scheme['subdepartment'];
                });

                $matchingDepartment = reset($matchingDepartments);
                $schemeResult['schemes'][$key]['sub_department_name'] = isset($matchingDepartment->sub_department_name) ? $matchingDepartment->sub_department_name : null;
            }

            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $schemeResult
            );
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => $countQryStmt->rowCount(),
                'recordsFiltered' => $countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $write_db=null;
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