<?php
/* Smarty version 3.1.31, created on 2018-02-20 11:44:23
  from "/var/www/bc-comet_de/intern/smarty/templates/ranking/mygames.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a8bfc0707f1e0_32898245',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '85d230fe6bbe584d6aa9745648c029af9c76763a' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/ranking/mygames.tpl',
      1 => 1519123460,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a8bfc0707f1e0_32898245 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h3>Overview of All Played Games</h3>

<p class="text-right">
	<a class="btn btn-success" href="?action=add">Spiel eintragen</a>
</p>

<div class="table-responsive">
	<table class="table table-sm table-striped table-hover">
		<thead>
			<tr>
				<th>Date</th>
				<th>Time</th>
				<th>Team A</th>
				<th>Team B</th>
				<th>Points</th>
				<th class="text-center">Optionen</th>
			</tr>
		</thead>
		<tbody>
	    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['games']->value, 'game');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['game']->value) {
?>
			<tr>
				<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['game']->value['datetime'],"d.m.Y");?>
</td>
				<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['game']->value['datetime'],"H:i");?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['game']->value['playerA1'];?>
 <?php if ($_smarty_tpl->tpl_vars['game']->value['playerA2']) {?>// <?php echo $_smarty_tpl->tpl_vars['game']->value['playerA2'];
}?></td>
				<td><?php echo $_smarty_tpl->tpl_vars['game']->value['playerB1'];?>
 <?php if ($_smarty_tpl->tpl_vars['game']->value['playerB2']) {?>// <?php echo $_smarty_tpl->tpl_vars['game']->value['playerB2'];
}?></td>
				<td><?php echo $_smarty_tpl->tpl_vars['game']->value['setA1'];?>
:<?php echo $_smarty_tpl->tpl_vars['game']->value['setB1'];?>
 <?php echo $_smarty_tpl->tpl_vars['game']->value['setA2'];?>
:<?php echo $_smarty_tpl->tpl_vars['game']->value['setB2'];?>
 <?php if ($_smarty_tpl->tpl_vars['game']->value['setA3']) {
echo $_smarty_tpl->tpl_vars['game']->value['setA3'];?>
:<?php echo $_smarty_tpl->tpl_vars['game']->value['setB3'];
}?></td>
				<td class="text-center"><a class="btn btn-info" href="?action=edit&id=<?php echo $_smarty_tpl->tpl_vars['game']->value['matchId'];?>
">Editieren</a> <a class="btn btn-danger" href="?action=delete&id=<?php echo $_smarty_tpl->tpl_vars['game']->value['matchId'];?>
">Löschen</a></td>
			</tr>
	    <?php
}
} else {
?>

	      <tr>
	        <td colspan="9">Failed to get all Games from data base. Reason: <?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 </td>
	      </tr>
	    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

		</tbody>
	</table>
</div>
<?php }
}
