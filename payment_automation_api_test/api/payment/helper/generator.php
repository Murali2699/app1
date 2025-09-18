<?php

// User ID Generator
function generate_user_id($name, $db,$schema_name)
{
    // Remove whitespace and non-alphanumeric characters from name and mobile
    $user_id = preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($name)));
    // $mobile = preg_replace('/[^0-9]+/', '', $mobile);

    // Generate a random 3 digit number
    $random_num = rand(100, 999);

    // Append random number to username
    $user_id = $user_id . '_' . $random_num;

    // Check if username already exists, generate a new random number if necessary
    while (is_exists('user_id', $user_id, $db,$schema_name)) {
        generate_user_id($name, $db,$schema_name);
    }

    // Return the unique username
    return $user_id;
}

function is_exists($type, $value, $db,$schema_name)
{
    // Check if user_id exists in the database, return true or false
    if ($type == 'user_id') {
        $col = 'user_id';
    } else if ($type == 'reference_id') {
        $col = 'reference_id';
    }
    $check_user_id_sql = "SELECT * FROM ".$schema_name.".users where $col=?";
    $check_user_id_stmt = $db->prepare($check_user_id_sql);
    $check_user_id_stmt->execute(array($value));
    $check_user_id_count = $check_user_id_stmt->rowCount();
    if ($check_user_id_count === 0) {
        return false;
    } else {
        return true;
    }
}

/**
 * 
 * Start with a random 10 digit number.
 * Calculate the checksum digit using the Luhn algorithm.
 * Append the checksum digit to the end of the 10 digit number to get an 11 digit number.
 * Multiply every other digit in the 11 digit number by 2, starting with the second to last digit (the 10th digit), and then subtract 9 from any result greater than 9. This step is called "doubling and subtracting 9."
 * Add up all the digits in the 11 digit number, including the checksum digit.
 * Round the sum up to the nearest multiple of 10.
 * Subtract the rounded sum from the next highest multiple of 10 to get the checksum digit.
 * Append the checksum digit to the end of the 10 digit number to get the final 12 digit number.
 * 
 */
function generateChecksum($number)
{
    // Convert the number to an array of digits
    $digits = str_split($number);
    $sum = 0;

    // Double and subtract 9 from every other digit, starting with the second to last digit
    for ($i = count($digits) - 2; $i >= 0; $i -= 2) {
        $digits[$i] = ($digits[$i] * 2 > 9) ? ($digits[$i] * 2 - 9) : ($digits[$i] * 2);
    }

    // Add up all the digits
    foreach ($digits as $digit) {
        $sum += $digit;
    }

    // Round up the sum to the nearest multiple of 10
    $roundedSum = ceil($sum / 10) * 10;

    // Subtract the rounded sum from the next highest multiple of 10 to get the checksum digit
    $checksum = $roundedSum - $sum;

    return $checksum;
}

function generateUniqueNumber($db,$schema)
{
    // Generate a random 10 digit number
    $number = mt_rand(1000000000, 9999999999);

    // Calculate the checksum digit
    $checksum = generateChecksum($number);

    // Append the checksum digit to the end of the number
    $numberWithChecksum = $number . $checksum;

    // Check if reference id already exists, generate a new random number if necessary
    while (is_exists('reference_id', $numberWithChecksum, $db,$schema)) {
        generateUniqueNumber($db);
    }

    // Return the final 12 digit number
    return $numberWithChecksum;
}

// Generate Random Numbers
function generateOTP($n) {
    $generator = "1357902468";
    $result = "";
    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand()%(strlen($generator))), 1);
    }
    return $result;
}
//generate session id
function generate_session_id()
{
    session_start();
    $session_id = session_id().date("Ymd").date("Hism");
    session_unset();
    session_destroy();
    return $session_id;
}