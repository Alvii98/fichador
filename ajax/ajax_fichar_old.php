<?php
require_once __DIR__ .'/../class/consultas.php';

$cliente = new SoapClient('http://10.10.10.34/cgi-bin/wsrecursoshumanos/wsfichador/wsdl/IGenerarLicencia', array("cache_wsdl" => 0));
// $_POST['documento'] = '27941759';
// $_POST['op'] = 1;
$json = new StdClass();
$json->error = '';
$datos = '';

if (isset($_POST['documento']) && isset($_POST['op'])) {
    $datos_usuario = datos::datos_usuario($_POST['documento']);
    $datosFichado = $cliente->Consultafichado($_POST['documento']);
    if ($_POST['op'] == 1) {
        $datos_persona = datos::datos_persona($_POST['documento']);
        if (empty($datos_persona) && empty($datos_usuario) ) {
            $json->error = 1;
        }elseif (datos::control_medico($_POST['documento'])) {
            $json->error = 2;
        }elseif (datos::impedir_fichado($_POST['documento'])) {
            $json->error = 3;
        }elseif (datos::suspendido($_POST['documento'])) {
            $json->error = 4;
        }
        if (minutos_ultimo_fichado($datosFichado->vistafichado[0]->hora_de_cruce)) {
            // Si no paso el minuto no puede volver a fichar
            $json->error = 5;
        }
        
        if (!empty($json->error)){
            print json_encode($json);exit;
        }
    }
    try {
        // Si no encuentra fichados arranca con entrada
        if (empty($datosFichado->vistafichado[0])) $cruce = 'ENTRADA';
        // Si pasa las 15 horas de la entrada vuelve a fichar como entrada
        elseif (pasaron_15hs($datosFichado->vistafichado[0])) $cruce = 'ENTRADA';
        // Si el ultimo fichado es entrada ficha salida
        elseif ($datosFichado->vistafichado[0]->cruce == 'ENTRADA') $cruce = 'SALIDA';
        else $cruce = 'ENTRADA';
        
        if ($_POST['op'] == 2) {
            $desc = 'FICHADO DE LA PERSONA';
            $usuario = strtoupper(utf8_encode($datos_usuario[0]['USER_LOGIN']));
            $lugar = $datos_usuario[0]['LUGAR_PRESTA_SERVICIO'];
            $confirmar = 1; // TENGO QUE ACOMODARLO QUE SI ES ENTRADA = 0 Y SI ES SALIDA TENGO QUE HACER UPDATE AL ULTIMO EN 1
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
            // print_r($crearFichado);exit;
            $json->fichado = 'Fichado correctamente.';
        }
    } catch (\Throwable $th) {
        $json->error = 1;
    }
    if ($_POST['op'] == 1) {
        $datos = array();
        foreach ($datos_persona as $value) {
            $ultimos_fichados = array();
            foreach ($datosFichado->vistafichado as $value2) {
                $ultimos_fichados[] = array('hora_de_cruce' => empty($value2->hora_de_cruce) ? '' : DateTime::createFromFormat('j/n/Y H:i:s', $value2->hora_de_cruce)->format('d/m/Y H:i:s'),
                'cruce' => $value2->cruce);
            }
            $datos[] = array('apellido' => ucwords(mb_strtolower(utf8_encode($value['apellido']))),
            'nombre' => utf8_encode($value['nombre']),
            'documento_nro' => $value['documento_nro'],
            'ultimos_fichados' => $ultimos_fichados,
            'cruce' => $cruce);
        }
    }
}

$json->datos = $datos;
print json_encode($json);exit;


function pasaron_15hs($datos){
    if (empty($datos)) return false;
    if ($datos->cruce == 'SALIDA') return false; 
    $date = DateTime::createFromFormat('j/n/Y H:i:s', $datos->hora_de_cruce)->format('Y-m-d H:i:s');
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