<?php
// phpinfo();exit;
error_reporting(E_ALL);
ini_set('display_errors',1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('../../../helper/header.php');
require_once('../../../helper/write_database.php');
ini_set('memory_limit', '-1');
ini_set('post_max_size', '100M');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $expectedColumns = ['benificiary_id', 'installment_id', 'benificiary_reference_id', 'benificiary_name', 'benificiary_mobile_no', 'adhaar_no', 'amount', 'payment_mode_id', 'district_code', 'taluk_code', 'jurisdiction_layer_code', 'jurisdiction_code', 'benificiary_account_no', 'benificiary_bank_name', 'benificiary_ifsc_code', 'benificiary_iin_no'];

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

    $schemecategory = !empty($_POST['ddl_scheme_cate']) ? trim($_POST['ddl_scheme_cate']):"";
    $schemecode = !empty($_POST['ddl_scheme_code']) ? trim($_POST['ddl_scheme_code']):"";
    $schemename = !empty($_POST['ddl_scheme_name']) ? trim($_POST['ddl_scheme_name']):"";
    $schemename = str_replace("'","''",$schemename);
    $subschemename = !empty($_POST['ddl_sub_scheme']) ? trim($_POST['ddl_sub_scheme']):"";
    $subschemename = str_replace("'","''",$subschemename);
    $idepartment = !empty($_POST['ddl_department']) ? trim($_POST['ddl_department']):0;
    $isubdepartment = !empty($_POST['ddl_sub_department']) ? trim($_POST['ddl_sub_department']):0;
    $districtcode = !empty($_POST['ddl_district']) ? trim($_POST['ddl_district']):0;
    $talukcode = !empty($_POST['ddl_taluk']) ? trim($_POST['ddl_taluk']):0;
    $mm = !empty($_POST['ddl_month']) ? trim($_POST['ddl_month']):"";
    $yyyy = !empty($_POST['ddl_year']) ? trim($_POST['ddl_year']):"";
    $UserID = !empty($_POST['user_id']) ? trim($_POST['user_id']):0;
    $upload_reference_no = !empty($_POST['upload_reference_no']) ? trim($_POST['upload_reference_no']):0;
    
    $created_from = $schemename;
    $postDatayyyymm = '';
    if(!empty($yyyy) && !empty($mm)){
        $postDatayyyymm = $yyyy.$mm;
    }
    


    $schemeID = 0;

        $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$UserID.") WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

        // $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(".$UserID.") WHERE scheme_category LIKE '".$schemecategory."' AND scheme_code LIKE '".$schemecode."' AND scheme_name LIKE '".$schemename."' AND subscheme_name LIKE '".$subschemename."' AND department = $idepartment AND subdepartment = $isubdepartment";

        // echo $getSchemeIDsqlQuery;exit;

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
    //   echo 'hai';
// print_r($getSchemeIDqueryStmt);exit;
        if ($getSchemeIDqueryStmt !== false) {
            $GetschemeID = $getSchemeIDqueryStmt;
            $schemeID = $GetschemeID['id'];
        }

        $file = new SplFileObject($_FILES['beneficiary_csv_file']['tmp_name'], 'r');

        // Get the total row count
        
        
    if (isset($_FILES['beneficiary_csv_file'])) {

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseURL = $protocol . '://' . $host;
        $domain = preg_replace("(^https?://)", "", $baseURL);

        switch ($domain) {
            case 'kmut.tn.gov.in':
            //prod
                $currentFilepath = '/var/www/html/kmut/payment_automation_prod_testing/payment_automation_api/api/payment/api/v1/';
                $currentScriptName = 'insert_benifi_kmut_batch';
                $currentVenvPath = "/root/dbt/venv/bin/activate";
                $currentPythonFilePath = "/root/dbt/venv/bin/python";
                $currentBaseUrl = $domain;
                break;

            case 'tngis.tnega.org':
            //staging
                $currentFilepath = '/var/www/html/payment_automation_api/api/payment/api/v1/';
                $currentScriptName = 'insert_benifi_kmut_batch';
                $currentVenvPath = "/root/dbt/venv/bin/activate";
                $currentPythonFilePath = "/root/dbt/venv/bin/python";
                $currentBaseUrl = $domain;
                break;
                
            default:
            //local
                $currentFilepath = '/opt/lampp/htdocs/payment_automation/api/payment/api/v1/';
                $currentScriptName = 'insert_benifi_kmut_batch';
                $currentVenvPath = "/root/dbt/venv/bin/activate";
                $currentPythonFilePath = "/root/dbt/python ";
                $currentBaseUrl = $domain;
                break;
        }

        // echo $currentBaseUrl;exit;
        $fileContent = file_get_contents($_FILES['beneficiary_csv_file']['tmp_name']);
        // $fileContent = $_FILES['beneficiary_csv_file']['tmp_name'];
        $name = $_FILES['beneficiary_csv_file']['name'];

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $uploadDir = $currentFilepath;
        
        if ($ext == 'csv' || $ext == 'xlsx') {
            $uniqueIDfilename = $upload_reference_no.'_'.date('Y-m-d').'.'.$ext;
            $fileFullPath = $uploadDir . $uniqueIDfilename;
            // move_uploaded_file($fileContent, $fileFullPath);
            file_put_contents($fileFullPath, $fileContent);

            /*call python script*/
                $scriptName = $currentScriptName;//python file name
                $venvPath = $currentVenvPath;
                $activateCommand = "bash -c 'source $venvPath'";
                shell_exec($activateCommand);
                $python_executable = $currentPythonFilePath;

                $command = "$python_executable /root/dbt/{$scriptName}.py {$schemeID} {$postDatayyyymm} {$uploadDir} {$ext} {$uniqueIDfilename} {$UserID} '".$created_from."' {$upload_reference_no} 2>&1";
                // $result = shell_exec($command);

                exec($command, $output, $returnCode);

                if ($returnCode === 0) {
                    // Command executed successfully
                    $result = implode("\n", $output);
                    $returnResult = array('success' => true, 'message' => 'Command executed successfully', 'data' => $result);
                } else {
                    // Command failed, $output contains error message
                    $errorMessage = implode("\n", $output);
                    $returnResult = array('success' => false, 'message' => 'Command failed: ' . $errorMessage);
                }

                echo json_encode($returnResult);
            return false;
        }else{
                echo json_encode(array('success'=> false, 'message'=> 'File Not Allowed'));
            return false;
        }
        return false;
        exit;        
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
    // print_r($headerRow);
    // echo '-----------------';
    // print_r($expectedColumns);
    // exit;
    $headerRow = array_filter($headerRow);
    if ($headerRow !== $expectedColumns) {
        return "CSV columns do not match the expected columns !" . implode(', ', $headerRow);
    }

    return TRUE;
}

?>
