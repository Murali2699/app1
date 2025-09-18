<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
require_once('/opt/lampp/htdocs/payment_automation/api/payment/helper/read_database.php');

try {

$getSchemesarraivedSQL = "select s.scheme_name ,s.subscheme_name ,s.department ,s.subdepartment,pd.scheme_id ,pd.yyyymm ,date(pd.created_ts),count(*),pd.district_code ,pd.taluk_code  from payment_details pd join schemes s on s.id = pd.scheme_id group by pd.scheme_id ,pd.yyyymm ,date(pd.created_ts),s.scheme_name ,s.subscheme_name ,s.department ,s.subdepartment,pd.district_code ,pd.taluk_code ;";

$all_scheme_export_scheme_query = "select * from all_scheme_export_scheme;";

    $getSchemeListstmt = $read_db->prepare($getSchemesarraivedSQL);
    $getSchemeListstmt->execute();
    $prepost_count = $getSchemeListstmt->rowCount();

$getSchemeListresult = [];
    if ($prepost_count >= 1) {
        
        $getSchemeListresult = $getSchemeListstmt->fetchAll(PDO::FETCH_ASSOC);


        $allSchemeListstmt = $read_db->prepare($all_scheme_export_scheme_query);
        $allSchemeListstmt->execute();
        $allexport_prepost_count = $allSchemeListstmt->rowCount();
        $allSchemeListresult = $allSchemeListstmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        $totalCountsqlQuery = "select * from fn_payment_getbenificiarieslistcount($benificiaryid,$schemeID,'".$schemename."','".$subschemename."',$idepartment,$isubdepartment,$districtcode,$talukcode,'".$jurisdictionlayercode."','".$yyyymm."','".$fromdate."','".$todate."',$paymentperiod,$approvalstatus);";

        // $insert_table_query = "INSERT INTO all_scheme_export_scheme (approval_status, count, created_ts,`date`, department, districtcode, excel_export_status, filename, jurisdiction_layer_code, path, payment_process_type, paymentperiod, scheme_id, scheme_name, subdepartment, subscheme_name, talukcode, todate, yyyymm)
        // VALUES ('approved', 10, '2024-02-14 08:30:00', '2024-02-14', 'Finance Department', 12345, 'exported', 'example.xlsx', 'layer_code_001', '/path/to/file', 'online', 1, 123, 'Sample Scheme', 'Subdepartment 1', 'Subscheme A', 67890, '2024-02-28', '202402');";


        
    
    }

    print_r($getSchemeListresult); echo '\n'; print_r($allSchemeListresult);exit;
    
} catch (PDOException $e) {
    // Handle database connection error
    echo "Connection failed: " . $e->getMessage();
} catch (Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}