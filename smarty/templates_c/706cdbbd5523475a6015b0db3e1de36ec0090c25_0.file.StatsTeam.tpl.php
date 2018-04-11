<?php
/* Smarty version 3.1.31, created on 2018-01-02 18:26:19
  from "/var/www/bc-comet_de/intern/smarty/templates/ranking/StatsTeam.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a4bc0bb8d44b6_03659530',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '706cdbbd5523475a6015b0db3e1de36ec0090c25' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/ranking/StatsTeam.tpl',
      1 => 1511785078,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a4bc0bb8d44b6_03659530 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h2><?php echo $_smarty_tpl->tpl_vars['tableTitle']->value;?>
</h2>


<?php if (isset($_smarty_tpl->tpl_vars['explain']->value)) {?>
  <div class="alert alert-info">
      <?php echo $_smarty_tpl->tpl_vars['explain']->value;?>

  </div>
<?php }
if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value) {?>
  <p class="text-right">
    <a class="btn btn-success" href="/pages/reportInsertGame.php?formAction=NewGame">Spiel eintragen</a>
  </p>
<?php }?>

<div class="table-responsive">
  <table class="table table-sm table-striped" data-toggle="table"
    data-url="/gh/get/response.json/wenzhixin/bootstrap-table/tree/master/docs/data/data1/"
    data-search="true"
    data-show-refresh="true"
    data-show-toggle="true"
    data-show-columns="true">
    <thead>
      <tr class="thead-inverse">
        <th colspan = "2">Rank</th>
        <th colspan = "1">Team</th>
        <th colspan = "4">Games</th>
        <th colspan = "4">Sets</th>
        <th colspan = "4">Points</th>
      </tr>
      <tr>
        <th data-field="position">Position</th>
        <th>Rank</th>
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
    </thead>
    <tbody>
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['players']->value, 'player', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['player']->value) {
?>
      <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['player']->value['position'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['player']->value['rankPoints'];?>
</td>
        <td><a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['player']->value['player1Id'];?>
"><?php echo $_smarty_tpl->tpl_vars['player']->value['player1FirstName'];?>
 <?php echo $_smarty_tpl->tpl_vars['player']->value['player1LastName'];?>
</a> // <a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['player']->value['player2Id'];?>
"><?php echo $_smarty_tpl->tpl_vars['player']->value['player2FirstName'];?>
 <?php echo $_smarty_tpl->tpl_vars['player']->value['player2LastName'];?>
</a></td>
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
      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </tbody>
  </table>
</div>
<?php }
}
