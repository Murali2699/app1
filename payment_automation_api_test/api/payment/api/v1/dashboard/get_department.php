<?php
require_once('../../../helper/header.php');
// error_reporting(E_ALL);
// ini_set('display_errors',1);

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $url = "https://tngis.tnega.org/generic_api/v1/department";
        $data = $_POST;
  
        if($_POST['type']=="sub_department" && $_POST['department_code']!=0){
            $data = 'type=sub_department&department_code='.$_POST['department_code'];
        }elseif($_POST['type']=="department"){
            $data = 'type=department';
        }           

        // Dynamically set headers based on the incoming request
        $headers = array(
            'X-APP-NAME: dBt$#&1@',
            'Content-Type: application/x-www-form-urlencoded'
          );

        $ch = curl_init($url);
        // 'type=department';

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        http_response_code(200);

        // if($_POST['type']=="department"){
        //     $response = [["success"=> 1,"message"=> "Department List","data"=> [["department_code"=> 1,"department_name"=> "Adi Dravidar And Tribal Welfare Department"],["department_code"=> 2,"department_name"=> "Agriculture And Farmers Welfare"],["department_code"=> 3,"department_name"=> "Animal Husbandry, Dairying, Fisheries And Fishermen Welfare Department"],["department_code"=> 4,"department_name"=> "Bc, Mbc & Minorities Welfare Department"],["department_code"=> 5,"department_name"=> "Commercial Taxes And Registration Department"],["department_code"=> 6,"department_name"=> "Co-Operation, Food And Consumer Protection Department"],["department_code"=> 7,"department_name"=> "Energy Department"],["department_code"=> 8,"department_name"=> "Environment, Climate Change And Forests Department"],["department_code"=> 9,"department_name"=> "Finance Department"],["department_code"=> 10,"department_name"=> "Health And Family Welfare Department"],["department_code"=> 11,"department_name"=> "Higher Education Department"],["department_code"=> 12,"department_name"=> "Highways And Minor Ports Department"],["department_code"=> 13,"department_name"=> "Home, Prohibition And Excise Department"],["department_code"=> 14,"department_name"=> "Housing And Urban Development Department"],["department_code"=> 15,"department_name"=> "Human Resources Management Department"],["department_code"=> 16,"department_name"=> "Industries Department"],["department_code"=> 17,"department_name"=> "Information Technology Department"],["department_code"=> 18,"department_name"=> "Labour Welfare And Skill Development Department"],["department_code"=> 19,"department_name"=> "Micro , Small And Medium Enterprises Department"],["department_code"=> 20,"department_name"=> "Municipal Administration And Water Supply Department"],["department_code"=> 21,"department_name"=> "Planning, Development And Special Initiatives Department"],["department_code"=> 22,"department_name"=> "Handlooms, Handicrafts, Textiles And Khadi Department"],["department_code"=> 23,"department_name"=> "Law Department"],["department_code"=> 24,"department_name"=> "Pubilc Department"],["department_code"=> 25,"department_name"=> "Water Resource Department"],["department_code"=> 26,"department_name"=> "Public Work Department"],["department_code"=> 27,"department_name"=> "Revenue And Disaster Management Department"],["department_code"=> 28,"department_name"=> "Rural Development And Panchayat Raj Department"],["department_code"=> 29,"department_name"=> "School Education Department"],["department_code"=> 30,"department_name"=> "Social Welfare And Women Empowerment Department"],["department_code"=> 31,"department_name"=> "Tamil Development And Information Department"],["department_code"=> 32,"department_name"=> "Tourism,Culture And Religious Endowments Department"],["department_code"=> 33,"department_name"=> "Transport Department"],["department_code"=> 34,"department_name"=> "Youth Welfare And Sports Development Department"],["department_code"=> 35,"department_name"=> "Welfare Of Differently Abled Person"],["department_code"=> 36,"department_name"=> "Mudhalvarin Mugavari"]]]];
        // }elseif($_POST['type']=="sub_department"){
        //     $response = [["success"=>1,"message"=>"SubDepartmentList","data"=>[["sub_department_code"=>201,"sub_department_name"=>"LandAdministration"],["sub_department_code"=>202,"sub_department_name"=>"LandReforms"],["sub_department_code"=>203,"sub_department_name"=>"RevenueAdministration"],["sub_department_code"=>204,"sub_department_name"=>"UrbanLandCeilingAndUrbanLandTax"],["sub_department_code"=>205,"sub_department_name"=>"SurveyAndSettlement"]]]];
        // }


        // echo json_encode($response, true);
        echo $response;

    } else {
        http_response_code(405);
        $data = array("success" => 0, "message" => "Method Not Allowed");
        echo json_encode($data);
        die();
    }
} catch (Exception $e) {
    http_response_code(500);
    $error = array("success" => 0, "message" => "Internal Server Error: " . $e->getMessage());
    echo json_encode($error);
}



// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://tngis.tnega.org/generic_api/v1/department',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS => 'type=department',
//   CURLOPT_HTTPHEADER => array(
//     'X-APP-NAME: dBt$#&1@',
//     'Content-Type: application/x-www-form-urlencoded'
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;
