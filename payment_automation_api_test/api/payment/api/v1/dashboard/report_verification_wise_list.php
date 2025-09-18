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

    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = strtolower($_POST['search']['value']);

    $qry="";
    if($searchValue!=""){
        if($type==1){
            $qry="where lower(district_name) like '%$searchValue%'";
        } else if($type==2){
            $qry="where lower(taluk_name) like '%$searchValue%'";
        } else if($type==3){
            $qry="where lower(shop_code) like '%$searchValue%'";
        }
    }

    $columnSortOrder = strtoupper($columnSortOrder);
    $ord="";
    if($columnIndex!=0){
        $ord="ORDER BY $columnName $columnSortOrder";
    }

    if (isset($district_code) && isset($taluk_code) && isset($shop_code)) {
        if($type==1){
          $benfi_count_sql = "select *, district_name as name from fn_ufcsummary_distirictwisecounts(0) $qry $ord limit ".$rowperpage."offset ".$row.";";
          $row_count_qry = "select * from fn_ufcsummary_distirictwisecounts(0)";
        } else if($type==2){
          $benfi_count_sql = "select *, taluk_name as name from fn_ufcsummary_talukwisecounts($district_code,$taluk_code) $qry $ord limit ".$rowperpage."offset ".$row.";";
          $row_count_qry = "select * from fn_ufcsummary_talukwisecounts($district_code,$taluk_code)";
        } else if($type==3){
          if($shop_code!=""){
              $benfi_count_sql = "select *, shop_code as name from fn_ufcsummary_shopwisecounts($district_code,$taluk_code,$shop_code) $qry $ord limit ".$rowperpage."offset ".$row.";";
              $row_count_qry = "select * from fn_ufcsummary_shopwisecounts($district_code,$taluk_code,$shop_code)";
          } else {
              $benfi_count_sql = "select *, shop_code as name from fn_ufcsummary_shopwisecounts($district_code,$taluk_code,'') $qry $ord limit ".$rowperpage."offset ".$row.";";
              $row_count_qry = "select * from fn_ufcsummary_shopwisecounts($district_code,$taluk_code,'')";
          }
        }
        // echo $benfi_count_sql;
        // exit;

        $benefi_count_stmt = $db->prepare($benfi_count_sql);
        $benefi_count_stmt->execute();
        $prepost_count = $benefi_count_stmt->rowCount();

        $shopwise_stmt1 = $db->prepare($row_count_qry);
        $shopwise_stmt1->execute();

        if ($prepost_count >= 1) {
            $benefi_count_result = $benefi_count_stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $data = array(
                //'qry' => $benfi_count_sql,
                'draw' => intval($draw),
                'recordsTotal' => $shopwise_stmt1->rowCount(),
                'recordsFiltered' => $shopwise_stmt1->rowCount(),
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $benefi_count_result
            );
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => $shopwise_stmt1->rowCount(),
                'recordsFiltered' => $shopwise_stmt1->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
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
