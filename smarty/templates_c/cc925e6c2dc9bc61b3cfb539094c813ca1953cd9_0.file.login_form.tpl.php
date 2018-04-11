<?php
/* Smarty version 3.1.31, created on 2018-02-20 15:16:13
  from "/var/www/bc-comet_de/intern/smarty/templates/login/login_form.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a8c2dad2aa228_24025517',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cc925e6c2dc9bc61b3cfb539094c813ca1953cd9' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/login/login_form.tpl',
      1 => 1519136172,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:messages.tpl' => 1,
  ),
),false)) {
function content_5a8c2dad2aa228_24025517 (Smarty_Internal_Template $_smarty_tpl) {
?>
<form action="<?php echo $_smarty_tpl->tpl_vars['formTO']->value;?>
" method="post">
  <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="Log In">
  <div class="login-form">
    <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


    <div class="alert alert-info">
     <p class="text-center"> <strong>~~ BC Comet Intranet ~~</strong> </p>
       <p> Möchtest du Zugang zu unserem neuen System haben, dann schicke eine E-Mail an: <a href="mailto:stefan@weinekind.de?subject=Zugang Intern BC Comet&body=Hallo Stefan,%0D%0A%0D%0Aich hätte gerne Zugang zum BC Comet Intranet:%0D%0A%0D%0AMein Vorname:%0D%0AMein Nachname:%0D%0A">Stefan Metzner</a>.<br> Weitere Informationen folgen dann via E-Mail.</p>
    </div>
    <div class="form-group">
        <input type="email" class="form-control login-field" value="" placeholder="E-Mail-Adresse" id="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
" required>
    </div>

    <div class="form-group">
        <input type="password" class="form-control login-field" value="" placeholder="Password" id="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
" required>
    </div>

    <input type="submit" name="<?php echo $_smarty_tpl->tpl_vars['variableNameAction']->value;?>
" class="btn btn-success btn-block " value="<?php echo $_smarty_tpl->tpl_vars['variableNameActionLogin']->value;?>
">

    <a href="?action=request_password" class="btn btn-warning btn-block mt-5" role="button">Passwort vergessen ?</a>
    <hr>
    <p class="text-center">
      <a href="http://www.bc-comet.de/impressum/">Impressum</a> // <a href="http://www.bc-comet.de/datenschutz/">Datenschutz</a>
    </p>

    </div>
  </div>
</form>
<?php }
}
