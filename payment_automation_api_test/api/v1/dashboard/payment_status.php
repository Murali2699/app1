<?php
//$now = DateTime::createFromFormat('U.u', microtime(true));
//$request_dt = $now->format("Y-m-d H:i:s.u");
header("Content-type: application/json; charset=utf-8");
$start_time = microtime(true);
$payload = $_POST;
//require_once('../../../helper/header.php');
require_once('../../../helper/kmut_payment_db.php');
require_once('../../../helper/write_database.php');

//$data = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $headers = apache_request_headers();




    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    //print_r($data['Request']['PayMode']);exit;
    // $userid = isset($data['Request']['UserID']) ? $data['Request']['UserID'] : '';
    // $password = isset($data['Request']['Password']) ? $data['Request']['Password'] : '';
    $applicationreferenceno = isset($data['Request']['ApplicationReferenceNo']) ? $data['Request']['ApplicationReferenceNo'] : '';
    $type = isset($data['Request']['TYPE']) ? $data['Request']['TYPE'] : '';
    $oprcode = isset($data['Request']['OprCode']) ? $data['Request']['OprCode'] : '';
    $paidamount = isset($data['Request']['PaidAmount']) ? $data['Request']['PaidAmount'] : '';
    $usercharge = isset($data['Request']['UserCharge']) ? $data['Request']['UserCharge'] : '';
    $transno = isset($data['Request']['TRANNO']) ? $data['Request']['TRANNO'] : '';
    $paymode = isset($data['Request']['PayMode']) ? $data['Request']['PayMode'] : '';
    $paidamount = (string)$paidamount;



    if ($headers['Authorization'] == 'Basic Og==') 
    {
       http_response_code(200);
       $data = array("RESCODE" => 401, "RESPMSG" => "Authorized Username","EDISTTXNNO"=>$transno,"RECEIPTDATA"=>"");
       echo json_encode(array("Response"=>$data));
       die();
    } 
    

    if ( !empty($applicationreferenceno) && !empty($type) && !empty($oprcode) && !empty($transno) && !empty($paymode) && $paidamount != '' && $usercharge != '')
    {
       
        if ($type == 'CHECKSTATUS') 
        {
            $update_sql = "SELECT response_code from fn_update_appeal_transaction(
                             '$applicationreferenceno', 
                             '{$oprcode}',               
                             '{$type}',
                             $paidamount,
                             $usercharge,
                             '{$transno}',
                             '{$paymode}'
                         );";
            $paymentupdate_stmt = $write_db->prepare($update_sql);
            $paymentupdate_stmt->execute();
             $result = $paymentupdate_stmt->fetchAll(PDO::FETCH_ASSOC);
             $response_code = $result[0]['response_code'];
             if ($response_code == 0) 
             {
                 $status = "Pending For Payment";
             } 
             else if($response_code == 1)
             {
                $status = "Invalid Application";
             }
             else if($response_code == 2)
             {
                $status = "Already Processed";
             }
              http_response_code(200);
          $data = array("RESCODE" => $response_code, "RESPMSG" => $status,"EDISTTXNNO"=>$transno,"RECEIPTDATA"=>"");
          echo json_encode(array("Response"=>$data));
          $write_db=null;
        } 
        else if($type == 'PAYMENTCONFIRMATION')
        {
           
           $update_sql = "SELECT response_code from fn_update_appeal_transaction(
                            '$applicationreferenceno', 
                            '{$oprcode}',               
                            '{$type}',
                            $paidamount,
                            $usercharge,
                            '{$transno}',
                            '{$paymode}'
                        );";
           $paymentupdate_stmt = $write_db->prepare($update_sql);
           $paymentupdate_stmt->execute();
           $paymentupdate= $paymentupdate_stmt->rowCount();
           if ($paymentupdate >= 1) {
               $result = $paymentupdate_stmt->fetchAll(PDO::FETCH_ASSOC);
               $response_code = $result[0]['response_code'];
               if ($response_code == 0) 
               {
                   $status = "Success";
               } 
               else if($response_code == 1)
               {
                  $status = "Invalid Application";
               }
               else if($response_code == 2)
               {
                  $status = "Already Processed";
               }
               http_response_code(200);
               $data = array("RESCODE" => $response_code, "RESPMSG" => $status,"EDISTTXNNO"=>$transno,"RECEIPTDATA"=>"");
               echo json_encode(array("Response"=>$data));

              $write_db=null;
               die();
           } 
           else 
           {
               http_response_code(200);
              $data = array("RESCODE" => $response_code, "RESPMSG" => $status,"EDISTTXNNO"=>$transno,"RECEIPTDATA"=>"");
              echo json_encode(array("Response"=>$data));

               $write_db=null;
               die();
           }
            

        }
        


       
    }
    else
    {
        http_response_code(405);
        $data = array("success" => 0, "message" => "Parameter Missing");
        echo json_encode($data);
      
    }

   exit();
   
} 
else 
{
    http_response_code(405);
    $data = array("success" => 0, "message" => "Method Not Allowed");
    echo json_encode($data);
    die();
}
