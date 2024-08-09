<?php
/* Smarty version 3.1.34-dev-7, created on 2024-08-09 23:39:11
  from 'C:\xampp\htdocs\fichador\templates\index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_66b68c7fa8dd67_36077928',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5329e2f259e5bc67cafdabc1839878c58d37e874' => 
    array (
      0 => 'C:\\xampp\\htdocs\\fichador\\templates\\index.html',
      1 => 1723239364,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66b68c7fa8dd67_36077928 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gesti&oacute;n horaria</title>
  <!-- BOOTSTRAP 4.6 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <!-- CSS ESTILOS -->
  <link rel="stylesheet" href="css/poncho.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/estilo.css?<?php echo $_smarty_tpl->tpl_vars['NO_CACHE']->value;?>
">
  <!-- Alertify -->
  <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"><?php echo '</script'; ?>
>
  <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" rel="stylesheet">

  <?php echo '<script'; ?>
 src="js/funciones.js?<?php echo $_smarty_tpl->tpl_vars['NO_CACHE']->value;?>
"><?php echo '</script'; ?>
>
  <?php if ($_smarty_tpl->tpl_vars['RECONOCIMIENTOFACIAL']->value) {?>
  <!-- Face Api -->
  <?php echo '<script'; ?>
 src="libs/face-api/face-api.min.js"><?php echo '</script'; ?>
>
  <!-- p5 -->
  <?php echo '<script'; ?>
 src="libs/p5-ml5/p5.min.js"><?php echo '</script'; ?>
>
  <!-- ml5 -->
  <?php echo '<script'; ?>
 src="libs/p5-ml5/ml5.min.js"><?php echo '</script'; ?>
>
  
  <?php echo '<script'; ?>
 src="js/fichado_con_reconocimiento.js?<?php echo $_smarty_tpl->tpl_vars['NO_CACHE']->value;?>
"><?php echo '</script'; ?>
>
  <div id="reconocimiento_activo"></div>
  <?php } else { ?>
  <?php echo '<script'; ?>
 src="js/fichado_manual.js?<?php echo $_smarty_tpl->tpl_vars['NO_CACHE']->value;?>
"><?php echo '</script'; ?>
>
  <?php }?>
  
</head>
<body onload="mueveReloj()">
  <?php echo $_smarty_tpl->tpl_vars['MODAL_ERROR']->value;?>

  <div class="container m-t-1 colgar_fichador" id="container">
    <div class="row d-flex justify-content-center">
      <div class="col-md-8 bd-tab" style="padding-bottom: 20px !important;">
        <div class="col-md-12 p-l-0 p-r-0">
          <img class="image-responsive" alt="DNM" src="img/banner.bmp" width="100%">
        </div>
        <div class="col-md-12">
          <div class="col-md-7">
            <h1 class="m-b-0"><span style="color: #45658d;">Sistema de fichado</span></h1>
          </div>
          <div class="col-md-5">
            <div class="col-md-4 m-t-2">
              <i class="bi bi-clock reloj"></i>
            </div>
            <div class="col-md-8 m-t-2">
              <h5 class="m-t-1">Hora actual:</h5>
              <p class="h2" id="hora_actual"></p>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <hr>
        </div>

        <div class="col-md-12">
          <div class="col-md-7 p-r-2">
            <h6>Documento:</h6>
            <input type="text" class="form-control" maxlength="8" onkeypress="return valideKey(event);" id="documento"
              autocomplete="off">
            <P style="font-size: 14px; margin-top: 10px;">Si la carga fue manual presiona <strong>"Enter"</strong></P>
            <!--tabla registro-->
            <h6 class="m-t-1">Registros:</h6>
            <table class="table table-bordered" id="tabla_registro">
              <thead>
                <tr>
                  <th>Cruce</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody id="ultimos_registros">
                <tr style="height:30px;">
                  <td></td>
                  <td></td>
                </tr>
                <tr style="height:30px;">
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-5 p-l-2 m-t-2 p-t-1">
            <img src="img/foto.bmp" id="foto_persona" height="222" width="222" style="border-radius: 10px;">
            <label id="ape_nom"></label>
          </div>
        </div>

        <!-- <div class="col-md-12">
          <hr class="m-b-1 m-t-1">
        </div>
        
        <div class="col-md-12 m-t-0">
          <div class="col-md-7">
            Usuario logueado: <strong>Automatico</strong>
          </div>
          <div class="col-md-5">
            <?php if ($_smarty_tpl->tpl_vars['RECONOCIMIENTOFACIAL']->value) {?>
              Rol: <strong>Manual / Facial</strong>
            <?php } else { ?>
              Rol: <strong>Manual</strong>
            <?php }?>
          </div>
        </div> -->
      </div>
    </div>
  </div>
  <video id="video" autoplay width="640" height="480"></video>
  <main><canvas id="canvas" width="640" height="480"></canvas></main>
  <!-- <video id="video" width="640" height="480" autoplay></video>
   <video id="video" autoplay playsinline></video> -->
</body>
</html><?php }
}
