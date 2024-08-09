<?php
require_once __DIR__ .'/Singleton.php';

class datos{
    static public function acceso_fichador($ip,$pc){

        $instancia = SingletonConexion::getInstance();
        $conn = $instancia->getConnection();
        
        $query = "SELECT * FROM tabla_direccion_ip where ip = :ip or UPPER(pc) = :pc";

        $result = $conn->prepare($query);
        // $pc = '%'.strtoupper($pc).'%';
        $result->bindParam(':ip', $ip, PDO::PARAM_STR, 40);
        $result->bindParam(':pc', strtoupper($pc));

        $result->execute();

        return $result->fetchAll();
    }

    static public function datos_persona($documento){

        $instancia = SingletonConexion::getInstance();
        $conn = $instancia->getConnection();

        $query = "SELECT apellido, nombre, sexo, legajo,caracter as unidad, descripcion1 as departamento, oficio as tarea, categoria,nivel,
        grado, cuit,documento_tipo,documento_nro, ingreso, mail,fecha_nac,
        honorario,apto_medico,direccion,telefono,cd_postal, tipo_localidad,ingreso,funcion_ejecutiva, oficio_2, fecha_apn,sexo 
        from personalactivo where (fecha_baja > curdate() or fecha_baja is null) and ingreso <= curdate() and documento_nro = '".$documento."'";

        $result = $conn->prepare($query);

        // $result->bindParam(':doc', $documento, PDO::PARAM_STR, 8);

        $result->execute();

        return $result->fetchAll();
    }

    static public function control_medico($documento){

        $instancia = SingletonConexion::getInstance();
        $conn = $instancia->getConnection();

        $query = "SELECT agentes.Apellido AS APELLIDO,agentes.Nombre AS NOMBRE, licencia.uniones,
        if(((agentes_medicina_laboral.fecha_inicio is not null) and isnull(agentes_medicina_laboral.fecha_final))
        ,if(((agentes_medicina_laboral.fecha_inicio > curdate()) and (agentes_medicina_laboral.fecha_inicio_parte =
        agentes_medicina_laboral.fecha_inicio)),'Presente','Ausente'),
        If ((agentes_medicina_laboral.fecha_inicio  <=curdate()) and  ((agentes_medicina_laboral.fecha_final >= curdate())), 'Ausente',
        'Presente')) AS Medico,agentes.Documento_nro, agentes_medicina_laboral.fecha_inicio ,agentes_medicina_laboral.fecha_final
        from agentes left join agentes_medicina_laboral on (agentes.Documento_nro = agentes_medicina_laboral.documento_nro)
        left join licencia  on (licencia.codigo = agentes_medicina_laboral.codigo_licencia and licencia.exepto = 0)
        where  agentes.Ingreso <= curdate() and ((isnull(agentes_medicina_laboral.fecha_inicio)
        and isnull(agentes_medicina_laboral.fecha_final)) or (agentes_medicina_laboral.fecha_inicio = (select j.fecha_inicio AS fecha_inicio
        from agentes_medicina_laboral j where (j.documento_nro = agentes_medicina_laboral.documento_nro) order by j.documento_nro,j.fecha_inicio desc limit 1)))
        AND AGENTES.DOCUMENTO_NRO = :doc order by agentes.Apellido,agentes.nombre";

        $result = $conn->prepare($query);

        $result->bindParam(':doc', $documento, PDO::PARAM_STR, 8);

        $result->execute();

        $result = $result->fetchAll();
        
        if (trim($result[0]['Medico']) == 'Presente') return false;
        else return true;
    }

    static public function impedir_fichado($documento){

        $instancia = SingletonConexion::getInstance();
        $conn = $instancia->getConnection();

        $query = "SELECT alarma FROM sistema_cambiar WHERE num_doc = :doc";

        $result = $conn->prepare($query);

        $result->bindParam(':doc', $documento, PDO::PARAM_STR, 8);

        $result->execute();

        $result = $result->fetchAll();

        if (empty($result[0]['alarma'])) return false;
        else if ($result[0]['alarma'] == 'F') return false;
        else return true;
    }

    static public function suspendido($documento){

        $instancia = SingletonConexion::getInstance();
        $conn = $instancia->getConnection();

        $query = "SELECT estado,estado_suspension FROM sistema_cambiar WHERE num_doc = :doc";

        $result = $conn->prepare($query);

        $result->bindParam(':doc', $documento, PDO::PARAM_STR, 8);

        $result->execute();

        $result = $result->fetchAll();

        if (trim($result[0]['ESTADO']) == 'ACTIVO' && trim($result[0]['ESTADO_SUSPENSION']) == 'NORMAL') return false;
        else return true;
    }

    static public function permiso_fichador($documento){

        $instancia = SingletonConexion::getInstance();
        $conn = $instancia->getConnection();

        $query = "CALL permiso_fichador(:doc)";

        $result = $conn->prepare($query);

        $result->bindParam(':doc', $documento, PDO::PARAM_STR, 8);

        $result->execute();

        return $result->fetchAll();
    }

    static public function datos_usuario($doc){

        $instancia = SingletonConexion::getInstance();        
        $conn = $instancia->getConnection2();

        $query = "SELECT pwd_data.users.id_per,user_login,num_doc,apellido,nombre,lugar_presta_servicio 
        FROM PERS_DATA.AGENTES,PWD_DATA.USERS
        WHERE documento = :doc AND num_doc = :doc";

        $result = $conn->prepare($query);

        $result->bindParam(':doc', $doc, PDO::PARAM_STR, 8);

        $result->execute();

        return $result->fetchAll();
    }

    static public function ultimo_cruce($doc){

        $instancia = SingletonConexion::getInstance();        
        $conn = $instancia->getConnection2();

        $query = "SELECT cruce,TO_CHAR(hora_de_cruce, 'DD/MM/YYYY HH24:MI:SS') as hora_cruce
        FROM PERS_DATA.REGISTROS_ENTRADA_SALIDA
        WHERE id_per = (select id_per FROM PERS_DATA.AGENTES where num_doc = '40756445') 
        ORDER BY hora_de_cruce DESC FETCH FIRST 2 ROWS ONLY";

        $result = $conn->prepare($query);

        $result->bindParam(':doc', $doc, PDO::PARAM_STR, 8);

        $result->execute();

        return $result->fetchAll();
    }
}

// print'<pre>';print_r(datos::datos_persona('397235562'));//18471805

