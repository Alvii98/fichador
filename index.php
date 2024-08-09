<?php
require_once __DIR__ .'/settings/librerias.php';
require_once __DIR__ .'/class/consultas.php';

$ip = empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_REAL_IP'];
$pc = str_replace('.migraciones.local', '', gethostbyaddr($ip));
// print'<pre>';print_r($ip.'<br>'.$pc);exit;
// print'<pre>';print_r(datos::acceso_fichador($ip,$pc));exit;

$smarty->assign('RECONOCIMIENTOFACIAL', true);

$smarty->assign('MODAL_ERROR', $smarty->fetch('partials/modal.html'));

$smarty->display('index.html');
