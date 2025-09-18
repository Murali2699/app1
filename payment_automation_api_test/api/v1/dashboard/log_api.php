<?php

function log_api($userid,$response_status_code,$session_id,$payload,$db,$schema,$requestmethod,$operationtype,$request_dt,$response_dt,$response_time_ms,$error_detail)
{
    $payload = json_encode($payload);
    $error_detail = json_encode($error_detail);
    //$response_params = json_encode($response_params);
    $userip = $_SERVER['REMOTE_ADDR'];
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $session = $session_id == '' ? '' : $session_id;

    if (preg_match('/linux/i', $browser)) {
    $platform = 'linux';
    }elseif (preg_match('/macintosh|mac os x/i', $browser)) {
        $platform = 'mac';
    }elseif (preg_match('/windows|win32/i', $browser)) {
        $platform = 'windows';
    }
    else
    {
        $platform = 'mobile';
    }

    // Next get the name of the useragent yes seperately and for good reason
      if(preg_match('/MSIE/i',$browser) && !preg_match('/Opera/i',$browser)){
        $browser_name = 'Internet Explorer';
        $ub = "MSIE";
      }elseif(preg_match('/Firefox/i',$browser)){
        $browser_name = 'Mozilla Firefox';
        $ub = "Firefox";
      }elseif(preg_match('/OPR/i',$browser)){
        $browser_name = 'Opera';
        $ub = "Opera";
      }elseif(preg_match('/Chrome/i',$browser) && !preg_match('/Edge/i',$browser)){
        $browser_name = 'Google Chrome';
        $ub = "Chrome";
      }elseif(preg_match('/Safari/i',$browser) && !preg_match('/Edge/i',$browser)){
        $browser_name = 'Apple Safari';
        $ub = "Safari";
      }elseif(preg_match('/Netscape/i',$browser)){
        $browser_name = 'Netscape';
        $ub = "Netscape";
      }elseif(preg_match('/Edge/i',$browser)){
        $browser_name = 'Edge';
        $ub = "Edge";
      }elseif(preg_match('/Trident/i',$browser)){
        $browser_name = 'Internet Explorer';
        $ub = "MSIE";
      }
      else
      {
        $browser_name = 'chrome';
      }
      //$response_time_ms = round($response_time_ms);
      $response_time_ms = round($response_time_ms * 10000);

      $txt = $userid.','.$userip.','.$session.','.$browser_name.','.$platform.','.$requestmethod.','.$operationtype.','.$payload.','.$request_dt.','.$response_dt.','.$response_status_code.','.$response_time_ms.','.$error_detail;
      // $myfile = file_put_contents('logs/appeal_logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

      $uchwyt = fopen("logs/".date("dM").".txt", "a");
      if(file_exists("logs/".date("dM").".txt"))
      {
        $myfile = file_put_contents("logs/".date("dM").".txt", $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
      }
      else
      {
      fwrite($uchwyt, $txt);
      fclose($uchwyt);
      }

    /*CALL public.spsaveapiaccesslog(
    <IN $userid, character varying>, 
    <IN $userip, character varying>, 
    <IN $session, character varying>, 
    <IN $browser, character varying>, 
    <IN $platform, character varying>, 
    <IN $requestmethod, character varying>, 
    <IN $operationtype, character varying>, 
    <IN $payload, text>, 
    <IN $request_dt, timestamp without time zone>, 
    <IN $response_dt, timestamp without time zone>, 
    <IN $response_status_code, character varying>, 
    <IN $response_time_ms, integer>, 
    <OUT $logid, bigint>
    )*/
    // $log_sql = "CALL public.spsaveapiaccesslog(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    // $log_sth = $db->prepare($log_sql);
    // if($log_sth->execute(array($userid,$userip,$session,$browser_name,$platform,$requestmethod,$operationtype,$payload,$request_dt,$response_dt,$response_status_code,$response_time_ms,$error_detail,'1')))
    // {

    // }
    // else
    // {
    
    // }
}

?>