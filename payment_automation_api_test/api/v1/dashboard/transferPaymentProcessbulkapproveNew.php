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
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;

    /*form filter datas*/
        $rawString = str_replace('"','',$_POST['fileters']);
        parse_str($rawString, $filters);
        $benificiaryid = !empty($filters['ddl_beeificiary']) ? $filters['ddl_beeificiary']:0;
        $schemecategory = !empty($filters['ddl_scheme_cate']) ? $filters['ddl_scheme_cate']:"";
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
        // $iyearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";
        $fromdate = !empty($filters['from_date']) ? $filters['from_date']:'01/01/2000';
        $todate = !empty($filters['to_date']) ? $filters['to_date']:'01/01/2000';
        $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;

        $idistrictcode = isset($_POST['district']) ? $_POST['district'] : 0;
        $italukcode = isset($_POST['taluk']) ? $_POST['taluk'] : 0;
        $trackingid = isset($_POST['trackingID']) ? $_POST['trackingID'] : 0;
        $updatedby = isset($_POST['user_id']) ? $_POST['user_id'] : '0';

        $yearString = !empty($filters['ddl_year']) ? $filters['ddl_year']:'';
        $mothString = !empty($filters['ddl_month']) ? $filters['ddl_month']:'';
        $yyyymm = '';

        if($paymentperiod == 4){
            $paymentperiod = 0;
            $yyyymm = $yearString.$mothString;
        }
        

        $schemeID = 0;
        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

        $sqlQuery = $getSchemeIDsqlQuery;

        $queryStmt = $write_db->prepare($sqlQuery);
        // Bind parameters
        $queryStmt->bindParam(':schemecategory', $schemecategory, PDO::PARAM_STR);
        $queryStmt->bindParam(':schemecode', $schemecode, PDO::PARAM_STR);
        $queryStmt->bindParam(':schemename', $schemename, PDO::PARAM_STR);
        $queryStmt->bindParam(':subschemename', $subschemename, PDO::PARAM_STR);
        $queryStmt->bindParam(':idepartment', $idepartment, PDO::PARAM_INT);
        $queryStmt->bindParam(':isubdepartment', $isubdepartment, PDO::PARAM_INT);

        // Execute the query
        $queryStmt->execute();

        // Fetch the result
        $getSchemeIDqueryStmt = $queryStmt->fetch(PDO::FETCH_ASSOC);

        if ($getSchemeIDqueryStmt !== false) {
            $GetschemeID = $getSchemeIDqueryStmt;
            $schemeID = $GetschemeID['id'];
        }


        // $sqlQuery = "select * from fn_validation_getprevalidationsummary($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$yyyymm."','".$fromdate."','".$todate."',$paymentperiod,'".$user_id."') $qry;";
        
        
        $defultIntValue = 0;
        $updatestatus = false;
    try {
        
    $write_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $write_db->prepare("CALL sp_payment_onboardbeificiaries(:updatestatus,:ischemeid,:ischemename,:isubschemename,:idepartment,:isubdepartment,:idistrictcode,:italukcode,:ijurisdictionlayercode,:iyearcode,:ifromdate,:itodate,:ipaymentperiod,:iuserid,:trackingid, :updatedby)");

        $stmt->bindParam(':updatestatus', $updatestatus, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);
        $stmt->bindParam(':ischemeid', $schemeID, PDO::PARAM_INT);
        $stmt->bindParam(':ischemename', $schemename, PDO::PARAM_STR);
        $stmt->bindParam(':isubschemename', $subschemename, PDO::PARAM_STR);
        $stmt->bindParam(':idepartment', $idepartment, PDO::PARAM_INT);
        $stmt->bindParam(':isubdepartment', $isubdepartment, PDO::PARAM_INT);
        $stmt->bindParam(':idistrictcode', $idistrictcode, PDO::PARAM_INT);
        $stmt->bindParam(':italukcode', $italukcode, PDO::PARAM_INT);
        $stmt->bindParam(':ijurisdictionlayercode', $jurisdictionlayercode, PDO::PARAM_STR);
        $stmt->bindParam(':iyearcode', $yyyymm, PDO::PARAM_STR);
        $stmt->bindParam(':ifromdate', $fromdate, PDO::PARAM_STR);
        $stmt->bindParam(':itodate', $todate, PDO::PARAM_STR);
        $stmt->bindParam(':ipaymentperiod', $paymentperiod, PDO::PARAM_INT);
        $stmt->bindParam(':iuserid', $user_id, PDO::PARAM_STR);
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
            "message" => "Payment Pending Not Bulk Approve Successfully submited",
            'data' => null
        );
    }

    echo json_encode($data);
    die();
    $write_db=null;
    } catch (PDOException $e) {
        // Handle PDOExceptions
        http_response_code(200);
        $data = array(
            "success" => 0,
            "message" => "Database Error: " . $e->getMessage(),
            'data' => array('trackingid'=>intval($trackingid))
        );
        echo json_encode($data);
    } catch (Exception $e) {
        // Handle other exceptions
        http_response_code(200);
        $data = array(
            "success" => 0,
            "message" => "Error: " . $e->getMessage(),
            'data' => array('trackingid'=>intval($trackingid))
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