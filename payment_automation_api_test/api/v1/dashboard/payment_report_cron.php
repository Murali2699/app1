<?php
ini_set('max_execution_time', 0);
date_default_timezone_set('Asia/Calcutta');


/* local*/
// require_once('../../helper/write_database.php');

/* staging*/
 require_once('var/www/html/payment_automation_api/api/payment/helper/write_database.php');

/* production*/
// require_once('/var/www/html/kmut/payment_automation/payment_automation_api/api/payment/helper/write_database.php');

$sql1 = "select * from public.view_payment_report";
$sql1_stmt = $write_db->prepare($sql1);
$sql1_stmt->execute();

echo "Payment Report Running Cron...";


/*$sql2 = "call sp_summary_benficiarycount()";
$sql2_stmt = $write_db->prepare($sql2);
$sql2_stmt->execute();

echo "Sync 2 Complete";*/
