<?php
// echo'<pre>';
// print_r($_POST);exit;
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
        // echo $approvalstatus;exit;

        /* get scheme id*/
        $getSchemeIDsqlQuery = "select id from fn_scheme_getschemes() where scheme_code like '".$schemecode."' and scheme_name like '".$schemename."' and subscheme_name like '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;
        $schemeID = 0;
        $getSchemeIDqueryStmt = $write_db->prepare($getSchemeIDsqlQuery);
        $getSchemeIDqueryStmt->execute();
        $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
        
        if ($schemeIDprepost_count >= 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
        }
      

    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = strtolower($_POST['search']['value']);

    $qry="";
    if($searchValue!=""){
        $qry="where lower(benificiary_mobile_no) like '%$searchValue%'
        OR lower(benificiary_name) like '%$searchValue%'
        OR lower(payment_mode_name) like '%$searchValue%'
        OR lower(benificiary_reference_id) like '%$searchValue%'";  
    }


    $columnSortOrder = strtoupper($columnSortOrder);
    $ord="";
    if($columnIndex!=0){
        $ord="ORDER BY $columnName $columnSortOrder";
    }else{
        $ord = "ORDER BY id ASC";
    }


    // if ($schemeID!=0 && isset($taluk_code) && isset($shop_code)) {

        $sqlQuery = "select * from fn_payment_getbenificiariesforapproval($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,$rowperpage,$row) $qry;";//$ord limit ".$rowperpage." offset ".$row.";";
// echo $sqlQuery;exit;
        $totalCountsqlQuery = "select * from fn_payment_getbenificiarieslistcount($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus);";

        $totalStmt = $write_db->prepare($totalCountsqlQuery);
        $totalStmt->execute();
        $totalCount = $totalStmt->fetchAll(PDO::FETCH_ASSOC);
        

        $action ='';

        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        // $countQryStmt = $write_db->prepare($countQuery);
        // $countQryStmt->execute();

        // print_r($countQryStmt);exit;
        
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($complaintResult as $key => $scheme) {
                
                $complaintResult[$key]['action'] ='<a href="javascript:void(0);" class="badge text-bg-success c-pointer" data-bs-toggle="modal" data-bs-target=".viewdatainfo" onclick="viewScheme('.$scheme['id'].')"><i class="bi bi-eye"></i> view</a>';
                if($approvalstatus == 1){
                $complaintResult[$key]['checkbox'] = '<input class="form-check-input" type="checkbox" name="pending_check[]" value="'.$scheme['id'].'">';
                }
                if($approvalstatus == 4){
                    $complaintResult[$key]['remarks'] ='Account Number is wrong';
                    $complaintResult[$key]['checkbox'] = '<input class="form-check-input" type="checkbox" name="failed_check[]" value="'.$scheme['id'].'">';
                }
                
                if($approvalstatus == 5){
                    $complaintResult[$key]['checkbox'] = '<input class="form-check-input" type="checkbox" name="pre_file_generated_check[]" value="'.$scheme['id'].'">';
                }
            }

            // $get_department_list = getDepartment();
            // foreach ($complaintResult as $key => $scheme) {

            //     $matchingDepartments = array_filter($get_department_list, function($item) use ($scheme) {
            //         return $item->department_code == $scheme['department'];
            //     });
            //     $matchingDepartment = reset($matchingDepartments);
            //     $complaintResult[$key]['department_name'] = isset($matchingDepartment->department_name) ? $matchingDepartment->department_name : $scheme['department'];

            // }
            
            // foreach ($complaintResult as $key => $scheme) {
            //     $get_sub_department_list = getSubDepartment($scheme['department']);            
            //     $matchingDepartments = array_filter($get_sub_department_list, function($item) use ($scheme) {
            //         return $item->sub_department_code == $scheme['subdepartment'];
            //     });
            //     $matchingDepartment = reset($matchingDepartments);
            //     $complaintResult[$key]['sub_department_name'] = isset($matchingDepartment->sub_department_name) ? $matchingDepartment->sub_department_name : $scheme['subdepartment'];
            // }
            
            http_response_code(200);
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['total_benificiaries'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['total_benificiaries'],//$countQryStmt->rowCount(),
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['total_benificiaries'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['total_benificiaries'],//$countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $write_db=null;
    // else{
    //     http_response_code(200);
    //     $data = array(
    //         'draw' => intval($draw),
    //         'recordsTotal' => $totalStmt->rowCount(),
    //         'recordsFiltered' => $totalStmt->rowCount(),
    //         "success" => 0, 
    //         "message" => "Use the application filter"
    //     );
    //     echo json_encode($data);
    //     die();
    // }
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
//             CURLOPT_URL => 'https://tngis.tn.gov.in/apps/generic_api/v1/department',
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
//             CURLOPT_URL => 'https://tngis.tn.gov.in/apps/generic_api/v1/department',
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