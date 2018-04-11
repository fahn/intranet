<?php
/* Smarty version 3.1.31, created on 2018-03-15 22:50:58
  from "/var/www/bc-comet_de/intern/smarty/templates/login/change_password.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5aaaeac2eb70b7_10280543',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f878579255f9182a8771699de161b43f87fa7bdb' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/login/change_password.tpl',
      1 => 1521150658,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:messages.tpl' => 1,
  ),
),false)) {
function content_5aaaeac2eb70b7_10280543 (Smarty_Internal_Template $_smarty_tpl) {
?>
<form action="" method="post">
    <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="change_password">
    <input type="hidden" name="loginFormLoginToken"  id="loginFormLoginToken"  value="<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
    <input type="hidden" name="loginFormLoginEmail"  id="loginFormLoginEmail"  value="<?php echo $_smarty_tpl->tpl_vars['mail']->value;?>
">
    <div class="login-form">
        <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


        <h2>Passwort ändern</h2>

        <div class="form-group">
          <input type="password" class="form-control login-field" value="" placeholder="Enter password" id="loginFormLoginPass" name="loginFormLoginPass" required>
          <label class="login-field-icon fui-lock" for="loginFormLoginPass"></label>
        </div>

        <div class="form-group">
          <input type="password" class="form-control login-field" value="" placeholder="repeat password" id="loginFormLoginPass2" name="loginFormLoginPass2" required>
          <label class="login-field-icon fui-lock" for="loginFormLoginPass2"></label>
        </div>

        <div class="row">
          <div class="col-md-6">
            <input type="submit" name="request" class="btn btn-primary" value="Password ändern">
          </div>

          <div class="col-md-6 text-right">
            <a href="https://int.bc-comet.de/pages/" class="btn btn-warning" role="button">Abbruch</a>
          </div>
        </div>
    </div>
</form>
<?php }
}
