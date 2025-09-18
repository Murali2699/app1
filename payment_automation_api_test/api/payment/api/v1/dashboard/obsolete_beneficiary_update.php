<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
$request_dt = date("Y-m-d H:i:s.u");
$start_time = microtime(true);
$curl = curl_init();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // parse_str($_POST['data'], $output);  

    $userid = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $schemeid = isset($_POST['']) ? (int) $_POST[''] : 0;
    $benificiaryid = isset($_POST['txt_update_beneficiary_id']) ? (int) $_POST['txt_update_beneficiary_id'] : 0;
    $obsoletetype = isset($_POST['update_obsolete_type']) ? (int) $_POST['update_obsolete_type'] : 0;
    $obsoleteremarks = isset($_POST['update_remarks']) ?  $_POST['update_remarks'] : '';
    $status = false;
    $lbl_scheme_name = isset($_POST['lbl_scheme_name']) ?  $_POST['lbl_scheme_name'] : '';
     

    if (empty($benificiaryid) || empty($obsoletetype)) {
        die(json_encode(['success' => false, 'message' => 'Beneficiary ID and Obsolete Type are required']));
    }

// Function to update obsolete beneficiary
    try {
        // Call the stored procedure
        $query = "CALL public.sp_payment_updateobsoletebenificiary(?,?,?,?,?,?)";
        $stmt = $write_db->prepare($query);

        $stmt->bindParam(1, $userid, PDO::PARAM_INT);
        $stmt->bindParam(2, $schemeid, PDO::PARAM_INT);
        $stmt->bindParam(3, $benificiaryid, PDO::PARAM_INT);
        $stmt->bindParam(4, $obsoletetype,  PDO::PARAM_INT);
        $stmt->bindParam(5, $obsoleteremarks, PDO::PARAM_STR);        
        $stmt->bindParam(6, $status, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        $response = array('success' => 1, "message" => "Obsolete beneficiary updated Successfully","data"=>$result);
        echo json_encode($response);
        $write_db = null;
        die();
    } catch (PDOException $err) {
        $data =[];
        $error = $err->errorInfo;
        $splitted_error = substr($error[2], 0, strpos($error[2], "\n"));
        http_response_code(405);
        array_push($data, array("success" => 0, "message" => 'Backend Error'.$splitted_error,'Error' => $error));
        echo json_encode($data);

        $response_status_code = http_response_code(405);
        $response_dt = date("Y-m-d H:i:s.u");
        $response_time_ms = microtime(true) - $start_time;
        $write_db = null;
        die();
    }
}
?>
