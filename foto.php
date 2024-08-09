<?php
require_once __DIR__ .'/libs/FTPFileManager/FTPFileManager.php'; 

try {
    $pathFoto = '/Foto/'.$_GET['dni'].'foto.jpg';
    
    $fotoBase64 = FTPFileManager::base64($pathFoto);
    
    if(!empty($fotoBase64)){
        header('Content-Type: image/jpg');
        echo $fotoBase64;
    }else{
        $imagen = fopen("img/foto_no_disponible.jpg", 'rb');
        fpassthru($imagen);
    }

} catch (\Throwable $th) {
    //throw $th;
    $imagen = fopen("img/foto_no_disponible.jpg", 'rb');
    fpassthru($imagen);
}

?>