<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
    $schmecate = !empty($_POST['schmecate']) ? $_POST['schmecate'] : "";
    $schemeCode = !empty($_POST['schemeCode']) ? $_POST['schemeCode'] : "";
    $schemeName = !empty($_POST['schemeName']) ? $_POST['schemeName'] : "";
    $subSchemeName = !empty($_POST['subSchemeName']) ? $_POST['subSchemeName'] : "";
    $department = !empty($_POST['department']) ? $_POST['department'] : 0;
    $subDepartment = !empty($_POST['subDepartment']) ? $_POST['subDepartment'] : 0;
    $paymentCycle = !empty($_POST['paymentCycle']) ? $_POST['paymentCycle'] : 0;
    $paymentPeriod = !empty($_POST['paymentPeriod']) ? $_POST['paymentPeriod'] : 0;
    $paymentInstall = !empty($_POST['paymentInstall']) ? $_POST['paymentInstall'] : 0;
    $yyyymm = !empty($_POST['yyyymm']) ? $_POST['yyyymm'] : "";
    $paymentdate = !empty($_POST['paymentdate']) ? $_POST['paymentdate'] : "2000-01-01"; 


    $schemeID = 0;

    $getSchemeIDsqlQuery = "SELECT id FROM fn_scheme_getschemesbyuser(:userId) WHERE scheme_category LIKE :schemecategory AND scheme_code LIKE :schemecode AND scheme_name LIKE :schemename AND subscheme_name LIKE :subschemename AND department = :idepartment AND subdepartment = :isubdepartment";

    $queryStmt = $read_db->prepare($getSchemeIDsqlQuery);
    $queryStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $queryStmt->bindParam(':schemecategory', $schmecate, PDO::PARAM_STR);
    $queryStmt->bindParam(':schemecode', $schemeCode, PDO::PARAM_STR);
    $queryStmt->bindParam(':schemename', $schemeName, PDO::PARAM_STR);
    $queryStmt->bindParam(':subschemename', $subSchemeName, PDO::PARAM_STR);
    $queryStmt->bindParam(':idepartment', $department, PDO::PARAM_INT);
    $queryStmt->bindParam(':isubdepartment', $subDepartment, PDO::PARAM_INT);
    
    $queryStmt->execute();

    $getSchemeIDqueryStmt = $queryStmt->fetch(PDO::FETCH_ASSOC);

    if ($getSchemeIDqueryStmt !== false) {
        $schemeID = $getSchemeIDqueryStmt['id'];
    }

    $sql= "SELECT * FROM fn_payment_getemomarkedbeneficiarydetails(:schemeid, :schemename, :subschemename, :idepartment, :isubdepartment, :iyyyymm, :paymentdate, :userid)"; 

    $statement = $read_db->prepare($sql);

    $statement->bindParam(':schemeid', $schemeID, PDO::PARAM_INT);
    $statement->bindParam(':schemename', $schemeName, PDO::PARAM_STR);
    $statement->bindParam(':subschemename', $subSchemeName, PDO::PARAM_STR);
    $statement->bindParam(':idepartment', $department, PDO::PARAM_INT);
    $statement->bindParam(':isubdepartment', $subDepartment, PDO::PARAM_INT);
    $statement->bindParam(':iyyyymm', $yyyymm, PDO::PARAM_INT);
    $statement->bindParam(':paymentdate', $paymentdate, PDO::PARAM_STR);
    $statement->bindParam(':userid', $userId, PDO::PARAM_STR);
    $statement->execute();
    $prepost_count = $statement->rowCount();
    $benefi_count_result = $statement->fetchAll(PDO::FETCH_ASSOC);
       
    if ($prepost_count >= 1) {

        http_response_code(200);
        $data = array("success" => 1, "message" => "Data Found", 'data' => $benefi_count_result);
        echo json_encode($data);
        exit();

    } else {

        http_response_code(200);
        $data = array("success" => 0, "message" => "EMO Marked Beneficiary details were not found for the selection", 'data' => []);
        echo json_encode($data);
        exit();

    }

} else {

    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    exit();

}
    ?>