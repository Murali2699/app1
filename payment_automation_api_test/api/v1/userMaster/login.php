<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
// session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once('../../../helper/header.php');
require_once('../../../helper/read_database.php');
require_once('../../../helper/write_database.php');
// require_once('../../../helper/sendOTP.php');
// Get POST data
$case = $_POST['case'];
$mobile_number = isset($_POST['mobile_number']) ? $_POST['mobile_number'] : null;

$data = array();

if ($case === 'sentOtp') {
    if ($mobile_number === null) {
        http_response_code(500);
        array_push($data, array("success" => 0, "message" => "Please Enter Mobile number"));
        echo json_encode($data);
    } else {
        $query = $read_db->prepare("SELECT * FROM fngetuserbymobileno(:mobileno)");
        $query->bindParam(':mobileno', $mobile_number, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['active']) && $result['active'] === 'yes') {
            try {
                $generatedOTP = generateOTP(4);
                $user_id = $result['id'];
                $sms_message_name = 'KMUT User Management';
                $otp_status = sendOTP($user_id, $generatedOTP, $sms_message_name, $read_db, 'public');
                if ($otp_status == true) {
                    $stmt = $write_db->prepare("CALL spupdateotp('$mobile_number'::character varying, '$generatedOTP'::character varying,true);");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    http_response_code(200);
                    array_push($data, array("success" => 1, "message" => "OTP sent successfully"));
                    echo json_encode($data);
                    $read_db = null;
                    $write_db = null;

                    die();
                } else {
                    http_response_code(500);
                    array_push($data, array("success" => 0, "message" => "Problem in sending OTP. Please try again"));
                    echo json_encode($data);

                    $read_db = null;
                    $write_db = null;
                }
                die();
            } catch (PDOException $e) {
                http_response_code(500);
                $errorData = array("success" => 2, "message" => "Error in Updating OTP: " . $e->getMessage());
                echo json_encode($errorData);

                $read_db = null;
                $write_db = null;

                die();
            }
        } else {
            http_response_code(500);
            array_push($data, array("success" => 0, "message" => "Please Enter Registered  Mobile number"));
            echo json_encode($data);

            $read_db = null;
            $write_db = null;

            die();
        }
    }
} else if ($case === 'verifyOtp') {
    $otp = $_POST['generated_otp'];
    if ($otp === null) {
        http_response_code(500);
        array_push($data, array("success" => 0, "message" => "Please Enter OTP"));
        echo json_encode($data);

        $read_db = null;
        $write_db = null;

        die();
    } else {
        $query = $read_db->prepare("SELECT * FROM fnvalidateuserotp(:mobileno, :otpvalue)");
        $query->bindParam(':mobileno', $mobile_number, PDO::PARAM_STR);
        $query->bindParam(':otpvalue', $otp, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            http_response_code(200);
            $data = array(
                "success" => 1,
                "message" => "OTP verified successfully",
                "details" => array(
                    "first_name" => $result['first_name'],
                    "last_name" => $result['last_name'],
                    "email" => $result['email'],
                    "user_name" => $result['user_name'],
                    "mobile_no" => $result['mobile_no'],
                    "password" => $result['password'],
                    "role_id" => $result['role_id'],
                    "language_type" => $result['language_type'],
                )
            );
            echo json_encode($data);
        } else {
            http_response_code(500);
            $data = array("success" => 0, "message" => "OTP validation failed");
            echo json_encode($data);
        }
        $read_db = null;
        $write_db = null;
        die();
    }
} else if ($case === 'officialLogin') {
    $username = $_POST['user_name'];
    $encryptedPassword = $_POST['password'];
    $secretKeyHex = $_POST['secretKeyHex'];
    $ivHex = $_POST['ivHex'];

    // Convert the secret key and IV from hexadecimal to binary
    $secretKey = hex2bin($secretKeyHex);
    $iv = hex2bin($ivHex);

    // Decrypt the received password using the secret key and IV
    $decryptedPassword = openssl_decrypt(base64_decode($encryptedPassword), 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);

    $query2 = $read_db->prepare("SELECT password from fngetuserbymobileno(:username)");
    $query2->bindParam(':username', $username, PDO::PARAM_STR);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);

    $password = $result2['password'];

    if (password_verify($decryptedPassword, $password)) {
        $query = $read_db->prepare("SELECT f.*, r.role_name
    FROM public.fnvalidateuser(:username, :pwd) as f
    JOIN fngetactiveroles() as r ON r.id = f.role_id;
    ");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':pwd', $password, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // print_r($result);exit;
            http_response_code(200);
            $data = array(
                "success" => 1,
                "message" => "Login successfully",
                "details" => array(
                "user_id" => $result['id'],
                "first_name" => $result['first_name'],
                    "last_name" => $result['last_name'],
                    "email" => $result['email'],
                    "user_name" => $result['user_name'],
                    "mobile_no" => $result['mobile_no'],
                    "role_id" => $result['role_id'],
                    // "language_type" => $result['language_type'],
                    "district_code" => $result['district_code'],
                    "taluk_code" => $result['taluk_code'],
                    // "shop_code" => $result['shop_code'],
                    // "approval_level" => $result['approval_level'],
                    "role_name" => $result['role_name']
                )
            );
            echo json_encode($data);
        }
    } else {
        http_response_code(500);
        $data = array("success" => 0, "message" => "Login failed");
        echo json_encode($data);
    }
    $read_db = null;
    $write_db = null;
    die();
} else if ($case === 'updatePwd') {
    $username = $_POST['user_name'];
    $password = $_POST['password'];

    if ($password === null) {
        http_response_code(500);
        array_push($data, array("success" => 0, "message" => "Please Enter Password"));
        echo json_encode($data);

        $read_db = null;
        $write_db = null;

        die();
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = $write_db->prepare("CALL public.spupdatepassword(:username, :pwd,NULL);");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':pwd', $hashedPassword, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            http_response_code(200);
            $data = array(
                "success" => 1,
                "message" => " Password updated successfully",
            );
            echo json_encode($data);
        } else {
            http_response_code(500);
            $data = array("success" => 0, "message" => "UserName and Password updation failed");
            echo json_encode($data);
        }
        $read_db = null;
        $write_db = null;
        die();
    }
} else if ($case === 'publicLogin') {
    $aadhar = $_POST['aadhar_number'];

    $query = $read_db->prepare("SELECT * from fn_get_applicant_data(:p_aadhaar_no);");
    $query->bindParam(':p_aadhaar_no', $aadhar, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        http_response_code(200);
        $data = array(
            "success" => 1,
            "message" => "Login successfully",
            "details" => $result
        );
        echo json_encode($data);
    } else {
        http_response_code(200);
        $data = array("success" => 0, "message" => "Login failed");
        echo json_encode($data);
    }
    $read_db = null;
    $write_db = null;
    die();
} 
else if ($case === 'resetPwd') {
    $username = $_POST['mobile_number'];
    $password = '$2y$10$.T75W.lyDeSX4/jEVX69bOnR7PgIsZAg8f0ABv5UCuq7xRGKxDVoC';
  
        $query = $write_db->prepare("CALL public.spupdatepassword(:username, :pwd,NULL);");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':pwd', $password, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            http_response_code(200);
            $data = array(
                "success" => 1,
                "message" => " Password resetted successfully",
            );
            echo json_encode($data);
        } else {
            http_response_code(500);
            $data = array("success" => 0, "message" => "UserName and Password reset failed");
            echo json_encode($data);
        }
        $read_db = null;
        $write_db = null;
        die();
    }
else {
    $data['error'] = 'Invalid case parameter';
}

function generateOTP($n)
{
    $generator = "1357902468";
    $result = "";
    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }
    return $result;
}


?>