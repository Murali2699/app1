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
        $schemecategory = !empty($filters['ddl_scheme_cate']) ? $filters['ddl_scheme_cate']:"";
        $schemecode = !empty($filters['ddl_scheme_code']) ? $filters['ddl_scheme_code']:"";
        $schemename = !empty($filters['ddl_scheme_name']) ? $filters['ddl_scheme_name']:"";
        $schemename = str_replace("'","''",$schemename);
        $subschemename = !empty($filters['ddl_sub_scheme']) ? $filters['ddl_sub_scheme']:"";
        $subschemename = str_replace("'","''",$subschemename);
        $idepartment = !empty($filters['ddl_department']) ? $filters['ddl_department']:0;
        $isubdepartment = !empty($filters['ddl_sub_department']) ? $filters['ddl_sub_department']:0;
        $districtcode = !empty($filters['ddl_district']) ? $filters['ddl_district']:0;
        $talukcode = !empty($filters['ddl_taluk']) ? $filters['ddl_taluk']:0;
        $jurisdictionlayercode = "";//!empty($filters['ddl_jurisdiction']) ? $filters['ddl_jurisdiction']:"''";
        $paymnetyearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";
        $credityearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";

        $paymentfromdate = !empty($filters['from_date']) ? $filters['from_date']:'2000-01-01';
        $paymenttodate = !empty($filters['to_date']) ? $filters['to_date']:'2000-01-01';
        $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        
        $creditfromdate = !empty($filters['credited_from_date']) ? $filters['credited_from_date']:'2000-01-01';
        $credittodate = !empty($filters['credited_to_date']) ? $filters['credited_to_date']:'2000-01-01';
        $creditperiod = !empty($filters['crediteddayWeekMonthFilter']) ? $filters['crediteddayWeekMonthFilter']:0;

        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;
        $taluk_enable = isset($_POST['taluk_enable']) ? $_POST['taluk_enable']:0;
        
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        // print_r($filters);exit;
        // echo $approvalstatus;exit;

        $schemeID = 0;

        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

        $sqlQuery = $getSchemeIDsqlQuery;

        $queryStmt = $write_db->prepare($sqlQuery);
        // Bind parameters
        $queryStmt->bindParam(':schemecategory', $schemecategory, PDO::PARAM_STR);
        $queryStmt->bindParam(':schemecode', $schemecode, PDO::PARAM_STR);
        $queryStmt->bindParam(':schemename', $schemename, PDO::PARAM_STR);
        $queryStmt->bindParam(':subschemename', $subschemename, PDO::PARAM_STR);
        $queryStmt->bindParam(':idepartment', $idepartment, PDO::PARAM_INT);
        $queryStmt->bindParam(':isubdepartment', $isubdepartment, PDO::PARAM_INT);

        // Execute the query
        $queryStmt->execute();

        // Fetch the result
        $getSchemeIDqueryStmt = $queryStmt->fetch(PDO::FETCH_ASSOC);
        
        // $getschemeCount = count($getSchemeIDqueryStmt);
        // print_r($getSchemeIDqueryStmt);exit;

        if ($getSchemeIDqueryStmt !== false) {
            $GetschemeID = $getSchemeIDqueryStmt;
            $schemeID = $GetschemeID['id'];
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




        $sqlQuery = "select * from fn_payment_summary_getreasonwisedetails($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$paymnetyearcode."','".$paymentfromdate."','".$paymenttodate."',$paymentperiod) $qry;";

        $totalCountsqlQuery = "select count(*) from fn_payment_summary_getreasonwisedetails($schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$paymnetyearcode."','".$paymentfromdate."','".$paymenttodate."',$paymentperiod) $qry;";


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