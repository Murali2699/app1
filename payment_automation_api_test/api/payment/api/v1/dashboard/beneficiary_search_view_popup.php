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

    $scheme_id = isset($_POST['scheme_id']) ? $_POST['scheme_id'] : 0;
    $beneficiarid = isset($_POST['beneficiarid']) ? $_POST['beneficiarid'] : 0;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;


    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = strtolower($_POST['search']['value']);

    $qry="";
    if($searchValue!=""){
        $qry="where lower(scheme_category) like '%$searchValue%'
        OR lower(district_name) like '%$searchValue%'
        OR lower(scheme_name) like '%$searchValue%'
        OR lower(scheme_code) like '%$searchValue%'
        OR lower(taluk_name) like '%$searchValue%'
        OR lower(subscheme_name) like '%$searchValue%'";
    }


            $columnSortOrder = strtoupper($columnSortOrder);
            $ord="";
            if($columnIndex!=0){
                $ord="ORDER BY $columnName $columnSortOrder";
            }else{
                $ord = "ORDER BY id ASC";
            }


    
            $sqlQuery = "select * from fn_payment_getbeneficiarypaymentdetails($scheme_id,'".$beneficiarid."',$user_id) $qry;";

            $totalCountsqlQuery = "select count(*) from fn_payment_getbeneficiarypaymentdetails($scheme_id,'".$beneficiarid."',$user_id) $qry;";


        $totalStmt = $write_db->prepare($totalCountsqlQuery);
        $totalStmt->execute();
        $totalCount = $totalStmt->fetchAll(PDO::FETCH_ASSOC);

        // print_r($totalCount);exit;
        

        $action ='';

        $queryStmt = $write_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            
            
            http_response_code(200);
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            die();
        } else {
            $totalCount[0]['count'] = 0;
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['count'],//$countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
        $write_db=null;
    
}
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}