<?php
/* Smarty version 3.1.34-dev-7, created on 2024-08-09 23:39:11
  from 'C:\xampp\htdocs\fichador\templates\partials\modal.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_66b68c7f8b1f32_75587042',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cb53686c039512eaef9b4ec20762ef14833a6443' => 
    array (
      0 => 'C:\\xampp\\htdocs\\fichador\\templates\\partials\\modal.html',
      1 => 1710260695,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66b68c7f8b1f32_75587042 (Smarty_Internal_Template $_smarty_tpl) {
?><!-- MODAL ERROR FICHADOR -->

<div class="similmodal" id="error_modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <b>Error con la camara</b>
            </div>             
            <div class="modal-footer text-center">
                <div class="col-12 btn-card">
                    <label>No pudimos conectarnos con la camara para hacer el reconocimiento facial, 
                        verifique que la camara este andando como correponde y vuelva a intentar.</label>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="similmodal" id="reconocimiento_modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="col-md-12 col-12 m-t-2">
                    <div class="col-md-4 col-4">
                        <img src="" class="foto-card" id="foto_reconocimiento">
                    </div>
                    <div class="col-md-4 col-4 center">
                        <div class="wave"></div>
                        <div class="wave"></div>
                        <div class="wave"></div>
                        <div class="wave"></div>
                        <div class="wave"></div>
                    </div>
                    <div class="col-md-4 col-4">
                        <img src="" class="foto-card" id="foto_reconocimiento2">
                    </div>
                </div>
                <div class="col-md-12 col-12 m-t-2">
                    <label>Estamos realizando comparaciones faciales..</label>
                </div>
                <div class="col-md-12 col-12">
                    (Si usted intenta fichar con algun tipo de objeto la imagen ira a revision y se anulara el fichado)
                </div>
            </div>
            <!-- <div class="modal-footer text-center">
                <div class="col-12">
                    <div class="loader"></div>
                </div>
                <div class="col-12">
                    <label>Estamos realizando comparaciones faciales..</label>
                </div>
                <div class="col-12">
                    (Si usted intenta fichar con algun tipo de objeto la imagen ira a revision y se anulara el fichado)
                </div>
            </div> -->
        </div>
    </div>
</div>
<?php }
}
