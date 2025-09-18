<?php
require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
$request_dt = date("Y-m-d H:i:s.u");
$start_time = microtime(true);
$curl = curl_init();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// $userID = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
// $schemeID = isset($_POST['scheme_id']) ? $_POST['scheme_id'] : 0;
// $beneficiaryID = isset($_POST['beneficiary_id']) ? $_POST['beneficiary_id'] : 0;
// $obsoleteType = isset($_POST['obsolete_type']) ? $_POST['obsolete_type'] : 0;
// $obsoleteRemarks = isset($_POST['obsolete_remarks']) ? $_POST['obsolete_remarks'] : '';
// $status = !empty($_POST['']) ? $_POST[''] : '';

    $userid = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $schemeid = isset($_POST['']) ? (int) $_POST[''] : 0;
    $benificiaryid = isset($_POST['beneficiary_id']) ? (int) $_POST['beneficiary_id'] : 0;
    $obsoletetype = isset($_POST['add_obsolete_type']) ? (int) $_POST['add_obsolete_type'] : 0;
    $obsoleteremarks = isset($_POST['add_remarks']) ?  $_POST['add_remarks'] : '';
    $status = false;
    $lbl_scheme_name = isset($_POST['ddl_ischeme_name']) ?  $_POST['ddl_ischeme_name'] : '';

try {
    $sql = "CALL public.sp_payment_createobsoletebenificiary(?,?,?,?,?,?)";
    $stmt = $write_db->prepare($sql);

    $stmt->bindParam(1, $userid, PDO::PARAM_INT);
    $stmt->bindParam(2, $schemeid, PDO::PARAM_INT);
    $stmt->bindParam(3, $benificiaryid, PDO::PARAM_INT);
    $stmt->bindParam(4, $obsoletetype,  PDO::PARAM_INT);
    $stmt->bindParam(5, $obsoleteremarks, PDO::PARAM_STR);        
    $stmt->bindParam(6, $status, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    http_response_code(200);
    $response = array('success' => 1, "message" => "Obsolete beneficiary add Successfully","data"=>$result);
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
