<?php
/* Smarty version 3.1.31, created on 2017-11-28 16:17:53
  from "/var/www/bc-comet_de/intern/smarty/templates/admin/ClubList.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a1d7e216ad969_81817705',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3f140c524b9988c7e69530ffd4afdcf9d861729b' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/admin/ClubList.tpl',
      1 => 1511771596,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_pagination.tpl' => 2,
  ),
),false)) {
function content_5a1d7e216ad969_81817705 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h3>Vereine</h3>
<p class="text-right">
	<a class="btn btn-success" href="?action=add_club">Club hinzufügen</a>
</p>

<?php if ($_smarty_tpl->tpl_vars['pagination']->value) {?>
	<?php $_smarty_tpl->_subTemplateRender("file:_pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php }?>

<div class="table-responsive">
	<table class="table table-sm table-striped table-hover">
		<thead>
			<tr>
				<th>Name</th>
				<th>Vereinsnummer</th>
				<th>Verband</th>
				<th class="text-center">Option</th>
			</tr>
		</thead>
		<tbody>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['clubs']->value, 'club');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['club']->value) {
?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['club']->value['name'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['club']->value['clubNumber'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['club']->value['association'];?>
</td>
					<td class="text-center"><a class="btn btn-info" href="?action=edit&id=<?php echo $_smarty_tpl->tpl_vars['club']->value['clubId'];?>
">Editieren</a> <a class="btn btn-danger" href="?action=delete&id=<?php echo $_smarty_tpl->tpl_vars['club']->value['clubId'];?>
">Löschen</a></td>
				</tr>
			<?php
}
} else {
?>

				<tr>
			    <td colspan="4" class="text-center">Failed to get all clubs.</td>
			  </tr>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

		</tbody>
	</table>
</div>

<?php if ($_smarty_tpl->tpl_vars['pagination']->value) {?>
	<?php $_smarty_tpl->_subTemplateRender("file:_pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

<?php }
}
}
