<?php
/* Smarty version 3.1.31, created on 2017-11-27 13:24:27
  from "/var/www/bc-comet_de/intern/smarty/templates/messages.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a1c03fbc7bb84_11687880',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aac658cf4bf55b6a763a1b77ebc198b0fc9964f9' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/messages.tpl',
      1 => 1511771596,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1c03fbc7bb84_11687880 (Smarty_Internal_Template $_smarty_tpl) {
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
