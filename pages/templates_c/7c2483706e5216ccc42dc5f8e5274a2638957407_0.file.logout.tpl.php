<?php
/* Smarty version 3.1.31, created on 2017-10-16 19:39:56
  from "/var/www/weinekind_de/rl/smarty/templates/logout.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59e4eeec5f2671_58350230',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7c2483706e5216ccc42dc5f8e5274a2638957407' => 
    array (
      0 => '/var/www/weinekind_de/rl/smarty/templates/logout.tpl',
      1 => 1508162249,
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
function content_59e4eeec5f2671_58350230 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "test.conf", "setup", 0);
?>

<?php $_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>'foo'), 0, false);
?>


<div class="container">

    <?php $_smarty_tpl->_subTemplateRender("file:navi.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


    <div id = "formUserLogout" class = "small">
        <form>
            <p>Hello <?php echo $_smarty_tpl->tpl_vars['currentUserName']->value;?>
! You are logged in!</p>
            <input
                type		= "submit"
                name		= "<?php echo $_smarty_tpl->tpl_vars['variableNameAction']->value;?>
"
                value		= "<?php echo $_smarty_tpl->tpl_vars['variableNameActionLogout']->value;?>
"
                formaction	= "<?php echo $_smarty_tpl->tpl_vars['formAction']->value;?>
"
                formmethod	= "post"
            />
        </form>
    </div>

        
<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
