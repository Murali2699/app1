<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');
// require_once('../../helper/mysql_database_phase1.php');


error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = isset($_POST['user_id'])? $_POST['user_id']:0;
    $icategory = !empty($_POST['dropdown_type'])?$_POST['dropdown_type']:"";

    $get_schem = "select * from fn_general_getdropdownvalues('".$icategory."',$userID)";
    $schem_stmt = $read_db->prepare($get_schem);
    $schem_stmt->execute();
    $schem_count = $schem_stmt->rowCount();
    $opts2='';
        if ($schem_count >= 1) {
                    $schemResult = $schem_stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $schemResult
            );
            echo json_encode($data);
            $read_db=null;
            die();
            
        }else{
            http_response_code(200);
            $data = array(
                "success" => 0, 
                "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
            );
            echo json_encode($data);
            die();
        }
    }else {
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}