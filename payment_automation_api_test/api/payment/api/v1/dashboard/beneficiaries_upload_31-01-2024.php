<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
ini_set('memory_limit', '-1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

// print_r($_POST);
// print_r($_FILES);
// exit;

// print_r($_POST);exit;

    $expectedColumns = ['benificiary_id','installment_id','benificiary_reference_id','benificiary_name','benificiary_mobile_no','adhaar_no','amount','payment_mode_id','district_code','taluk_code','jurisdiction_layer_code','jurisdiction_code','benificiary_account_no','benificiary_bank_name','benificiary_ifsc_code','benificiary_iin_no'];


    $validateResult = validateCSVStructure($_FILES['beneficiary_csv_file'], $expectedColumns);
    if($validateResult!==true){
            $fileStucturerror = array(
                "success" => false,
                "message" => $validateResult,
            );
            echo  json_encode($fileStucturerror,true);
            return false;
    }

    
    // Print the contents of $_POST

    $schemecategory = !empty($_POST['ddl_scheme_cate']) ? $_POST['ddl_scheme_cate']:"";
    $schemecode = !empty($_POST['ddl_scheme_code']) ? $_POST['ddl_scheme_code']:"";
    $schemename = !empty($_POST['ddl_scheme_name']) ? $_POST['ddl_scheme_name']:"";
    $schemename = str_replace("'","''",$schemename);
    $subschemename = !empty($_POST['ddl_sub_scheme']) ? $_POST['ddl_sub_scheme']:"";
    $subschemename = str_replace("'","''",$subschemename);
    $idepartment = !empty($_POST['ddl_department']) ? $_POST['ddl_department']:0;
    $isubdepartment = !empty($_POST['ddl_sub_department']) ? $_POST['ddl_sub_department']:0;
    $districtcode = !empty($_POST['ddl_district']) ? $_POST['ddl_district']:0;
    $talukcode = !empty($_POST['ddl_taluk']) ? $_POST['ddl_taluk']:0;
    $mm = !empty($_POST['ddl_month']) ? $_POST['ddl_month']:"";
    $yyyy = !empty($_POST['ddl_year']) ? $_POST['ddl_year']:"";
    $UserID = !empty($_POST['user_id']) ? $_POST['user_id']:0;
    $upload_reference_no = !empty($_POST['upload_reference_no']) ? $_POST['upload_reference_no']:0;
    
    $created_from = $schemename;
    $postDatayyyymm = '';
    if(!empty($yyyy) && !empty($mm)){
        $postDatayyyymm = $yyyy.$mm;
    }
    


    $schemeID = 0;

        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$UserID.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

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
      

        if ($getSchemeIDqueryStmt !== false) {
            $GetschemeID = $getSchemeIDqueryStmt;
            $schemeID = $GetschemeID['id'];
        }

        $file = new SplFileObject($_FILES['beneficiary_csv_file']['tmp_name'], 'r');

        // Get the total row count
        
        
    if (isset($_FILES['beneficiary_csv_file'])) {
        $csvFile = $_FILES['beneficiary_csv_file']['tmp_name'];

        // Initialize counters
        $successCount = 0;
        $failureCount = 0;

        try {
            // Establish a database connection
            // $write_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                $isFirstRow = true; // Flag to skip the first row

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($isFirstRow) {
                        $isFirstRow = false;
                        continue; // Skip the first row
                    }
                    $yyyymm = $postDatayyyymm;
                    $benificiary_id = $data[0];
                    $installment_id = $data[1];
                    $benificiary_reference_id = $data[2];
                    $benificiary_name = $data[3];
                    $benificiary_mobile_no = $data[4];
                    $adhaar_no = $data[5];
                    $amount = $data[6];
                    $payment_mode_id = $data[7];
                    $district_code = !empty($data[8]) ? $data[8]:0;
                    $taluk_code = !empty($data[9]) ? $data[9]:0;
                    $jurisdiction_layer_code = !empty($data[10]) ? $data[10]:null;
                    $jurisdiction_code = !empty($data[11]) ? $data[11]:null;
                    $benificiary_account_no = $data[12];
                    $benificiary_bank_name = $data[13];
                    $benificiary_ifsc_code = $data[14];
                    $benificiary_iin_no = $data[15];

                    // echo "CALL public.sp_payment_uploadpaymentdetails($schemeID,$yyyymm,$benificiary_id,$installment_id,'".$benificiary_reference_id."','".$benificiary_name."','".$benificiary_mobile_no."','".$adhaar_no."',$amount,$payment_mode_id,$district_code,$taluk_code,'".$jurisdiction_layer_code."','".$jurisdiction_code."','".$benificiary_account_no."','".$benificiary_bank_name."','".$benificiary_ifsc_code."','".$benificiary_iin_no."','".$UserID."','".$created_from."',$upload_reference_no,$out_status)"; exit;


                    // Your SQL query
                    $sql = "CALL public.sp_payment_uploadpaymentdetails(:in_scheme_id,:in_yyyymm,:in_benificiary_id,:in_installment_id,:in_benificiary_reference_id,:in_benificiary_name,:in_benificiary_mobile_no,:in_adhaar_no,:in_amount,:in_payment_mode_id,:in_district_code,:in_taluk_code,:in_jurisdiction_layer_code,:in_jurisdiction_code,:in_benificiary_account_no,:in_benificiary_bank_name,:in_benificiary_ifsc_code,:in_benificiary_iin_no,:in_created_by,:in_created_from,:in_upload_reference_no,:out_status)";

                    // $out_status = true;
                    
                    // $sql = "CALL public.sp_payment_uploadpaymentdetails($schemeID,$yyyymm,$benificiary_id,$installment_id,'".$benificiary_reference_id."','".$benificiary_name."','".$benificiary_mobile_no."','".$adhaar_no."',$amount,$payment_mode_id,$district_code,$taluk_code,'".$jurisdiction_layer_code."','".$jurisdiction_code."','".$benificiary_account_no."','".$benificiary_bank_name."','".$benificiary_ifsc_code."','".$benificiary_iin_no."','".$UserID."','".$created_from."',$upload_reference_no,true);";


                    // sleep(1);
                    // echo $sql;exit;
                    

                    try {
                        // Prepare and execute the SQL query
                        $stmt = $write_db->prepare($sql);
                        
                        $stmt->bindParam(':in_scheme_id', $schemeID, PDO::PARAM_INT);
                        $stmt->bindParam(':in_yyyymm', $yyyymm, PDO::PARAM_INT);
                        $stmt->bindParam(':in_benificiary_id', $benificiary_id, PDO::PARAM_INT);
                        $stmt->bindParam(':in_installment_id', $installment_id, PDO::PARAM_INT);
                        $stmt->bindParam(':in_benificiary_reference_id', $benificiary_reference_id, PDO::PARAM_STR);
                        $stmt->bindParam(':in_benificiary_name', $benificiary_name, PDO::PARAM_STR);
                        $stmt->bindParam(':in_benificiary_mobile_no', $benificiary_mobile_no, PDO::PARAM_STR);
                        $stmt->bindParam(':in_adhaar_no', $adhaar_no, PDO::PARAM_STR);
                        $stmt->bindParam(':in_amount', $amount, PDO::PARAM_STR);
                        $stmt->bindParam(':in_payment_mode_id', $payment_mode_id, PDO::PARAM_INT);
                        $stmt->bindParam(':in_district_code', $district_code, PDO::PARAM_INT);
                        $stmt->bindParam(':in_taluk_code', $taluk_code, PDO::PARAM_INT);
                        $stmt->bindParam(':in_jurisdiction_layer_code', $jurisdiction_layer_code, PDO::PARAM_STR);
                        $stmt->bindParam(':in_jurisdiction_code', $jurisdiction_code, PDO::PARAM_STR);
                        $stmt->bindParam(':in_benificiary_account_no', $benificiary_account_no, PDO::PARAM_STR);
                        $stmt->bindParam(':in_benificiary_bank_name', $benificiary_bank_name, PDO::PARAM_STR);
                        $stmt->bindParam(':in_benificiary_ifsc_code', $benificiary_ifsc_code, PDO::PARAM_STR);
                        $stmt->bindParam(':in_benificiary_iin_no', $benificiary_iin_no, PDO::PARAM_STR);

                        $stmt->bindParam(':in_created_by', $UserID, PDO::PARAM_STR);
                        $stmt->bindParam(':in_created_from', $created_from, PDO::PARAM_STR);
                        $stmt->bindParam(':in_upload_reference_no', $upload_reference_no, PDO::PARAM_INT);

                        // Bind OUT parameters
                        $stmt->bindParam(':out_status', $out_status, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);

                        $stmt->execute();

                        // $out_status_value['functionResult'] = $out_status;

                        // Increment the success count
                        $successCount++;
                    } catch (PDOException $e) {
                        // Log the error for this data row
                       echo error_log("Error for data row: " . implode(',', $data) . ". Error: " . $e->getMessage());

                        // Increment the failure count
                        $failureCount++;
                        $failureReasons[] = "Error for data row: " . implode(',', $data) . ". Error: " . $e->getMessage();
                        continue; // Continue processing other data
                    }
                }

                fclose($handle);

                

                // $sp_update_payment_details_sql = "CALL public.sp_update_payment_details_temp_status(:in_upload_reference_no,:out_status);";
                // $beneficiary_update_stmt = $write_db->prepare($sp_update_payment_details_sql);
                // $beneficiary_update_stmt->bindParam(':in_upload_reference_no', $upload_reference_no, PDO::PARAM_INT);

                // // Bind OUT parameters
                // $beneficiary_update_stmt->bindParam(':out_status', $out_status, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);

                // $beneficiary_update_stmt->execute();

                // Close the database connection

                
             

                // Create a response array
                $response = array(
                    "success" => true,
                    "message" => "Total records processed: " . ($successCount + $failureCount),
                    "recordsInserted" => $successCount,
                    "recordsFailed" => $failureCount,
                    "upload_reference_no" => $upload_reference_no,
                );
                
                // Add failure reasons if there are failures
                if ($failureCount > 0) {
                    $response["failureReasons"] = $failureReasons;
                }

                // Set the HTTP response code to 200 (OK)
                http_response_code(200);

                // Return the JSON response
                echo json_encode($response);
            } else {
                // Set the HTTP response code to 400 (Bad Request) for file open failure
                http_response_code(400);
                echo json_encode(array("success" => false, "message" => "Failed to open the uploaded file."));
            }
        } catch (PDOException $e) {
            // Set the HTTP response code to 500 (Internal Server Error) for database connection failure
            http_response_code(500);
            echo json_encode(array("success" => false, "message" => "Error: " . $e->getMessage()));
        }
        
    } else {
        // Set the HTTP response code to 400 (Bad Request) for missing file
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "No file uploaded."));
    }

    $write_db = null;
}else{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}



function validateCSVStructure($fileInfo, $expectedColumns) {
    
    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        return "File upload failed with error code: " . $fileInfo['error'];
    }

    $csvContent = file_get_contents($fileInfo['tmp_name']);
    $rows = explode(PHP_EOL, $csvContent);
    $headerRow = str_getcsv(array_shift($rows));

    if ($headerRow !== $expectedColumns) {
        return "CSV columns do not match the expected columns !";
    }

    return TRUE;
}

?>
