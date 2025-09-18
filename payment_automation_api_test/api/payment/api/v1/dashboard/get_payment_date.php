<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

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
        $benificiaryid = !empty($filters['ddl_beeificiary']) ? $filters['ddl_beeificiary']:0;
        $schemecate = !empty($filters['ddl_scheme_cate']) ? $filters['ddl_scheme_cate']:"";
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
        $paymentperiodmonth = !empty($filters["ddl_month"]) ? $filters["ddl_month"]:0;
        $paymentperiodyear = !empty($filters["ddl_year"]) ? $filters["ddl_year"]:0;
        $fromdate = !empty($filters['from_date']) ? $filters['from_date']:'2000-01-01';
        $todate = !empty($filters['to_date']) ? $filters['to_date']:'2000-01-01';

        $paymentdatefrom = !empty($filters['from_date']) ? $filters['from_date']:'2000-01-01';
        $paymentdateto = !empty($filters['to_date']) ? $filters['to_date']:'2000-01-01';

        $financialQuarterly = !empty($filters['ddl_financialQuarterly']) ? $filters['ddl_financialQuarterly']:0;
        $calendarQuarterly = !empty($filters['ddl_calendarQuarterly']) ? $filters['ddl_calendarQuarterly']:0;
        $calendar_half_yearly = !empty($filters['ddl_calendar_half_yearly']) ? $filters['ddl_calendar_half_yearly']:0;
        $financial_half_yearly = !empty($filters['ddl_financial_half_yearly']) ? $filters['ddl_financial_half_yearly']:0;
        
        $paymentperiodtype = !empty($filters['dayWeekMonthFilter']) ? $filters['dayWeekMonthFilter']:0;
        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;

        $installmentid = !empty($filters['installment_txt_value']) ? $filters['installment_txt_value']:0;
        $yearmonth =!empty($filters['ddl_year']) ? $filters['ddl_year']:0;
        $paymentdate = !empty($filters['payment_date']) ? $filters['payment_date']:"2000-01-01";
        // echo $approvalstatus;exit;



        /* get scheme id   fn_scheme_getschemesbyuser(".$user_id.")*/
        if($schemecate =='' && $schemecode =='' && $schemename =='' && $subschemename =='' && $idepartment ==0 && $isubdepartment ==0){
            $whitchCondition = ' if is runing';
            $getSchemeIDsqlQuery = "select id from fn_scheme_getschemesbyuser(".$user_id.");";

        }elseif($schemecate !='' && $schemecode =='' && $schemename =='' && $subschemename =='' && $idepartment ==0 && $isubdepartment ==0){
            $whitchCondition = ' else if is runing';
            $getSchemeIDsqlQuery = "select id from fn_scheme_getschemesbyuser(".$user_id.") where scheme_category like '".$schemecate."';";
        }
        else{
            $whitchCondition = ' else is runing';
            $getSchemeIDsqlQuery = "select id from fn_scheme_getschemesbyuser(".$user_id.") where scheme_category like '".$schemecate."' and scheme_code like '".$schemecode."' and scheme_name like '".$schemename."' and subscheme_name like '".$subschemename."' and  department = ".$idepartment."and subdepartment =".$isubdepartment;

        }



        // echo $getSchemeIDsqlQuery;exit;


        $schemeID = 0;
        $getSchemeIDqueryStmt = $read_db->prepare($getSchemeIDsqlQuery);
        $getSchemeIDqueryStmt->execute();
        $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
        
        if ($schemeIDprepost_count == 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
        }

        // payment period type

        // print_r($filters);exit;

        if($paymentperiodtype=='1'){
            $paymentperiodtype = 1;
        }elseif($paymentperiodtype =='14'){
            $paymentperiodtype = 14;
        }elseif($paymentperiodtype =='fq'){
            $paymentperiodtype = $financialQuarterly;
        }elseif($paymentperiodtype =='cq'){
            $paymentperiodtype = $calendarQuarterly;
        }elseif($paymentperiodtype =='fh'){
            $paymentperiodtype = $financial_half_yearly;
        }elseif($paymentperiodtype =='ch'){
            $paymentperiodtype = $calendar_half_yearly ;
        }
        

        // print_r($schemeID);exit;
    // old query
        // $sqlQuery = "select * from fn_payment_summary_getmaindashboardpaymentdatedetails('".$schemecate."',$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."',$paymentperiodtype,$paymentperiodmonth,$paymentperiodyear,'".$paymentdatefrom."','".$paymentdateto."');";

        $sqlQuery = "select * from fn_payment_summary_getmaindashboardpaymentdatedetails('".$schemecate."',$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,$installmentid,$yearmonth,$user_id);";
        
        // echo $sqlQuery; exit;
        $action ='';

        $queryStmt = $read_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            $data = array(
                "success" => 1,
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            die();
        $read_db=null;
    }else{
        http_response_code(200);
        $data = array(
            "success" => 0, 
            "message" => "Payment was not Done for the Given Period" //Payment Were Not Done Not the Selected Period
        );
        echo json_encode($data);
        die();
    }
}
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}