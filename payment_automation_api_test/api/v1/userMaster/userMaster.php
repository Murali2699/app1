<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');

error_reporting(E_ALL);
ini_set('display_errors',1);

// Get POST data
$case = $_POST['case'];
$districtCode = isset($_POST['district_code']) ? $_POST['district_code'] : null;
$talukCode = isset($_POST['taluk_code']) ? $_POST['taluk_code'] : null;
$pdsShopCode = isset($_POST['pds_shop_code']) ? $_POST['pds_shop_code'] : null;
$villageCode = isset($_POST['village_code']) ? $_POST['village_code'] : null;

$response = array();

if ($case === 'district') {
    if (is_null($districtCode)) {
        // Retrieve all district data
        $query = $read_db->query("SELECT district_code ,district_name from fngetdistrictlist() order by district_name");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $response['districts'] = $result;

            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            // CURLOPT_URL => 'http://household-api-application-env.ap-south-1.elasticbeanstalk.com/api/kmut_web_portal_api/api/v1/userMaster/userMaster',
            // CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_ENCODING => '',
            // CURLOPT_MAXREDIRS => 10,
            // CURLOPT_TIMEOUT => 0,
            // CURLOPT_FOLLOWLOCATION => true,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_POSTFIELDS => 'case=district&user_id=23840',
            // CURLOPT_HTTPHEADER => array(
            //     'X-App-Key: km$ut_webp0rt@l',
            //     'X-App-Name: KMUT WebPortal',
            //     'Content-Type: application/x-www-form-urlencoded',
            //     'Cookie: AWSALB=XZUv/Mrhybl22/xkxIm7nIuENHClN0e1/fPKJxOVL5FDHpn2vJC7FImfQBjgrsCjo2Qzb02U+fWPvdJ79JqkHWBdgrtyJW7qq99NuuGcNCf1x2S4ci2nYyJC6I3w; AWSALBCORS=XZUv/Mrhybl22/xkxIm7nIuENHClN0e1/fPKJxOVL5FDHpn2vJC7FImfQBjgrsCjo2Qzb02U+fWPvdJ79JqkHWBdgrtyJW7qq99NuuGcNCf1x2S4ci2nYyJC6I3w'
            // ),
            // ));

            // $result = curl_exec($curl);

            // curl_close($curl);
            // $resultDistrict = json_decode($result);
            // $response['districts']  = $resultDistrict->districts;
            


    } else {
        // Retrieve district data based on district code
        $query = $read_db->prepare("SELECT district_code ,district_name from fngetdistrictlist() WHERE district_code = :districtCode ");
        $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        $response['district'] = $result;
    }
} elseif ($case === 'taluk') {
    if (!is_null($districtCode)) {
        if (is_null($talukCode)) {
            // Retrieve all taluk data for the given district
            $query = $read_db->prepare("SELECT taluk_code ,taluk_name from fngettaluklist(:districtCode) order by taluk_name ");
            $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $response['taluk'] = $result;


                // $curl = curl_init();

                // curl_setopt_array($curl, array(
                // CURLOPT_URL => 'http://household-api-application-env.ap-south-1.elasticbeanstalk.com/api/kmut_web_portal_api/api/v1/userMaster/userMaster',
                // CURLOPT_RETURNTRANSFER => true,
                // CURLOPT_ENCODING => '',
                // CURLOPT_MAXREDIRS => 10,
                // CURLOPT_TIMEOUT => 0,
                // CURLOPT_FOLLOWLOCATION => true,
                // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                // CURLOPT_CUSTOMREQUEST => 'POST',
                // CURLOPT_POSTFIELDS => 'case=taluk&user_id=23840&district_code='.$districtCode,
                // CURLOPT_HTTPHEADER => array(
                // 'X-App-Key: km$ut_webp0rt@l',
                // 'X-App-Name: KMUT WebPortal',
                // 'Content-Type: application/x-www-form-urlencoded',
                // 'Cookie: AWSALB=Wm1dzPjGA5cHvOulWoeIMQpAF9Rdj06lgPYySbyBacIoOP9nexgYC07UoSHCLn02ADmWblK1oUj86LJFbLdsKhePwOQ2I/tbLlWKg17vy+APmFjQX5dxHYGBIEC7; AWSALBCORS=Wm1dzPjGA5cHvOulWoeIMQpAF9Rdj06lgPYySbyBacIoOP9nexgYC07UoSHCLn02ADmWblK1oUj86LJFbLdsKhePwOQ2I/tbLlWKg17vy+APmFjQX5dxHYGBIEC7'
                // ),
                // ));

                // $result = curl_exec($curl);

                // curl_close($curl);
                // $resultTaluk = json_decode($result);
    
                // $response['taluk']  = $resultTaluk->taluk;

        } else {
            // Retrieve taluk data based on district and taluk code
            $query = $read_db->prepare("SELECT taluk_code ,taluk_name from fngettaluklist(:districtCode) WHERE taluk_code = :talukCode");
            $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
            $query->bindParam(':talukCode', $talukCode, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $response['taluk'] = $result;
        }
    }
} 
 elseif ($case === 'designation') {

    // Retrieve all taluk data for the given district
    $query = $read_db->prepare("SELECT id AS role_id, role_name FROM fngetactiveroles() ");
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    $response['designation'] = $result;

} elseif ($case === 'pds_shop') {
    if (!is_null($districtCode) && !is_null($talukCode)) {
        if (is_null($pdsShopCode)) {
            // Retrieve all pdsshop data for the given district and taluk
            $query = $read_db->prepare("SELECT psd_shop_code as pds_shop_code, psd_shop_name as pds_shop_name FROM fngetshopcodelist(:districtCode,:talukCode,:villageCode) ORDER BY pds_shop_code ASC;");
            $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
            $query->bindParam(':talukCode', $talukCode, PDO::PARAM_INT);
            $query->bindParam(':villageCode', $villageCode, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            $response['pdsshop'] = $result;
        } else {
            // Retrieve pdsshop data based on district, taluk, and pdsshop code
            $query = $read_db->prepare("SELECT psd_shop_code as pds_shop_code, psd_shop_name as pds_shop_name FROM fngetshopcodelist(:districtCode,:talukCode,:villageCode) where pds_shop_code = :pdsShopCode ORDER BY pds_shop_code ASC;");
            $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
            $query->bindParam(':talukCode', $talukCode, PDO::PARAM_INT);
            $query->bindParam(':villageCode', $villageCode, PDO::PARAM_INT);
            $query->bindParam(':pdsShopCode', $pdsShopCode, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            $response['pdsshop'] = $result;
        }
    }

} elseif ($case == 'getData') {
    $draw = $_POST['draw'];
    $start = $_POST['start'];
    $length = $_POST['length'];
    $district_code = isset($_POST['district_code']) ? $_POST['district_code'] : 0;

    $name_filter = isset($_POST['name_filter']) ? $_POST['name_filter'] : ''; 
    $mobile_filter = isset($_POST['mobile_filter']) ? $_POST['mobile_filter'] : ''; 
    $shopcode_filter = isset($_POST['shop_filter']) ? $_POST['shop_filter'] : ''; 

    if ($district_code == 0 ) {
       
        $sql = "SELECT u.id,
        u.first_name,
        u.mappingid,
        u.mobile_no,
        u.email,
        r.role_name,
        u.role_id, 
        u.approval_level,
        u.active,
        u.district_name,
        u.taluk_name,
        u.village_name,
        u.district_code,
        u.taluk_code,
        u.shop_code,
        u.village_name,
        u.village_code
 FROM fngetalluserswithmapping() AS u
 JOIN roles AS r ON u.role_id = r.id
 WHERE district_code IS NULL";

        if (!empty($name_filter)) {
            $sql .= " AND u.first_name LIKE :name_filter";
        }

        if (!empty($mobile_filter)) {
            $sql .= " AND u.mobile_no LIKE :mobile_filter";
        }
        

        $sql .= " LIMIT :limit OFFSET :offset";

        $getData_sql = $read_db->prepare($sql);

        // Bind parameters
       // $getData_sql->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
        $getData_sql->bindParam(':limit', $length, PDO::PARAM_INT);
        $getData_sql->bindParam(':offset', $start, PDO::PARAM_INT);

        if (!empty($name_filter)) {
            $getData_sql->bindParam(':name_filter', $name_filter, PDO::PARAM_STR);
        }

        if (!empty($mobile_filter)) {
            $getData_sql->bindParam(':mobile_filter', $mobile_filter, PDO::PARAM_STR);
        }

        $getData_sql->execute();
        function fetchData($getData_sql, $read_db)
        {
            while ($row = $getData_sql->fetch(PDO::FETCH_ASSOC)) {
                $pds_shop_code = ($row['shop_code'] == 0) ? null : $row['shop_code'];
                // Get the shop_name based on shop_code
                yield [

                    'user_id' => $row['id'],
                    'mapp_id' => $row['mappingid'],
                    'name' => $row['first_name'],
                    'mobile_number' => $row['mobile_no'],
                    'email_id' => $row['email'],
                    'designation' => $row['role_name'],
                    'role_id' => $row['role_id'],
                    'approval_level' => $row['approval_level'],
                    'is_active' => $row['active'],
                    'district_name' => $row['district_name'],
                    'district_code' => $row['district_code'],
                    'taluk_name' => $row['taluk_name'],
                    'taluk_code' => $row['taluk_code'],
                    'village_name' => $row['village_name'],
                    'village_code' => $row['village_code'],
                    'pds_shop_code' =>  $pds_shop_code,
                    'pds_shop_name' => NULL,
                ];
            }
        }
        $sql_count = $read_db->prepare("SELECT count(*) FROM fngetalluserswithmapping() WHERE district_code IS NULL OR district_code = 0;");
        $sql_count->execute(); // Execute the query
        $totalRecords = $sql_count->fetchColumn();

        $data = [];
        foreach (fetchData($getData_sql, $read_db) as $item) {
            array_push($data, $item);
        }
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $data,
        ];
        http_response_code(200);
        echo json_encode($response);
        $read_db = null;

        die();

    } else {
        $sql = "SELECT u.id,
        u.mappingid,
        u.first_name,
        u.mobile_no,
        u.email,
        r.role_name,
        u.role_id,
        u.approval_level,
        u.active,
        u.district_name,
        response u.taluk_name,
        u.village_name,
        u.district_code,
        u.taluk_code,
        u.shop_code,
        u.village_name,
        u.village_code
        FROM fngetalluserswithmapping() AS u
        JOIN roles AS r ON u.role_id = r.id
        WHERE u.district_code = :districtCode";

        if (!empty($name_filter)) {
            $sql .= " AND u.first_name LIKE :name_filter";
        }

        if (!empty($mobile_filter)) {
            $sql .= " AND u.mobile_no LIKE :mobile_filter";
        }
        if (!empty($shopcode_filter)) {
            $sql .= " AND u.shop_code LIKE :shop_code";
        }
        $sql .= " LIMIT :limit OFFSET :offset";

        $getData_sql = $read_db->prepare($sql);

        // Bind parameters
        $getData_sql->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
        $getData_sql->bindParam(':limit', $length, PDO::PARAM_INT);
        $getData_sql->bindParam(':offset', $start, PDO::PARAM_INT);

        if (!empty($name_filter)) {
            $getData_sql->bindParam(':name_filter', $name_filter, PDO::PARAM_STR);
        }

        if (!empty($mobile_filter)) {
            $getData_sql->bindParam(':mobile_filter', $mobile_filter, PDO::PARAM_STR);
        }
        if (!empty($shopcode_filter)) {
            $getData_sql->bindParam(':shop_code', $shopcode_filter, PDO::PARAM_STR);
        }

        $getData_sql->execute();
        function fetchData($getData_sql, $read_db)
        {
            while ($row = $getData_sql->fetch(PDO::FETCH_ASSOC)) {
                
                // Get the shop_name based on shop_code
                $getShopName_sql = $read_db->prepare("SELECT psd_shop_name FROM fngetshopcodelist(:district_code, :taluk_code, 0) WHERE psd_shop_code = :shop_code");
                $getShopName_sql->bindParam(":district_code", $row['district_code']);
                $getShopName_sql->bindParam(":taluk_code", $row['taluk_code']);
                $getShopName_sql->bindParam(":shop_code", $row['shop_code']);
                $getShopName_sql->execute();
                $shop_name = $getShopName_sql->fetchColumn();
                $pds_shop_code = ($row['shop_code'] == 0) ? null : $row['shop_code'];
                yield [
                    'user_id' => $row['id'],
                    'mapp_id' => $row['mappingid'],
                    'name' => $row['first_name'],
                    'mobile_number' => $row['mobile_no'],
                    'email_id' => $row['email'],
                    'designation' => $row['role_name'],
                    'role_id' => $row['role_id'],
                    'approval_level' => $row['approval_level'],
                    'is_active' => $row['active'],
                    'district_name' => $row['district_name'],
                    'district_code' => $row['district_code'],
                    'taluk_name' => $row['taluk_name'],
                    'taluk_code' => $row['taluk_code'],
                    'village_name' => $row['village_name'],
                    'village_code' => $row['village_code'],
                    'pds_shop_code' =>  $pds_shop_code,
                    'pds_shop_name' => $shop_name, // Add shop_name to the output
                ];
            }
        }
        $sql_count = $read_db->prepare("SELECT count(*) FROM fngetalluserswithmapping() WHERE district_code = :districtCode;");
        $sql_count->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
        $sql_count->execute(); // Execute the query
        $totalRecords = $sql_count->fetchColumn(); // Fetch the result

        $data = [];
        foreach (fetchData($getData_sql, $read_db) as $item) {
            array_push($data, $item);
        }
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $data,
        ];
        http_response_code(200);
        echo json_encode($response);
        $read_db = null;

        die();
    }

} elseif ($case == 'searchByUser') {
    // Sanitize the input and prepare the query
    $mobile_number = $_POST['mobile_number'];
    $check_sql = $read_db->prepare("SELECT first_name as name, district_name, mobile_no, shop_code FROM fngetallusers() WHERE mobile_no = :mobile_number");
    $check_sql->bindValue(':mobile_number', $mobile_number, PDO::PARAM_STR);
    $check_sql->execute();

    // Fetch user details
    $result = $check_sql->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Data Found
        $response['userDetails'] = $result;
        $response['success'] = 1;
        $response['message'] = "Data Found";
    } else {
        // Mobile number is not registered
        $response['success'] = 0;
        $response['message'] = "Mobile number is not registered";
    }

    // Set HTTP response code and return JSON response
    http_response_code(200);
    echo json_encode($response);

    // Close the database connections
    $read_db = null;

    die();

} elseif ($case === 'village') {
    if (!is_null($districtCode) && !is_null($talukCode)) {
        // Retrieve all pdsshop data for the given district and taluk
        $query = $read_db->prepare("SELECT village_code, village_name  FROM fngetvillagelist(:districtCode,:talukCode) ORDER BY village_code ASC;");
        $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
        $query->bindParam(':talukCode', $talukCode, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $response['village'] = $result;
    }

}
elseif ($case === 'taluk_user') {
    if (!is_null($districtCode)) {
        if (is_null($talukCode)) {
            $userId = $_POST['user_id'];
            
            // Retrieve all taluk data for the given district
            $query = $read_db->prepare("SELECT distinct taluk_code ,taluk_name from fngettaluklist(:districtCode,:userid) order by taluk_name ");
            $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
            $query->bindParam(':userid', $userId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $response['taluk'] = $result;
        } else {
            // Retrieve taluk data based on district and taluk code
            $query = $read_db->prepare("SELECT taluk_code ,taluk_name from fngettaluklist(:districtCode) WHERE district_code = :districtCode AND taluk_code = :talukCode");
            $query->bindParam(':districtCode', $districtCode, PDO::PARAM_INT);
            $query->bindParam(':talukCode', $talukCode, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $response['taluk'] = $result;
        }
    }
}
 else {
    $response['error'] = 'Invalid case parameter';
}

echo json_encode($response);
$read_db = null;
die();
?>