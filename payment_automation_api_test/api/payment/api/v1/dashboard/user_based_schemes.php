<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    
    $qry ="select scheme_category from fn_scheme_getschemesbyuser(".$user_id.") group by scheme_category;";
    $sqlQuery = $qry;

        $queryStmt = $read_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
      
        if ($prepost_count >= 1) {
            $schemes = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $schemes
            );
            echo json_encode($data);
            die();
        $read_db=null;
        }else{
            http_response_code(200);
            $data = array(
                "success" => 0, 
                "message" => "case Not Found"
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
