<?php
/* Smarty version 3.1.31, created on 2018-03-22 13:15:37
  from "/var/www/bc-comet_de/intern/smarty/templates/tournament/TournamentDetails.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ab39e6957d5e4_12214489',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a60c0702922012690a781bf60c5ff75b33a4090f' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/tournament/TournamentDetails.tpl',
      1 => 1521720916,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab39e6957d5e4_12214489 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h2 class="display-3 mb-5"><?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
</h2>

<div class="row equal">
  <div class="col-md-6 align-items-stretch">
    <div class="card">
        <h5 class="card-header">Informationen</h5>
        <div class="card-body">
        <p><strong>Ort:</strong> <?php echo $_smarty_tpl->tpl_vars['tournament']->value['place'];?>
</p>
        <p><strong>Zeitraum:</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['startdate'],"%d.%m.%Y");?>
 - <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['enddate'],"%d.%m.%Y");?>
</p>
        <p><strong>Meldeschluss:</strong> <span class="text-<?php if (strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline']) < time()) {?>danger<?php } else { ?>success<?php }?>"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['deadline'],"%d.%m.%Y");?>
</span></p>
        <p><strong>Ausschreibung:</strong> <?php if ($_smarty_tpl->tpl_vars['tournament']->value['link']) {?><a href="<?php echo $_smarty_tpl->tpl_vars['tournament']->value['link'];?>
" target="_blank">Link zur Ausschreibung</a><?php } else { ?>-<?php }?></p>
        <p><strong>Melder:</strong> <a href="https://int.bc-comet.de/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['reporterId'];?>
"><?php echo $_smarty_tpl->tpl_vars['tournament']->value['reporterName'];?>
</a><br>
        <?php if ($_smarty_tpl->tpl_vars['tournament']->value['classification']) {?>
          <p><strong>Altersklassen:</strong> <?php echo $_smarty_tpl->tpl_vars['tournament']->value['classification'];?>

          <?php if ($_smarty_tpl->tpl_vars['tournament']->value['additionalClassification']) {
echo implode($_smarty_tpl->tpl_vars['tournament']->value['additionalClassification'],",");
}?></p>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['tournament']->value['discipline']) {?>
        <p><strong>Disziplinen:</strong> <?php echo $_smarty_tpl->tpl_vars['tournament']->value['discipline'];?>
</p>
        <?php }?>
      </div>
    </div>
  </div>

  <div class="col-md-6 mb-3  align-items-stretch">
    <div class="card">
        <h5 class="card-header">Anfahrt</h5>
      <div class="card-body">
        <iframe
        style="width: 100%"
        height="300"
        frameborder="0" style="border:0"
        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCDYXGM6sJVeOvkbn6I2uvihQfs4BVQy0k
          &q=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['place'];?>
&zoom=9 " allowfullscreen>
        </iframe>
      </div>
    </div>
  </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['tournament']->value['description']) {?>
  <div class="row">
    <div class="col-md-12 align-items-stretch">
      <div class="card">
          <h5 class="card-header">Beschreibung</h5>
          <div class="card-body"><?php echo htmlspecialchars_decode($_smarty_tpl->tpl_vars['tournament']->value['description'], ENT_QUOTES);?>
</div>
      </div>
    </div>
  </div>
<?php }?>

<div class="d-flex flex-row-reverse">
    <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value) {?>
        <div class="p-2">
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Optionen</button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="?action=export&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
"><i class="fas fa-bullhorn"></i> Meldung</a>
                <a class="dropdown-item" href="?action=backup&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
"><i class="fas fa-cloud"></i> Sicherungen</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="?action=edit_tournament&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
"><i class="fas fa-edit"></i> Turnier bearbeiten</a>
            </div>
        </div>
        </div>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['tournament']->value['openSubscription'] == 1 && time() < strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline'])) {?>
        <div class="p-2">
            <a class="btn btn-success" href="?action=add_player&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
">Spieler melden</a>
        </div>
    <?php }?>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover">
    <tr>
      <th>Spieler</th>
      <th>Disziplin</th>
      <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value) {?>
      <th>Melder</th>
      <?php }?>
        <th class="text-center">Option</th>
    </tr>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['players']->value, 'player');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['player']->value) {
?>
      <?php if ($_smarty_tpl->tpl_vars['player']->value['visible'] == 1) {?>
        <tr>
          <td><a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['player']->value['playerID'];?>
" title="Profil von <?php echo $_smarty_tpl->tpl_vars['player']->value['playerName'];?>
"><?php echo $_smarty_tpl->tpl_vars['player']->value['playerName'];?>
</a> <?php if ($_smarty_tpl->tpl_vars['player']->value['partnerName']) {?>// <?php if ($_smarty_tpl->tpl_vars['player']->value['partnerName'] == 'FREI') {?><span class="text-danger font-weight-bold"><?php echo $_smarty_tpl->tpl_vars['player']->value['partnerName'];?>
</span> <?php } else { ?> <a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['player']->value['partnerID'];?>
" title="Profil von <?php echo $_smarty_tpl->tpl_vars['player']->value['partnerName'];?>
"><?php echo $_smarty_tpl->tpl_vars['player']->value['partnerName'];?>
</a><?php }
}?></td>
          <td><?php echo $_smarty_tpl->tpl_vars['player']->value['classification'];?>
</td>
          <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value) {?>
            <td><a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['player']->value['reporterID'];?>
" title="gemeldet von <?php echo $_smarty_tpl->tpl_vars['player']->value['reporterName'];?>
 am <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['player']->value['fillingDate'],"d.m.Y H:i");?>
"><?php echo $_smarty_tpl->tpl_vars['player']->value['reporterName'];?>
</a> (<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['player']->value['fillingDate'],"d.m.Y");?>
)</td>
          <?php }?>
          <td class="text-center">
            <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value || $_smarty_tpl->tpl_vars['player']->value['playerID'] == $_smarty_tpl->tpl_vars['userId']->value || $_smarty_tpl->tpl_vars['player']->value['partnerId'] == $_smarty_tpl->tpl_vars['userId']->value) {?>
              <a class="btn btn-danger" href="?action=deletePlayer&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
&tournamentPlayerId=<?php echo $_smarty_tpl->tpl_vars['player']->value['tournamentPlayerId'];?>
" onclick="return confirm('Möchtest du wirklich den Spieler abmelden ?');">Abmelden</a>
            <?php }?>
          </td>
        </tr>
      <?php }?>
    <?php
}
} else {
?>

      <tr>
        <td colspan="4" class="text-center font-weight-bold">Keine Einträge</td>
      </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

  </table>
</div>
<?php }
}
