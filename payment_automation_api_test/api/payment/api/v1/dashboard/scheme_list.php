<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

// require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');

error_reporting(error_reporting() & ~E_WARNING);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// print_r($_POST);
// exit;

//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = strtolower($_POST['search']['value']);

    $qry = "";
    $orAdd = "";
if ($searchValue != "") {
    $qry = "
        lower(scheme_code) LIKE '%$searchValue%' 
        OR lower(scheme_name) LIKE '%$searchValue%'
        OR lower(subscheme_name) LIKE '%$searchValue%'
    ";
}

$whereConditions = [];
if (!empty($_POST['fileters'])) {
    $arrCase = [
        "filter_scheme_category"=>"scheme_category like",
        "filter_scheme_code" => "and scheme_code like",
        "filter_scheme_name" => "and scheme_name like",
        "filter_subscheme" => "and subscheme_name like",
        "filter_deparment" => "and department =",
        "filter_sub_deparment" => "and subdepartment =",
    ];
    
    foreach ($_POST['fileters'] as $filter) {
        $filter_value = str_replace("'","''",$filter['inputValue']);
        if (array_key_exists($filter['case'], $arrCase)) {
            $whereConditions[] = "{$arrCase[$filter['case']]} '{$filter_value}'";
        }
    }
}

$whereClause = implode(' ', $whereConditions);
$addWhere = "";


if (!empty($whereClause) || $qry != "") {
    $addWhere = " WHERE ";

}

if (!empty($whereClause) && $qry != "") {
    $orAdd = " OR ";
}


$columnSortOrder = strtoupper($columnSortOrder);
$ord = ($columnIndex != 0) ? "ORDER BY $columnName $columnSortOrder" : "ORDER BY id ASC";

if (isset($district_code) && isset($taluk_code) && isset($shop_code)) {
    $sqlQuery = "SELECT * FROM fn_scheme_getschemesbyuser(".$user_id.") $addWhere $whereClause $orAdd $qry $ord LIMIT $rowperpage OFFSET $row;";
    // echo $sqlQuery ;exit;
    $countQuery = "SELECT COUNT(*) FROM fn_scheme_getschemesbyuser(".$user_id.") $addWhere $whereClause $orAdd $qry;";

        $action ='';

        $queryStmt = $read_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        $countQryStmt = $read_db->prepare($countQuery);
        $countQryStmt->execute();
        
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($complaintResult as $key => $scheme) {
                $complaintResult[$key]['action'] ='<a href="javascript:void(0);" class="badge text-bg-success c-pointer" data-bs-toggle="modal" data-bs-target=".viewdatainfo" onclick="viewScheme('.$scheme['id'].')"><i class="bi bi-eye"></i> view</a>';
            }

            $get_department_list = getDepartment();
            
            foreach ($complaintResult as $key => $scheme) {
                if (is_array($scheme) && isset($scheme['department'])) {
                    $matchingDepartments = array_filter($get_department_list, function($item) use ($scheme) {
                        return isset($item->department_code) && $item->department_code == $scheme['department'];
                    });
            
                    $matchingDepartment = reset($matchingDepartments);
            
                    $complaintResult[$key]['department_name'] = isset($matchingDepartment->department_name) ? $matchingDepartment->department_name : $scheme['department'];
                } else {
                    // Handle the case when $scheme['department'] is not set or not an array
                    $complaintResult[$key]['department_name'] = $scheme['department'];  // You might need to adjust this based on your requirements
                }
            }

            
            foreach ($complaintResult as $key => $scheme) {
                $get_sub_department_list = getSubDepartment($scheme['department']);            
                $matchingDepartments = array_filter($get_sub_department_list, function($item) use ($scheme) {
                    return $item->sub_department_code == $scheme['subdepartment'];
                });
                $matchingDepartment = reset($matchingDepartments);
                $complaintResult[$key]['sub_department_name'] = isset($matchingDepartment->sub_department_name) ? $matchingDepartment->sub_department_name : $scheme['subdepartment'];
            }
            
            http_response_code(200);
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $countQryStmt->rowCount(),
                'recordsFiltered' => $countQryStmt->rowCount(),
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
                'recordsTotal' => $countQryStmt->rowCount(),
                'recordsFiltered' => $countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $read_db=null;
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
    try {
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

        // Check for cURL errors
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($curl));
        }

        curl_close($curl);

        $arrDep = json_decode($response);

        // Check if the API call was successful
        if ($arrDep[0]->success == 1) {
            return $arrDep[0]->data;
        } else {
            throw new Exception('API call unsuccessful: ' . $arrDep[0]->message);
        }
    } catch (Exception $e) {
        // Handle exceptions (log, report, etc.) or rethrow if necessary
        return array('error' => $e->getMessage());
    }
}


function getSubDepartment($department_id) {
    try {
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
            CURLOPT_POSTFIELDS => 'type=sub_department&department_code=' . $department_id,
            CURLOPT_HTTPHEADER => array(
                'X-APP-NAME: dBt$#&1@',
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($curl));
        }

        curl_close($curl);

        $arrSubDep = json_decode($response);

        // Check if the API call was successful
        if ($arrSubDep[0]->success == 1) {
            return $arrSubDep[0]->data;
        } else {
            throw new Exception('API call unsuccessful: ' . $arrSubDep[0]->message);
        }
    } catch (Exception $e) {
        // Handle exceptions (log, report, etc.) or rethrow if necessary
        return array('error' => $e->getMessage());
    }
}

