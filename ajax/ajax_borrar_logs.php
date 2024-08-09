<?php

for ($i=15; $i <= 18 ; $i++) { 

    $fecha_actual = new DateTime();
    // Resta dias
    $fecha = $fecha_actual->sub(new DateInterval('P'.strval($i).'D'));
    
    $dayName = $fecha->format('d').$fecha->format('m').$fecha->format('Y');
    $foto_camara = 'foto_camara/'.$dayName.'_fotos/';
    $dayName = $fecha->format('Y').$fecha->format('m').$fecha->format('d');
    $log = 'log/'.$dayName.'_log.txt';

    if (is_file($log)) {
        unlink($log);

        if (is_file($log)) {
            $msjLog = '['.date("Y-m-d H:i:s").'] Se elimino el archivo '.$log.". \n";
            file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
        }else {
            $msjLog = '['.date("Y-m-d H:i:s").'] No se pudo eliminar el archivo '.$log.". \n";
            file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
        }
    }else {
        $msjLog = '['.date("Y-m-d H:i:s").'] No encontramos el archivo '.$log.". \n";
        file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
    }

    if (is_dir($foto_camara)) {
        try {
            $fotos = glob($foto_camara . '*');
            foreach ($fotos as $foto) {
                if (is_file($foto)) {
                    unlink($foto);
                }
            }
            rmdir($foto_camara);
            if (is_dir($foto_camara)) {
                $msjLog = '['.date("Y-m-d H:i:s").'] No se pudo eliminar la carpeta '.$foto_camara.". \n";
                file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
            }else {
                $msjLog = '['.date("Y-m-d H:i:s").'] Se elimino la carpeta '.$foto_camara.". \n";
                file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
            }
        } catch (\Throwable $th) {
            // print_r($th);
            $msjLog = '['.date("Y-m-d H:i:s").'] No se pudo eliminar la carpeta '.$th.". \n";
            file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
        }
    }else {
        $msjLog = '['.date("Y-m-d H:i:s").'] No se encontro la carpeta '.$foto_camara.". \n";
        file_put_contents('log/log_eliminados.txt', $msjLog, FILE_APPEND);
    }
}
