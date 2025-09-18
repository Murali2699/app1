<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schemeCategory = isset($_POST['schemeCate']) ? $_POST['schemeCate'] : '';
    $yearMonth = isset($_POST['yearMonth']) ? $_POST['yearMonth'] : 0;
    // $yearMonth = intval($yearMonth);

    $draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
    $row = isset($_POST['start']) ? $_POST['start'] : 0;
    $rowperpage = isset($_POST['length']) ? $_POST['length'] : 10;
    $columnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
    $columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : '';
    $columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    $querySql = "SELECT scheme_category, yyyymm, total_beneficiaries, total_amount, payment_success_count, payment_failure_count, payment_inprogress_count, payment_success_amount, payment_failure_amount, payment_inprogress_amount FROM fn_summary_getdbtpyamentsummary(:schemecategory, :yyyymm)";

    if (!empty($schemeCategory) && empty($yearMonth)) {
        // Filter only by scheme category
        $querySql .= " WHERE scheme_category = :schemecategory";
    } elseif (!empty($yearMonth)) {
        // Filter by year month
        $querySql .= " WHERE yyyymm = :yyyymm";
    }

    $queryStmt = $read_db->prepare($querySql);
    $queryStmt->bindParam(':schemecategory', $schemeCategory, PDO::PARAM_STR);
    $queryStmt->bindParam(':yyyymm', $yearMonth, PDO::PARAM_INT);

    $queryStmt->execute();
    $data = $queryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare and execute the count query to check if there is any data
    $countQuerySql = "SELECT COUNT(*) FROM fn_summary_getdbtpyamentsummary(:schemecategory, :yyyymm)";
    $countQueryStmt = $read_db->prepare($countQuerySql);
    $countQueryStmt->bindParam(':schemecategory', $schemeCategory, PDO::PARAM_STR);
    $countQueryStmt->bindParam(':yyyymm', $yearMonth, PDO::PARAM_INT);
    $countQueryStmt->execute();
    $rowCount = $countQueryStmt->fetchColumn();

    if ($rowCount > 0) {
        // Data found for the selected period
        http_response_code(200);
        echo json_encode(array(
            'success' => true,
            'data' => $data
        ));
        exit;
    } else {
        // No data found for the selected period
        http_response_code(200);
        echo json_encode(array(
            'success' => false,
            'message' => 'No data available for the selected period.'
        ));
        exit;
    }
} else {
    // Handle invalid request method
    http_response_code(405);
    echo json_encode(array(
        'success' => false,
        'message' => 'Method Not Allowed'
    ));
    exit;
}
