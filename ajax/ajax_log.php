<?php
$json = new StdClass();
$json->resp = 0;

if (isset($_POST['msj'])) {
    $msjLog = "[".date("Y-m-d H:i:s")."] ".$_POST['msj'];
    $msjLog .= " ip ".$_SERVER['REMOTE_ADDR']." Dispositivo ".$_SERVER['HTTP_USER_AGENT']." \n";   
    $now = new DateTime();
    $dayName = $now->format('Y').$now->format('m').$now->format('d');
    file_put_contents('log/'.$dayName."_log.txt", $msjLog, FILE_APPEND);
    $json->resp = 1;
}else {
    $msjLog = "[".date("Y-m-d H:i:s")."] Intento fallido";
    $msjLog .= " ip ".$_SERVER['REMOTE_ADDR']." Dispositivo ".$_SERVER['HTTP_USER_AGENT']." \n";    
    $now = new DateTime();
    $dayName = $now->format('Y').$now->format('m').$now->format('d');
    file_put_contents('log/'.$dayName."_log.txt", $msjLog, FILE_APPEND);
    $json->resp = 0;
}

print json_encode($json);