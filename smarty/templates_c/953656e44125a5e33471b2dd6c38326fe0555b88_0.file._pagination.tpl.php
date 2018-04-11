<?php
/* Smarty version 3.1.31, created on 2018-02-24 10:00:35
  from "/var/www/bc-comet_de/intern/smarty/templates/_pagination.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a9129b3e1f9a1_18368185',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '953656e44125a5e33471b2dd6c38326fe0555b88' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/_pagination.tpl',
      1 => 1519462795,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a9129b3e1f9a1_18368185 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['pagination']->value) {?>
  <nav aria-label="Page navigation example" class="mt-1 mb-1">
    <ul class="pagination justify-content-center">
      <?php if ($_GET['page'] > 1) {?>
      <li class="page-item">
            <a class="page-link" href="?page=<?php echo $_GET['page']-1;?>
" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <?php }?>
      <!-- Make dropdown appear above pagination -->
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pagination']->value, 'pn');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['pn']->value) {
?>
          <?php $_smarty_tpl->_assignInScope('max', $_smarty_tpl->tpl_vars['pn']->value['id']);
?>
            <li class="page-item <?php echo $_smarty_tpl->tpl_vars['pn']->value['status'];?>
"><a class="page-link" href="?page=<?php echo $_smarty_tpl->tpl_vars['pn']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['pn']->value['id'];?>
</a></li>
      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


      <?php if (!isset($_GET['page'])) {?>
        <?php $_smarty_tpl->_assignInScope('min', "2");
?>
      <?php } else { ?>
        <?php $_smarty_tpl->_assignInScope('min', $_GET['page']);
?>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['max']->value > 1 && $_smarty_tpl->tpl_vars['min']->value < $_smarty_tpl->tpl_vars['max']->value) {?>
        <li class="page-item">
            <a class="page-link" href="?page=<?php echo $_GET['page']+1;?>
" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
        <?php }?>
    </ul>
  </nav>
<?php }
}
}
