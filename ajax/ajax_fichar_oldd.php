<?php
require_once __DIR__ .'/../class/consultas.php';

$cliente = new SoapClient('http://10.10.10.34/cgi-bin/wsrecursoshumanos/wsfichador/wsdl/IGenerarLicencia', array("cache_wsdl" => 0));
// $_POST['documento'] = '29472134';
// $_POST['op'] = 1;
$json = new StdClass();
$json->error = '';
$datos = '';

if (isset($_POST['documento']) && isset($_POST['op'])) {
    $datos_usuario = datos::datos_usuario($_POST['documento']);
    $datosFichado = datos::ultimo_cruce($_POST['documento']);
    if ($_POST['op'] == 1) {
        $permiso_fichador = datos::permiso_fichador($_POST['documento']);
        if ($permiso_fichador[0]['codigo'] == 1 || empty($datos_usuario)) {
            $json->error = 1;
        }elseif ($permiso_fichador[0]['codigo'] == 2) {
            $json->error = 2;
        }elseif ($permiso_fichador[0]['codigo'] == 3) {
            $json->error = 3;
        }elseif (minutos_ultimo_fichado($datosFichado[0]['HORA_CRUCE'])) {
            // Si no paso el minuto no puede volver a fichar
            $json->error = 5;
        }
        if (!empty($json->error)){
            print json_encode($json);exit;
        }
    }
    try {
        // Si no encuentra fichados arranca con entrada
        if (empty($datosFichado[0])) $cruce = 'ENTRADA';
        // Si pasa las 15 horas de la entrada vuelve a fichar como entrada
        elseif (pasaron_15hs($datosFichado[0])) $cruce = 'ENTRADA';
        // Si el ultimo fichado es entrada ficha salida
        elseif ($datosFichado[0]['CRUCE'] == 'ENTRADA') $cruce = 'SALIDA';
        else $cruce = 'ENTRADA';
        if ($_POST['op'] == 2) {
            $desc = 'FICHADO DE LA PERSONA';
            $usuario = strtoupper(utf8_encode($datos_usuario[0]['USER_LOGIN']));
            $lugar = $datos_usuario[0]['LUGAR_PRESTA_SERVICIO'];
            $confirmar = 1; // EN EL SERVICIO SI ES ENTRADA LO PONE EN 0
            $ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_REAL_IP'];
            $pc = str_replace('.migraciones.local', '', gethostbyaddr($ip));
            if (datos::acceso_fichador($ip,$pc)[0]['facial'] == 1) {
                $formaCarga = 'FACIAL';
            }else {
                $formaCarga = 'MANUAL';
            }
            $json->ip = $ip;
            $host = $_SERVER['HTTP_HOST'];
            if ($host == 'www.dnm.gov.ar') {
                $crearFichado = $cliente->crearfichado($_POST['documento'],$cruce,date("d/m/Y H:i:s"),0,$desc,$usuario,$lugar,$confirmar,$formaCarga,$ip,'host');
            }
            $json->fichado = 'Fichado correctamente.';
        }
    } catch (\Throwable $th) {
        $json->error = 1;
    }
    if ($_POST['op'] == 1) {
        $datos = array();
        foreach ($datos_usuario as $value) {
            $ultimos_fichados = array();
            foreach ($datosFichado as $value2) {
                $ultimos_fichados[] = array('hora_de_cruce' => empty($value2['HORA_CRUCE']) ? '' : DateTime::createFromFormat('j/n/Y H:i:s', $value2['HORA_CRUCE'])->format('d/m/Y H:i:s'),
                'cruce' => $value2['CRUCE']);
            }
            $datos[] = array('apellido' => ucwords(mb_strtolower(utf8_encode($value['APELLIDO']))),
            'nombre' => ucwords(mb_strtolower(utf8_encode($value['NOMBRE']))),
            'documento_nro' => $value['NUM_DOC'],
            'ultimos_fichados' => $ultimos_fichados,
            'cruce' => $cruce);
        }
    }
}

$json->datos = $datos;
print json_encode($json);exit;


function pasaron_15hs($datos){
    if (empty($datos)) return false;
    if ($datos['CRUCE'] == 'SALIDA') return false; 
    $date = DateTime::createFromFormat('j/n/Y H:i:s', $datos['HORA_CRUCE'])->format('Y-m-d H:i:s');
    // Crear objetos DateTime para las dos fechas
    $fecha1 = new DateTime($date);
    $fecha2 = new DateTime();
    // Calcular la diferencia de tiempo entre las dos fechas
    $diferencia = $fecha1->diff($fecha2);
    if ($diferencia->y >= 1) return true;
    if ($diferencia->m >= 1) return true;
    if ($diferencia->d >= 1) return true;
    if ($diferencia->h >= 15) return true;
    return false;
}

function minutos_ultimo_fichado($date_time){
    if (empty($date_time)) return false;
    $date = DateTime::createFromFormat('j/n/Y H:i:s', $date_time)->format('Y-m-d H:i:s');
    // Crear objetos DateTime para las dos fechas
    $fecha1 = new DateTime($date);
    $fecha2 = new DateTime();
    // Calcular la diferencia de tiempo entre las dos fechas
    $diferencia = $fecha1->diff($fecha2);
    if ($diferencia->y >= 1) return false;
    if ($diferencia->m >= 1) return false;
    if ($diferencia->d >= 1) return false;
    if ($diferencia->h >= 1) return false;
    if ($diferencia->i <= 1) return true;
    return false;
}