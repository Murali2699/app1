<?php

/**
 * Function to check if the mobile number email already exists
 */
function check_user_exists($mobile_number, $db,$schema_name)
{
    //$sql = "SELECT * FROM ".$schema_name.".vao_list where mobile_no= ?";
    $sql = "SELECT * FROM ".$schema_name.".fngetuserbymobileno(?)";
    $sql_stmt = $db->prepare($sql);
    $sql_stmt->execute(array($mobile_number));
    $exists_count = $sql_stmt->rowCount();
    if ($exists_count == 0) {
        return false;
    } else {
        return true;
    }
}
