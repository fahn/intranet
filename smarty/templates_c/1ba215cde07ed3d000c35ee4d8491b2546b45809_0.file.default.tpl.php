<?php
/* Smarty version 3.1.31, created on 2018-03-17 10:36:08
  from "/var/www/bc-comet_de/intern/smarty/templates/default.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5aace1882fb454_85008422',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1ba215cde07ed3d000c35ee4d8491b2546b45809' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/default.tpl',
      1 => 1521278544,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5aace1882fb454_85008422 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<div class="card mt-5 mb-5">
  <h5 class="card-header">Willkommen <?php echo $_smarty_tpl->tpl_vars['currentUserName']->value;?>
</h5>
  <div class="card-body">
    <p class="card-text">in unserem Intranet. Hier kannst du deine interne Rangliste pflegen und dich für kommende Turniere anmelden.</p>
  </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <h5 class="card-header">Deine letzten 5 Spiele</h5>
            <div class="card-body">
                <p class="card-text">
                    <table class="table table-striped table-hover">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['games']->value, 'game');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['game']->value) {
?>
                        <tr>
                          <td> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['game']->value['datetime'],"d.m.Y");?>
</td>
                          <td><?php echo $_smarty_tpl->tpl_vars['game']->value['opponent'];?>
</td>
                          <td><?php echo $_smarty_tpl->tpl_vars['game']->value['chicken'];?>
</td>
                        </tr>
                    <?php
}
} else {
?>

                      Du hast noch keine Spiele gemacht.
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                    </table>
                    <hr>
                    <a href="/pages/statsPlayerAlltime.php" alt="Komplette Rangliste" title="Komplette Rangliste"><i class="fas fa-list-ol"></i> komplette Rangliste</a>
                </p>
            </div>
        </div>


        <div class="card mt-4 mb-4">
            <h5 class="card-header">Das Team</h5>
            <div class="card-body">
                <p class="card-text">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['users']->value, 'user');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
?>
                      <a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['user']->value['userId'];?>
">
                        <?php echo $_smarty_tpl->tpl_vars['user']->value['fullName'];?>

                      </a><br>
                    <?php
}
} else {
?>

                      Fehler. Bitte einen Admin kontaktieren
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                    <hr>
                    Bei diesen Personen kannst du dich jederzeit melden.
                </p>
            </div>
        </div>

        <div class="card mt-4 mb-4">
            <h5 class="card-header">Social Comet</h5>
            <div class="card-body">
                <p class="card-text text-center">
                    <a href="http://bc-comet.de" target="_blank"><i class="fas fa-home fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                    <a href="https://www.facebook.com/BC.Comet/" target="_blank"><i class="fab fa-facebook fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                    <a href="https://www.youtube.com/channel/UCJhuBsKc55YdTNznSORIEQg" target="_blank"><i class="fab fa-youtube fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                </p>
            </div>
        </div>
    </div>

  <div class="col-md-8">
    <div class="card">
        <h5 class="card-header">Kommende 5 Turniere</h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>Altersklasse</th>
                        <th>Name</th>
                        <th>Datum</th>
                        <th>Ort</th>
                    </tr>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tournaments']->value, 'tournament');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tournament']->value) {
?>
                    <tr>
                        <td><?php echo $_smarty_tpl->tpl_vars['tournament']->value['classification'];?>
</td>
                        <td><a <?php if (strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline']) < time()) {?>class="text-danger"<?php } else { ?>class="text-success"<?php }?> href="/pages/rankingTournament.php?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
: vom <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['startdate'],"d.m.y");?>
 - <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['enddate'],'d.m.y');?>
"><?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
</a></td>
                        <td <?php if (strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline']) < time()) {?>class="text-danger"<?php } else { ?> class="text-success"<?php }?>><?php if ($_smarty_tpl->tpl_vars['tournament']->value['startdate'] == $_smarty_tpl->tpl_vars['tournament']->value['enddate']) {
echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['startdate'],"d.m.y");
} else {
echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['startdate'],"d.m.y");?>
 - <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['enddate'],"d.m.y");
}?></td>
                        <td><?php echo $_smarty_tpl->tpl_vars['tournament']->value['place'];?>
</td>
                    </tr>
                    <?php
}
} else {
?>

                    <tr>
                        <td colspan="2">Leider keine Turniere in der kommenden Zeit.</td>
                    </tr>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                </table>
            </div>
            <hr>
            <a href="/pages/rankingTournament.php" title="Alle Turniere"><i class="fas fa-list-ul"></i> alle Turniere</a>
        </div>
    </div>

    <div class="card mt-3 last news">
        <h5 class="card-header">Letzte Neuigkeiten</h5>
        <ul class="list-group list-group-flush active">
            <li class="list-group-item">16.03.2018 // Fix Api error and fix sending mails</li>
            <li class="list-group-item">23.02.2018 // Fix and add icons to Menu</li>
            <li class="list-group-item">20.02.2018 // fixed serveral bugs</li>
            <li class="list-group-item">09.02.2018 // Marker in Turnieransicht verändert</li>
            <li class="list-group-item">08.02.2018 // Optimierungen, Überprüfung, ob Meldung von Spieler bei Turnieren berechtigt sind</li>
            <li class="list-group-item">07.02.2018 // Neue Turniere hinzugefügt & Änderungen am Meldesystem</li>
            <li class="list-group-item">31.01.2018 // Kompletter Austausch des Designs</li>
            <li class="list-group-item">28.01.2018 // Version für alle Mitglieder frei geschalten.</li>
        </ul>

</div>
<?php }
}
