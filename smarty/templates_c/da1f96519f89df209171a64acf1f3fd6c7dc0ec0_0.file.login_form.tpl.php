<?php
/* Smarty version 3.1.31, created on 2018-01-23 15:27:20
  from "/var/www/bc-comet_de/intern/smarty/templates/login_form.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a674648499296_12760880',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'da1f96519f89df209171a64acf1f3fd6c7dc0ec0' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/login_form.tpl',
      1 => 1516717638,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:messages.tpl' => 1,
  ),
),false)) {
function content_5a674648499296_12760880 (Smarty_Internal_Template $_smarty_tpl) {
?>
<form action="<?php echo $_smarty_tpl->tpl_vars['formTO']->value;?>
" method="post">
        <div class="login-form">
          <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

          <div class="alert alert-warning text-center">
	  	<p><i class="glyphicon glyphicon-star-empty"></i> <a href="https://docs.google.com/spreadsheets/d/1bFkHSGeUWtDzy8Xn_VF_dgMGNV8FXzysprjcB1Rs9N0/edit?usp=sharing">Link Meldesystem</a> <i class="glyphicon glyphicon-star-empty"></i></p>
	  </div>
          <div class="alert alert-info">
	   <p>Diese Webseite wird bald das alte Meldesystem ablösen. Da wir uns noch in der Beta-Phase befinden, haben nur wenige Leute Zugriff darauf!</p>
           <p>Wenn du uns bei dem neuem System helfen willst, dann melde dich bei Stefan Metzner beim Training.</p>
          </div>
          <div class="form-group">
              <input type="email" class="form-control login-field" value="" placeholder="Enter your Mail" id="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
" required>
              <label class="login-field-icon fui-user" for="<?php echo $_smarty_tpl->tpl_vars['variableNameEmail']->value;?>
"></label>
          </div>

          <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Password" id="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
" required>
              <label class="login-field-icon fui-lock" for="<?php echo $_smarty_tpl->tpl_vars['variableNamePassw']->value;?>
"></label>
          </div>
          
          <div class="row">
            <div class="col-md-4">
              <input type="submit" name="<?php echo $_smarty_tpl->tpl_vars['variableNameAction']->value;?>
" class="btn btn-primary btn-lg btn-block" value="<?php echo $_smarty_tpl->tpl_vars['variableNameActionLogin']->value;?>
">
            </div>
            
            <div class="col-md-4">
             &nbsp;
            </div>
            
            <div class="col-md-4 text-right">
              <a href="?action=request_password" class="btn btn-warning" role="button">Passwort vergessen ?</a>
            </div>
          </div>
        </div>
    </form><?php }
}
