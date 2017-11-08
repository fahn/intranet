<?php
/* Smarty version 3.1.31, created on 2017-10-17 14:28:44
  from "/var/www/weinekind_de/rl/smarty/templates/messages.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59e5f77c166b83_11360718',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '96491301e7ce24a1d792c3708bdc65cf64722241' => 
    array (
      0 => '/var/www/weinekind_de/rl/smarty/templates/messages.tpl',
      1 => 1508243254,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59e5f77c166b83_11360718 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['messages']->value)) {?>

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'message');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
?>
        <div class="alert alert-<?php echo $_smarty_tpl->tpl_vars['message']->value['type'];?>
">
            <?php echo $_smarty_tpl->tpl_vars['message']->value['message'];?>

        </div>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


<?php }
}
}
