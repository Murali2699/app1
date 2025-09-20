<?php

function generate_user_id($name, $db,$schema_name)
{
    
    $user_id = preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($name)));
    $random_num = rand(100, 999);
    $user_id = $user_id . '_' . $random_num;
    while (is_exists('user_id', $user_id, $db,$schema_name)) {
        generate_user_id($name, $db,$schema_name);
    }
    return $user_id;
}
function is_exists($type, $value, $db,$schema_name)
    if ($type == 'user_id') {
        $col = 'user_id';
    } else if ($type == 'reference_id') {
        $col = 'reference_id';
    $check_user_id_sql = "SELECT * FROM ".$schema_name.".users where $col=?";
    $check_user_id_stmt = $db->prepare($check_user_id_sql);
    $check_user_id_stmt->execute(array($value));
    $check_user_id_count = $check_user_id_stmt->rowCount();
    if ($check_user_id_count === 0) {
        return false;
    } else {
        return true;
function generateChecksum($number)
    $digits = str_split($number);
    $sum = 0;
    for ($i = count($digits) - 2; $i >= 0; $i -= 2) {
        $digits[$i] = ($digits[$i] * 2 > 9) ? ($digits[$i] * 2 - 9) : ($digits[$i] * 2);
    foreach ($digits as $digit) {
        $sum += $digit;
    $roundedSum = ceil($sum / 10) * 10;
    $checksum = $roundedSum - $sum;
    return $checksum;
function generateUniqueNumber($db,$schema)
    $number = mt_rand(1000000000, 9999999999);
    $checksum = generateChecksum($number);
    $numberWithChecksum = $number . $checksum;
    while (is_exists('reference_id', $numberWithChecksum, $db,$schema)) {
        generateUniqueNumber($db);
    return $numberWithChecksum;
function generateOTP($n) {
    $generator = "1357902468";
    $result = "";
    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand()%(strlen($generator))), 1);
    return $result;
function generate_session_id()
    session_start();
    $session_id = session_id().date("Ymd").date("Hism");
    session_unset();
    session_destroy();
    return $session_id;