<?php
/* Smarty version 3.1.31, created on 2018-02-02 11:33:41
  from "/var/www/bc-comet_de/intern/smarty/templates/login/request_password.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a743e85476b32_58383270',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '636117d50ee863dfc31dae518d5e0ead2a48ad86' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/login/request_password.tpl',
      1 => 1517567620,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:messages.tpl' => 1,
  ),
),false)) {
function content_5a743e85476b32_58383270 (Smarty_Internal_Template $_smarty_tpl) {
?>
<form action="" method="post">
    <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="request_password">
<div class="login-form">
    <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <h2>Passwort anfordern</h2>
    <div class="form-group">
      <input type="email" class="form-control login-field" value="" placeholder="Enter your Mail" id="loginFormLoginEmail" name="loginFormLoginEmail" required>
      <label class="login-field-icon fui-mail" for="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
"></label>
    </div>

    <input type="submit" name="request" class="btn btn-primary btn-block" value="Password anfordern">

    <a href="/pages/index.php" class="btn btn-warning btn-block mt-5" role="button">Zurück</a>
</div>
</form>
<?php }
}
