<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');
require_once('../../../helper/read_database_two.php');

error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;
    $taluk_code = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : 0;
    $shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;

    /*form filter datas*/
        $rawString = str_replace('"','',$_POST['fileters']);
        parse_str($rawString, $filters);
        $benificiaryid = !empty($filters['ddl_beneficiary_id']) ? $filters['ddl_beneficiary_id']:0;
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
        $iyearcode = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:"";

        $paymentperiod = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        if($paymentperiod == 0){
            $fromdate = !empty($filters['from_date']) ? $filters['from_date']:'2000-01-01';
            $todate = !empty($filters['to_date']) ? $filters['to_date']:'2000-01-01';
        }else{
            $fromdate = '2000-01-01';
            $todate = '2000-01-01';
        }

        
        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;
        $rejected_reason = !empty($_POST['rejected_reason']) ? $_POST['rejected_reason']:"";
        $reason_code = !empty($_POST['reason_code']) ? $_POST['reason_code']:"";
        

        $yearString = !empty($filters['ddl_year']) ? $filters['ddl_year']:'';
        $mothString = !empty($filters['ddl_month']) ? $filters['ddl_month']:'';
        $yyyymm = '';

        
        if($paymentperiod == 4){
            $paymentperiod = 0;
            $yyyymm = $yearString.$mothString;
        }
        

        // echo $approvalstatus;exit;

        /* get scheme id*/
        // $getSchemeIDsqlQuery = "select id from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code like '".$schemecode."' and scheme_name like '".$schemename."' and subscheme_name like '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;

        // $getSchemeIDqueryStmt = $write_db->prepare($getSchemeIDsqlQuery);
        // $getSchemeIDqueryStmt->execute();
        // $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();

        $schemeID = 0;

        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

        $sqlQuery = $getSchemeIDsqlQuery;

        $queryStmt = $read_db->prepare($sqlQuery);
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
        
        // print_r($getSchemeIDqueryStmt);exit;
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
    

    if($approvalstatus==4 && ($rejected_reason != null || $rejected_reason!='')){
        if($searchValue!="" && $rejected_reason !=''){
            $qry="where lower(benificiary_mobile_no) like '%$searchValue%'
            OR lower(benificiary_name) like '%$searchValue%'
            OR lower(payment_mode_name) like '%$searchValue%'
            OR lower(rejection_reason_code) like '%$searchValue%'
            OR rejection_reason_code like '$rejected_reason'
            OR lower(benificiary_reference_id) like '%$searchValue%'"; 
        }elseif($rejected_reason !='' && $searchValue==""){
            $qry="where lower(benificiary_mobile_no) like '$searchValue'
            OR lower(benificiary_name) like '$searchValue'
            OR lower(payment_mode_name) like '$searchValue'
            OR lower(rejection_reason_code) like '$searchValue'
            OR rejection_reason_code like '$rejected_reason'
            OR lower(benificiary_reference_id) like '$searchValue'"; 
        }else{
            $qry="where lower(benificiary_mobile_no) like '%$searchValue%'
            OR lower(benificiary_name) like '%$searchValue%'
            OR lower(payment_mode_name) like '%$searchValue%'
            OR lower(rejection_reason_code) like '%$searchValue%'
            OR lower(benificiary_reference_id) like '%$searchValue%'";
        }
    }else{
        if($searchValue!=""){
            $qry="where lower(benificiary_mobile_no) like '%$searchValue%'
            OR lower(benificiary_name) like '%$searchValue%'
            OR lower(payment_mode_name) like '%$searchValue%'
            OR lower(benificiary_reference_id) like '%$searchValue%'";  
        }
    }


    $columnSortOrder = strtoupper($columnSortOrder);
    $ord="";
    if($columnIndex!=0){
        $ord="ORDER BY $columnName $columnSortOrder";
    }else{
        $ord = "ORDER BY id ASC";
    }

        if($rejected_reason !=''){
            $totalCountsqlQuery = "select * from fn_payment_getbenificiarieslistcount($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,'".$reason_code."');";
            
        }else{
            $sqlQuery = "select * from fn_payment_getbenificiariesforapproval($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,$rowperpage,$row,'".$reason_code."') $qry;";//$ord limit ".$rowperpage." offset ".$row.";";
            // echo $sqlQuery;exit;

            $totalCountsqlQuery = "select * from fn_payment_getbenificiarieslistcount($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,'".$reason_code."');";
        }
        // echo$sqlQuery;exit;
        // echo $sqlQuery;exit;
    
        // $sqlQuery = "select * from fn_payment_getbenificiariesforapproval($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,$rowperpage,$row) $qry;";//$ord limit ".$rowperpage." offset ".$row.";";
        // // echo $sqlQuery;exit;

        // $totalCountsqlQuery = "select * from fn_payment_getbenificiarieslistcount($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus);";


        // change read db
        if($approvalstatus == 1){
            $totalStmt = $read_db->prepare($totalCountsqlQuery);
        }elseif($approvalstatus == 2){
            $totalStmt = $read_db_two->prepare($totalCountsqlQuery);
        }elseif($approvalstatus == 3){
            $totalStmt = $read_db->prepare($totalCountsqlQuery);
        }elseif($approvalstatus == 4){
            $totalStmt = $read_db_two->prepare($totalCountsqlQuery);
        }elseif($approvalstatus == 5){
            $totalStmt = $read_db->prepare($totalCountsqlQuery);
        }elseif($approvalstatus == 6){
            $totalStmt = $read_db_two->prepare($totalCountsqlQuery);
        }else{
            $totalStmt = $read_db->prepare($totalCountsqlQuery);
        }

        $totalStmt->execute();
        $totalCount = $totalStmt->fetchAll(PDO::FETCH_ASSOC);
        $reuseableTotalCount = $totalCount[0]['total_benificiaries'];

        $action ='';
        if($rejected_reason !=''){
            $totalRowperpage = $totalCount[0]['total_benificiaries'];
            $sqlQuery = "select * from fn_payment_getbenificiariesforapproval($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$iyearcode."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,$totalRowperpage,0,'".$reason_code."') $qry limit $rowperpage offset $row;";
        }
        // echo $sqlQuery;exit;

        if($approvalstatus == 1){
            $queryStmt = $read_db->prepare($sqlQuery);
        }elseif($approvalstatus == 2){
            $queryStmt = $read_db_two->prepare($sqlQuery);
        }elseif($approvalstatus == 3){
            $queryStmt = $read_db->prepare($sqlQuery);
        }elseif($approvalstatus == 4){
            $queryStmt = $read_db_two->prepare($sqlQuery);
        }elseif($approvalstatus == 5){
            $queryStmt = $read_db->prepare($sqlQuery);
        }elseif($approvalstatus == 6){
            $queryStmt = $read_db_two->prepare($sqlQuery);
        }else{
            $queryStmt = $read_db_two->prepare($sqlQuery);
        }
        
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        
        if ($prepost_count >= 1) {
            $paymentProcess = $queryStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($paymentProcess as $key => $scheme) {
                
                $paymentProcess[$key]['action'] ='<a href="javascript:void(0);" class="badge text-bg-success c-pointer" data-bs-toggle="modal" data-bs-target=".viewdatainfo" onclick="viewScheme('.$scheme['id'].')"><i class="bi bi-eye"></i> view</a>';

                if($approvalstatus == 1){
                    $preEncryptString = $scheme['id'] . ',' . $scheme['scheme_id'] . ',' . $scheme['yyyymm'] . ',' . $scheme['benificiary_id'] . ',' . $scheme['installment_id'];
                    $encryptionKey = 'jfEqTcM_BP0!/<Kc0)W}|Ap$_q(nyu21';
                    $encrypted = encryptString($preEncryptString, $encryptionKey);

                $paymentProcess[$key]['checkbox'] = '<input class="form-check-input" type="checkbox" name="pending_check[]" value="'.$encrypted.'">';
                }
                if($approvalstatus == 4){
                    
                    $preEncryptString = $scheme['id'] . ',' . $scheme['scheme_id'] . ',' . $scheme['yyyymm'] . ',' . $scheme['benificiary_id'] . ',' . $scheme['installment_id'];
                    $encryptionKey = 'jfEqTcM_BP0!/<Kc0)W}|Ap$_q(nyu21';
                    $encrypted = encryptString($preEncryptString, $encryptionKey);

                    $paymentProcess[$key]['checkbox'] = '<input class="form-check-input" type="checkbox" name="failed_check[]" value="'.$encrypted.'">';
                }
                if($approvalstatus == 5){
                    $paymentProcess[$key]['checkbox'] = '<input class="form-check-input" type="checkbox" name="pre_file_generated_check[]" value="'.$scheme['id'].'">';
                }

                $mobilenumber = $paymentProcess[$key]['benificiary_mobile_no'];
                $benificiary_id = $paymentProcess[$key]['benificiary_id'];
                unset($paymentProcess[$key]['benificiary_mobile_no']);
                unset($paymentProcess[$key]['benificiary_id']);

                $paymentProcess[$key]['benificiary_mobile_no'] =  maskPhoneNumber($mobilenumber);
                $paymentProcess[$key]['benificiary_id'] =  maskBeneficiaryID($benificiary_id);
            }

            
            http_response_code(200);
            if($rejected_reason !=''){
                $totalCount[0]['total_benificiaries'] = $prepost_count;
            }
            $data = array(
                //'qry' => $sqlQuery,
                'draw' => intval($draw),
                'recordsTotal' => $totalCount[0]['total_benificiaries'],//$countQryStmt->rowCount(),
                'recordsFiltered' => $totalCount[0]['total_benificiaries'],//$countQryStmt->rowCount(),
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $paymentProcess
                
            );

            if($approvalstatus == 4){
                $rowperpage =  $reuseableTotalCount;
                $payment_rejected_reasons_query="select rejection_reason,rejection_reason_code from fn_payment_getbenificiariesforapproval($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$yyyymm."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus,$rowperpage,$row,'') where rejection_reason_code is not null group by rejection_reason,rejection_reason_code";

                // echo $payment_rejected_reasons_query; exit;

                    $queryStmt = $read_db->prepare($payment_rejected_reasons_query);
                    $queryStmt->execute();
                    $rejected_reasons = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($rejected_reasons)){
                        $data['rejected_reson']= $rejected_reasons;
                    }else{
                        $data['rejected_reson']= null;
                    }
            }

            
            echo json_encode($data);
            die();
        } else {
            http_response_code(200);
            $data = array(
                'draw' => intval($draw),
                'recordsTotal' => $prepost_count,//$countQryStmt->rowCount(),
                'recordsFiltered' => $prepost_count,//$countQryStmt->rowCount(),
                "success" => 0, 
                "message" => "Data Not Found"
            );
            echo json_encode($data);
            die();
        }
        $read_db=null;
        $read_db_two=null;
}
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}

function maskPhoneNumber($phoneNumber) {
  $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
  if (strlen($phoneNumber) >= 10) {
      $maskedNumber = substr($phoneNumber, 0, 1) . str_repeat('*', strlen($phoneNumber) - 7) . substr($phoneNumber, 4, 1) . str_repeat('*', strlen($phoneNumber) - 6) . substr($phoneNumber, 9);
      return $maskedNumber;
  } else {
      return $phoneNumber;
  }
}

function maskBeneficiaryID($beneficiaryID) {
    $beneficiary_id = preg_replace('/[^0-9]/', '', $beneficiaryID);
    $beneficiaryIdStringLength = strlen($beneficiary_id);
    if ($beneficiaryIdStringLength >= 12) {
        $maskedNumber = substr($beneficiary_id, 0, 1) . str_repeat('*', strlen($beneficiary_id) - 9) . substr($beneficiary_id, 6, 1) . str_repeat('*', strlen($beneficiary_id) - 8) . substr($beneficiary_id, 12);
        return $maskedNumber;
    } else {
        return $beneficiary_id;
    }
  }

  function encryptString($string, $encryptionKey) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedString = openssl_encrypt($string, 'aes-256-cbc', $encryptionKey, 0, $iv);
    return base64_encode($encryptedString . '::' . $iv);
}