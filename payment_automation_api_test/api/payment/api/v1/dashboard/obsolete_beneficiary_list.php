<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

error_reporting(error_reporting() & ~E_WARNING);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    // $schemeCategory = isset($_POST['ddl_scheme_cate']) ? $_POST['ddl_scheme_cate'] : '';
    // $scheme_id = isset($_POST['ddl_scheme_code']) ? $_POST['ddl_scheme_code'] : '';

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
        OR lower(scheme_name) LIKE '%$searchValue%'z
        OR lower(subscheme_name) LIKE '%$searchValue%'
    ";
    }

    $whereConditions = [];
    if (!empty($_POST['fileters'])) {
        $arrCase = [
            "filter_scheme_category" => "scheme_category like",
            "filter_scheme_code" => "and scheme_code like",
            "filter_scheme_name" => "and scheme_name like",
            "filter_subscheme" => "and subscheme_name like",
            "filter_deparment" => "and department =",
            "filter_sub_deparment" => "and subdepartment =",
        ];

        foreach ($_POST['fileters'] as $filter) {
            $filter_value = str_replace("'", "''", $filter['inputValue']);
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

        $sqlQuery = "SELECT * FROM fn_scheme_getschemesbyuser(" . $user_id . ")  LIMIT $rowperpage OFFSET $row;";
        $countQuery = "SELECT COUNT(*) FROM fn_scheme_getschemesbyuser(" . $user_id . ")";
        $action = '';

        $querySt = $read_db->prepare($sqlQuery);
        $querySt->execute();

        $filter = $querySt->fetchAll(PDO::FETCH_ASSOC);
        // print_r($filter); exit;
        $countQryStmt = $read_db->prepare($countQuery);
        $countQryStmt->execute();



        $querySql = "SELECT * FROM fn_payment_getobsoletebenificiaries(:schemecategory, :schemeid, :schemename, :subschemename, :idepartment, :isubdepartment)  LIMIT :rowperpage OFFSET :row";
        $countQuerySql = "SELECT COUNT(*) FROM fn_payment_getobsoletebenificiaries(:schemecategory, :schemeid, :schemename, :subschemename, :idepartment, :isubdepartment)";

        $queryStmt = $read_db->prepare($querySql);
        $queryStmt->bindParam(':schemecategory', $_POST['ddl_scheme_cate'], PDO::PARAM_STR);
        $queryStmt->bindParam(':schemeid', $_POST['ddl_scheme_code'], PDO::PARAM_INT);
        $queryStmt->bindParam(':schemename', $_POST['ddl_scheme_name'], PDO::PARAM_STR);
        $queryStmt->bindParam(':subschemename', $_POST['ddl_sub_scheme'], PDO::PARAM_STR);
        $queryStmt->bindParam(':idepartment', $_POST['ddl_department'], PDO::PARAM_INT);
        $queryStmt->bindParam(':isubdepartment', $_POST['ddl_sub_department'], PDO::PARAM_INT);
        $queryStmt->bindParam(':rowperpage', $rowperpage, PDO::PARAM_INT);
        $queryStmt->bindParam(':row', $row, PDO::PARAM_INT);

        // Execute the query
        $queryStmt->execute();
        // print_r($queryStmt);exit;
        $countQryStmt = $read_db->prepare($countQuerySql);
        $countQryStmt->bindParam(':schemecategory', $_POST['ddl_scheme_cate'], PDO::PARAM_STR);
        $countQryStmt->bindParam(':schemeid', $_POST['ddl_scheme_code'], PDO::PARAM_INT);
        $countQryStmt->bindParam(':schemename', $_POST['ddl_scheme_name'], PDO::PARAM_STR);
        $countQryStmt->bindParam(':subschemename', $_POST['ddl_sub_scheme'], PDO::PARAM_STR);
        $countQryStmt->bindParam(':idepartment', $_POST['ddl_department'], PDO::PARAM_INT);
        $countQryStmt->bindParam(':isubdepartment', $_POST['ddl_sub_department'], PDO::PARAM_INT);
        $countQryStmt->execute();

        $prepost_count = $countQryStmt->fetchColumn();

        // echo $prepost_count; exit;

        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);

            // print_r($complaintResult); exit;    

            foreach ($complaintResult as $key => $scheme) {
                if ($scheme['is_active'] === true) {

                    $complaintResult[$key]['action'] = '<a href="javascript:void(0);" class="badge text-bg-success c-pointer" data-bs-toggle="modal" data-bs-target="#updateObsoleteModal" onclick="showObsoleteBeneficiaryDetails(' . htmlspecialchars(json_encode($scheme['is_active']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['scheme_name']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['benificiary_id']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['obsolete_type']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['obsolete_remarks']), ENT_QUOTES) . ')"><i class="bx bxs-x-circle"></i> Unblock</a>';
                } else {
                    $complaintResult[$key]['action'] = '<a href="javascript:void(0);" class="badge text-bg-danger c-pointer" data-bs-toggle="modal" data-bs-target="#updateObsoleteModal" onclick="showObsoleteBeneficiaryDetails(' . htmlspecialchars(json_encode($scheme['is_active']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['scheme_name']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['benificiary_id']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['obsolete_type']), ENT_QUOTES) . ',' . htmlspecialchars(json_encode($scheme['obsolete_remarks']), ENT_QUOTES) . ')"><i class="bx bxs-check-circle"></i> Block</a>';
                }
            }

            http_response_code(200);
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $prepost_count,
                'recordsFiltered' => $prepost_count,
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
                'recordsTotal' => $prepost_count,
                'recordsFiltered' => $prepost_count,
                "success" => 0,
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $read_db = null;
    }
} else {
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}

function maskPhoneNumber($phoneNumber)
{
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



function getDepartment()
{
    try {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
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


function getSubDepartment($department_id)
{
    try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
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
