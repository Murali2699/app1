<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // print_r($_POST);exit;
    $upload_reference_no = !empty($_POST['upload_reference_no']) ? $_POST['upload_reference_no']:0;
    $upload_reference_no_int= intval($upload_reference_no);
        $getBeneficiaryUploadStatusSqlQuery = "SELECT COUNT(*) FROM payment_details_temp WHERE upload_reference_no = $upload_reference_no_int";

        $query_stmt = $write_db->prepare($getBeneficiaryUploadStatusSqlQuery);
        $query_stmt->execute();
        $count_result = $query_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($count_result[0]['count'] >= 0) {
        $response = array(
            "success" => true,
            "message" => "count is processing",
            "data" => $count_result[0]
        );
    }else{
        $$count_result['count'] = 0;

        $response = array(
            "success" => false,
            "message" => "not found",
            "data" => $$count_result
        );
    }

        echo json_encode($response);
        die();


    $write_db = null;
}else{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}



function validateCSVStructure($fileInfo, $expectedColumns) {
    
    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        return "File upload failed with error code: " . $fileInfo['error'];
    }

    $csvContent = file_get_contents($fileInfo['tmp_name']);
    $rows = explode(PHP_EOL, $csvContent);
    $headerRow = str_getcsv(array_shift($rows));

    if ($headerRow !== $expectedColumns) {
        return "CSV columns do not match the expected columns !";
    }

    return TRUE;
}

?>
