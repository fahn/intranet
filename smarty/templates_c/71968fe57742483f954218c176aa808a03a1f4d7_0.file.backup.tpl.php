<?php
/* Smarty version 3.1.31, created on 2018-03-22 17:45:12
  from "/var/www/bc-comet_de/intern/smarty/templates/tournament/backup.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ab3dd98de8491_17229309',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '71968fe57742483f954218c176aa808a03a1f4d7' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/tournament/backup.tpl',
      1 => 1521737088,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab3dd98de8491_17229309 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h1 class="display-1">Sicherung</h1>
<div class="alert alert-danger">
    Arbeite noch daran.
</div>
<?php if ($_smarty_tpl->tpl_vars['diff']->value) {?>
  <h2> Vergleich</h2>
  <pre>
  <?php echo print_r($_smarty_tpl->tpl_vars['diff']->value);?>

  <hr>
  <?php echo print_r($_smarty_tpl->tpl_vars['diffResult']->value);?>

<?php }?>

<p class="text-right">
  <a class="btn btn-danger" href="?action=create_backup&id=<?php echo $_GET['id'];?>
">Erstellen</a>
</p>


<div class="table-responsive">
  <table id="myTable" class="table table-striped table-hover">
    <tr>
      <th colspan="3">Datum 12</th>
    </tr>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['backup']->value, 'line');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['line']->value) {
?>
      <tr>
        <td>Sicherung vom <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['line']->value['date'],"d.m.Y H:i");?>
 </td>
        <?php if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_line']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_line']->value['first'] : null)) {?>
          <td><a href="?action=backup&id=36&detail=<?php echo $_smarty_tpl->tpl_vars['line']->value['backupId'];?>
">Vergleichen</a></td>
        <?php } else { ?>
          <td></td>
        <?php }?>
      </tr>
    <?php
}
} else {
?>

      <tr>
        <td colspan="3" class="text-center">Keine Einträge vorhanden</td>
      </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

  </table>
</div>
<?php }
}
