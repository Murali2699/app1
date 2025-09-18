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

        $benificiaryid = !empty($_POST['ddl_beeificiary']) ? $_POST['ddl_beeificiary']:0;
        $schemecate = !empty($_POST['ddl_scheme_cate']) ? $_POST['ddl_scheme_cate']:"";
        $schemecode = !empty($_POST['ddl_scheme_code']) ? $_POST['ddl_scheme_code']:"";
        $schemename = !empty($_POST['ddl_scheme_name']) ? $_POST['ddl_scheme_name']:"";
        $schemename = str_replace("'","''",$schemename);

        $subschemename = !empty($_POST['ddl_sub_scheme']) ? $_POST['ddl_sub_scheme']:"";
        $subschemename = str_replace("'","''",$subschemename);

        $idepartment = !empty($_POST['ddl_department']) ? $_POST['ddl_department']:0;
        $isubdepartment = !empty($_POST['ddl_sub_department']) ? $_POST['ddl_sub_department']:0;
        $districtcode = !empty($_POST['ddl_district']) ? $_POST['ddl_district']:0;
        $talukcode = !empty($_POST['ddl_taluk']) ? $_POST['ddl_taluk']:0;
        $jurisdictionlayercode = "";//!empty($_POST['ddl_jurisdiction']) ? $_POST['ddl_jurisdiction']:"''";
        $paymentperiodmonth = !empty($_POST["ddl_month"]) ? $_POST["ddl_month"]:0;
        $paymentperiodyear = !empty($_POST["ddl_year"]) ? $_POST["ddl_year"]:0;
        $fromdate = !empty($_POST['from_date']) ? $_POST['from_date']:'2000-01-01';
        $todate = !empty($_POST['to_date']) ? $_POST['to_date']:'2000-01-01';

        $paymentdatefrom = !empty($_POST['from_date']) ? $_POST['from_date']:'2000-01-01';
        $paymentdateto = !empty($_POST['to_date']) ? $_POST['to_date']:'2000-01-01';

        $financialQuarterly = !empty($_POST['ddl_financialQuarterly']) ? $_POST['ddl_financialQuarterly']:0;
        $calendarQuarterly = !empty($_POST['ddl_calendarQuarterly']) ? $_POST['ddl_calendarQuarterly']:0;
        $calendar_half_yearly = !empty($_POST['ddl_calendar_half_yearly']) ? $_POST['ddl_calendar_half_yearly']:0;
        $financial_half_yearly = !empty($_POST['ddl_financial_half_yearly']) ? $_POST['ddl_financial_half_yearly']:0;
        
        $paymentperiodtype = !empty($_POST['dayWeekMonthFilter']) ? $_POST['dayWeekMonthFilter']:0;
        $approvalstatus = isset($_POST['approveStatus']) ? $_POST['approveStatus']:0;

        $installmentid = !empty($_POST['ddl_payment_installment']) ? $_POST['ddl_payment_installment']:0;
        $yearmonth =!empty($_POST['ddl_year']) ? $_POST['ddl_year']:0;
        $paymentdate = !empty($_POST['payment_date']) ? $_POST['payment_date']:"2000-01-01";
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

        $schemeID = 0;
        $getSchemeIDqueryStmt = $read_db->prepare($getSchemeIDsqlQuery);
        $getSchemeIDqueryStmt->execute();
        $schemeIDprepost_count = $getSchemeIDqueryStmt->rowCount();
        
        if ($schemeIDprepost_count == 1) {
            $GetschemeID = $getSchemeIDqueryStmt->fetchAll(PDO::FETCH_ASSOC);
            $schemeID = $GetschemeID[0]['id'];
        }
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
        
        //  echo $sqlQuery; exit;
        $action ='';

        $queryStmt = $read_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
        
        if ($prepost_count >= 1 || !empty($yearmonth)){
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
            "message" => "Details was not Found for the Given Period"
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