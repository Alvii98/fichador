<?php
// $iniConf = parse_ini_file("Configuracion/SeteosFTP_PERSONAL.ini", true);
define('_SERVER_', '10.10.10.90');
define('_BDD_', 'Personal1');
define('_BDD_USER_', 'root');
define('_BDD_PASS_', 'chiavari');

// define(_USER_ORA_, 'consulta_web');
// define(_PASS_ORA_, 'web');
define('_USER_ORA_', 'PERSONAL');
define('_PASS_ORA_', 'personal2008');
$host = $_SERVER['HTTP_HOST'];
//Segun el host a que base se apunta
define('_SERVER_ORA_','NOMINAS');
