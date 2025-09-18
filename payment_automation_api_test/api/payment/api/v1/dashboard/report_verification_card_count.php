<?php
require_once('../../../helper/header.php');
require_once('../../../helper/database.php');
// require_once('../../helper/mysql_database_phase1.php');


//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : '';
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : '';
    $shop_code = isset($_POST['shop_code']) ? "'".$_POST['shop_code']."'" : '';
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    if (isset($district_code) && isset($taluk_code) && isset($shop_code)) {
        if($type==1){
          $benfi_count_sql = "select * from fn_ufcsummary_districtwisecardcounts(0);";
        } else if($type==2){
          $benfi_count_sql = "select * from fn_ufcsummary_talukwisecardcounts($district_code,$taluk_code);";
        } else if($type==3){
          if($shop_code!=""){
              $benfi_count_sql = "select * from fn_ufcsummary_shopwisecardcounts($district_code,$taluk_code,$shop_code);";
          } else {
              $benfi_count_sql = "select * from fn_ufcsummary_shopwisecardcounts($district_code,$taluk_code,'');";
          }
        }
        // echo $benfi_count_sql;
        // exit;

        $benefi_count_stmt = $db->prepare($benfi_count_sql);
        $benefi_count_stmt->execute();
        $prepost_count = $benefi_count_stmt->rowCount();
        if ($prepost_count >= 1) {
            $benefi_count_result = $benefi_count_stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $data = array("success" => 1, "message" => "Data Found", 'data' => $benefi_count_result);
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array("success" => 0, "message" => "Data Not Found");
            echo json_encode($data);
            die();
        }
        $db=null;
    }
} 
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}
