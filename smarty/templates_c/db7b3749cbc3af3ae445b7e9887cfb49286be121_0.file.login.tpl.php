<?php
/* Smarty version 3.1.31, created on 2018-03-22 20:37:37
  from "/var/www/bc-comet_de/intern/smarty/templates/login.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ab406015a6713_77365787',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'db7b3749cbc3af3ae445b7e9887cfb49286be121' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/login.tpl',
      1 => 1521737109,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5ab406015a6713_77365787 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<body id="login">

<!--
    you can substitue the span of reauth email for a input with the email and
    include the remember me checkbox
    -->
    <div class="container">
        <div class="card card-container">
            <!-- <div class="alert alert-danger">
              Wartungsarbeiten! Es kann zu Störungen kommen!
            </div>
          -->
            <img id="profile-img" class="profile-img-card" src="/static/img/badminton.png" />
            <p id="profile-name" class="profile-name-card"></p>
            <?php echo $_smarty_tpl->tpl_vars['content']->value;?>


        </div><!-- /card-container -->
    </div><!-- /container -->

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php }
}
