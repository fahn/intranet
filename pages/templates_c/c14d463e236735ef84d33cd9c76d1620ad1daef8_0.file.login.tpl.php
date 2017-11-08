<?php
/* Smarty version 3.1.31, created on 2017-10-16 19:27:09
  from "/var/www/weinekind_de/rl/smarty/templates/login.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59e4ebed2de554_17950728',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c14d463e236735ef84d33cd9c76d1620ad1daef8' => 
    array (
      0 => '/var/www/weinekind_de/rl/smarty/templates/login.tpl',
      1 => 1508160927,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:navi.tpl' => 1,
    'file:messages.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_59e4ebed2de554_17950728 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<?php $_smarty_tpl->_subTemplateRender("file:navi.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>





<div id="formUserLogin" class="login-screen">
    <div class="login-icon">
        <img src="../design/img/badminton.png" alt="Welcome to Mail App">
        <h4>BC Comet <small>Rangliste</small></h4>
    </div>
    <form>
        <div class="login-form">
            <div class="form-group">
                <input type="text" class="form-control login-field" value="" placeholder="Enter your Mail" id="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
">
                <label class="login-field-icon fui-user" for="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
"></label>
            </div>

            <div class="form-group">
                <input type="password" class="form-control login-field" value="" placeholder="Password" id="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
">
                <label class="login-field-icon fui-lock" for="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
"></label>
            </div>
        
            <input type="submit" name="<?php echo $_smarty_tpl->tpl_vars['variableNameAction']->value;?>
" class="btn btn-primary btn-lg btn-block" value="<?php echo $_smarty_tpl->tpl_vars['variableNameActionLogin']->value;?>
" formaction="<?php echo $_smarty_tpl->tpl_vars['formTO']->value;?>
" formmethod="post">
            <a class="login-link" href="#">Lost your password?</a>
        </div>
    </form> 
</div>


        
<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
