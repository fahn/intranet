<?php
/* Smarty version 3.1.31, created on 2017-10-24 11:35:57
  from "/var/www/weinekind_de/rl/smarty/templates/stats.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59ef097d893d24_70170982',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '57a09d8ac172328f22a65a2f201fca9364b8d69c' => 
    array (
      0 => '/var/www/weinekind_de/rl/smarty/templates/stats.tpl',
      1 => 1508837756,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59ef097d893d24_70170982 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h2><?php echo $_smarty_tpl->tpl_vars['tableTitle']->value;?>
</h2>
<hr/>
<table class="table table-sm table-striped" data-toggle="table"
       data-url="/gh/get/response.json/wenzhixin/bootstrap-table/tree/master/docs/data/data1/"
       data-search="true"
       data-show-refresh="true"
       data-show-toggle="true"
       data-show-columns="true">
    <thead>
        <tr class="thead-inverse">
            <th class="text-center" colspan="2">Rank</th>
            <th class="text-center" colspan="2">Player</th>
            <th class="text-center" colspan="4">Games</th>
            <th class="text-center" colspan="4">Sets</th>
            <th class="text-center" colspan="4">Points</th>
        </tr>
        <tr>
            <th data-field="position">Position</th>
            <th>Rank</th>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Games</th>
            <th>Won</th>
            <th>Lost</th>
            <th>Ratio</th>
            <th>Sets</th>
            <th>Won</th>
            <th>Lost</th>
            <th>Ratio</th>
            <th>Poinst</th>
            <th>Won</th>
            <th>Lost</th>
            <th>Ratio</th>
        </tr>
    </thead>
    <tbody>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['players']->value, 'player', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['player']->value) {
?>
        <tr>
            <tr>
                    <td><?php echo $_smarty_tpl->tpl_vars['key']->value+1;?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['rankPoints'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['lastName'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['firstName'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['games'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['gamesWon'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['gamesLost'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['gamesRatio'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['sets'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['setsWon'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['setsLost'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['setsRatio'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['points'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['pointsWon'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['pointsLost'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['player']->value['pointsRatio'];?>
</td>
                </tr> 
        </tr>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </tbody>
</table><?php }
}
