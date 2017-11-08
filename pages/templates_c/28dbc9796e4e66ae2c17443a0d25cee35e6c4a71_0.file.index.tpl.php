<?php
/* Smarty version 3.1.31, created on 2017-10-24 11:35:06
  from "/var/www/weinekind_de/rl/smarty/templates/index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59ef094a04afc8_26610707',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '28dbc9796e4e66ae2c17443a0d25cee35e6c4a71' => 
    array (
      0 => '/var/www/weinekind_de/rl/smarty/templates/index.tpl',
      1 => 1508837697,
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
function content_59ef094a04afc8_26610707 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<div class="container">

    <?php $_smarty_tpl->_subTemplateRender("file:navi.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>




    <div id="row">
        <?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
            <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

        <?php }?>
    </div>


        
<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
