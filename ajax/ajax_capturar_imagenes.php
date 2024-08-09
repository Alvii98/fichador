<?php

$json = new StdClass();
$json->resp = 0;

if (isset($_POST['foto_camara']) && isset($_POST['documento'])) {
    $img = $_POST['foto_camara'];
    
    if(strpos($img, 'data:image/png;base64,') !== FALSE){
        $img = str_replace('data:image/png;base64,', '', $img);
    }elseif (strpos($img, 'data:image/jpg;base64,') !== FALSE) {
        $img = str_replace('data:image/jpg;base64,', '', $img);
    }elseif (strpos($img, 'data:image/jpeg;base64,') !== FALSE) {
        $img = str_replace('data:image/jpeg;base64,', '', $img);
    }else{
        $json->foto_camara = 1;
        print json_encode($json);exit;
    }
    $img = str_replace(' ', '+', $img);
    $img = base64_decode($img);
    $now = new DateTime();
    $dayName = $now->format('d').$now->format('m').$now->format('Y');
    $foto_camara = 'log_fotos/'.$dayName.'_fotos/';
    if (!is_dir($foto_camara)) mkdir($foto_camara, 0777, true);

    $hsName = $now->format('H').$now->format('i');
    $foto_camara = $foto_camara.$_POST['documento'].'_'.$hsName.'.png';

    if (!file_exists('fotos/'.$_POST['documento'].'.png')) {
        if (file_put_contents('fotos/'.$_POST['documento'].'.png',$img) !== false) {
            $json->foto_camara = 1;
        }
    }

    if (file_put_contents($foto_camara,$img) !== false) {
        $json->foto_camara = 1;
    }else {
        $json->foto_camara = 1;
    }
}else if (isset($_POST['documento'])) {
    if(!empty($_POST['documento'])){
        if (file_exists('fotos/'.$_POST['documento'].'.png')) {
            $foto = file_get_contents('fotos/'.$_POST['documento'].'.png');
            $json->foto = base64_encode($foto);
            $json->resp = 1;
        }else {
            $json->resp = 0;
        }
    }
}

print json_encode($json);