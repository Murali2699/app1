<?php
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case = isset($_POST['case']) ? $_POST['case'] : '';
    $scheme_code = isset($_POST['scheme_code']) ? $_POST['scheme_code'] : '';
    $scheme_name = isset($_POST['scheme_name']) ? $_POST['scheme_name'] : '';
    // $scheme_name = str_replace("'","''",$scheme_name);
    $sub_scheme_name = isset($_POST['sub_scheme_name']) ? $_POST['sub_scheme_name'] : '';
    // $sub_scheme_name = str_replace("'","''",$sub_scheme_name);
    $department = isset($_POST['department']) ? $_POST['department'] : '';
    $whereColumn = isset($_POST['whereColumn']) ? $_POST['whereColumn'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    
    
    if (!empty($case)) {
        if($case=='get_scheme_category'){
            $qry ="select scheme_category from fn_scheme_getschemesbyuser(".$user_id.") group by scheme_category;";
        }
        if($case=='get_scheme_code'){
            $qry ="select scheme_code from fn_scheme_getschemesbyuser(".$user_id.") where scheme_category like '".$whereColumn."' group by scheme_code;";
        }
        if($case=='get_scheme'){
            $qry ="select scheme_code,scheme_name from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code ='".$scheme_code."' group by scheme_code,scheme_name;";
        }
        if($case=='get_sub_scheme'){
            // $qry ="select scheme_code,scheme_name,subscheme_name from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code ='".$scheme_code."' and scheme_name = '".$scheme_name."' group by scheme_code,scheme_name,subscheme_name;";

                $qry = "SELECT scheme_code, scheme_name, subscheme_name FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_code = :scheme_code AND scheme_name = :scheme_name GROUP BY scheme_code, scheme_name, subscheme_name;";

                $sqlQuery = $qry;

                $queryStmt = $read_db->prepare($sqlQuery);

                // Bind parameters
                $queryStmt->bindParam(':scheme_code', $scheme_code, PDO::PARAM_STR);
                $queryStmt->bindParam(':scheme_name', $scheme_name, PDO::PARAM_STR);

                // Execute the query
                $queryStmt->execute();

                $prepost_count = $queryStmt->rowCount();

                $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
                // print_r($scheme_name);exit;
                http_response_code(200);
                $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
                );
                echo json_encode($data);
                die();
                return true;
            
        }
        if($case=='get_department'){
            // $qry ="select scheme_code,scheme_name,subscheme_name,department from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code ='".$scheme_code."' and scheme_name = '".$scheme_name."' and subscheme_name = '".$sub_scheme_name."' group by scheme_code,scheme_name,subscheme_name,department;";

            $qry = "SELECT scheme_code, scheme_name, subscheme_name, department FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_code = :scheme_code AND scheme_name = :scheme_name AND subscheme_name = :sub_scheme_name GROUP BY scheme_code, scheme_name, subscheme_name, department;";


                $sqlQuery = $qry;

                $queryStmt = $read_db->prepare($sqlQuery);

                // Bind parameters
                $queryStmt->bindParam(':scheme_code', $scheme_code, PDO::PARAM_STR);
                $queryStmt->bindParam(':scheme_name', $scheme_name, PDO::PARAM_STR);
                $queryStmt->bindParam(':sub_scheme_name', $sub_scheme_name, PDO::PARAM_STR);

                // Execute the query
                $queryStmt->execute();

                $prepost_count = $queryStmt->rowCount();

                $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
                $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
                );
                echo json_encode($data);
                die();
                return true;
        }
        if($case=='get_sub_department'){
            // $qry ="select scheme_code,scheme_name,subscheme_name,department,subdepartment from fn_scheme_getschemesbyuser(".$user_id.") where scheme_code ='".$scheme_code."' and scheme_name = '".$scheme_name."' and subscheme_name = '".$sub_scheme_name."' and department = '".$department."' group by scheme_code,scheme_name,subscheme_name,department,subdepartment;";


            $qry = "SELECT scheme_code, scheme_name, subscheme_name, department, subdepartment FROM fn_scheme_getschemesbyuser(".$user_id.") WHERE scheme_code = :scheme_code AND scheme_name = :scheme_name AND subscheme_name = :sub_scheme_name AND department = :department GROUP BY scheme_code, scheme_name, subscheme_name, department, subdepartment;";


            $sqlQuery = $qry;

            $queryStmt = $read_db->prepare($sqlQuery);

            // Bind parameters
            $queryStmt->bindParam(':scheme_code', $scheme_code, PDO::PARAM_STR);
            $queryStmt->bindParam(':scheme_name', $scheme_name, PDO::PARAM_STR);
            $queryStmt->bindParam(':sub_scheme_name', $sub_scheme_name, PDO::PARAM_STR);
            $queryStmt->bindParam(':department', $department, PDO::PARAM_STR);

            // Execute the query
            $queryStmt->execute();

            $prepost_count = $queryStmt->rowCount();

            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $data = array(
            "success" => 1, 
            "message" => "Data Found", 
            'data' => $complaintResult
            );
            echo json_encode($data);
            die();
            return true;


        }


// echo $qry;exit;


        $sqlQuery = $qry;
        // echo $sqlQuery;exit;
        // $action ='';

        $queryStmt = $read_db->prepare($sqlQuery);
        $queryStmt->execute();
        $prepost_count = $queryStmt->rowCount();
      
        // if ($prepost_count >= 1) {
            $complaintResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            $data = array(
                "success" => 1, 
                "message" => "Data Found", 
                'data' => $complaintResult
            );
            echo json_encode($data);
            die();
        // } else {
        //     http_response_code(200);
        //     $data = array(
        //         "success" => 0, 
        //         "message" => "தகவல் ஏதும் கிடைக்க பெறவில்லை"
        //     );
        //     echo json_encode($data);
        //     die();
        // }
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
