<?php
/* Smarty version 3.1.31, created on 2018-01-31 15:11:59
  from "/var/www/bc-comet_de/intern/smarty/templates/index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a71ceaf028b22_75354538',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b41ab2deb2166a4e3ad717040b86baac3dca96d5' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/index.tpl',
      1 => 1517407900,
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
function content_5a71ceaf028b22_75354538 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<body id="page">

  <div class="container">
    <?php $_smarty_tpl->_subTemplateRender("file:navi.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <?php $_smarty_tpl->_subTemplateRender("file:messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <div class="row">
      <div class="col-md-12">
        <?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
          <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

        <?php }?>
      </div>
    </div>
<!-- closing  </div> in footer -->

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php }
}
