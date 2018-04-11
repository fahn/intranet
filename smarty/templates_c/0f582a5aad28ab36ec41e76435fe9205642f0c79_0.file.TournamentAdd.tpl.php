<?php
/* Smarty version 3.1.31, created on 2018-02-12 21:45:54
  from "/var/www/bc-comet_de/intern/smarty/templates/tournament/TournamentAdd.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a81fd028411f0_70206043',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0f582a5aad28ab36ec41e76435fe9205642f0c79' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/tournament/TournamentAdd.tpl',
      1 => 1518468352,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a81fd028411f0_70206043 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h2 class="display-1 mb-5">Turnier <?php if ($_smarty_tpl->tpl_vars['task']->value == "add") {?>hinzufügen<?php } else { ?>editieren<?php }?></h2>

<?php if ($_smarty_tpl->tpl_vars['task']->value == "edit" && $_smarty_tpl->tpl_vars['vars']->value['visible'] == 0) {?>
  <div class="alert alert-danger">
  <strong>Achtung</strong> Das Turnier wurde gelöscht
</div>
<?php }?>

<form action="" method="post">
  <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="<?php echo $_smarty_tpl->tpl_vars['hidden']->value;?>
">
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentName">Name des Turniers:</label>
        <input class="form-control"  type="text" id="tournamentName" name="tournamentName" placeholder="B-/C-Rangliste" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['name'];?>
" required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentPlace">Ort:</label>
        <input class="form-control"  type="text" id="tournamentPlace" name="tournamentPlace" placeholder="Braunschweig" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['place'];?>
"required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <label for="tournamentStartdate">Start-Datum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentStartdate" name="tournamentStartdate" placeholder="dd.mm.YYYY" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['vars']->value['startdate'],"d.m.Y");?>
" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <label for="tournamentEnddate">Enddatum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentEnddate" name="tournamentEnddate" placeholder="dd.mm.YYYY" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['vars']->value['enddate'],"d.m.Y");?>
" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <label for="tournamentDeadline">Meldeschluss:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentDeadline" name="tournamentDeadline" placeholder="dd.mm.YYYY" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['vars']->value['deadline'],"d.m.Y");?>
" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentLink">Link zur Ausschreibung:</label>
        <input class="form-control"  type="text" id="tournamentLink" name="tournamentLink" placeholder="http://" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['link'];?>
">
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentClassification">Altersklasse:</label>
        <select multiple="multiple" class="form-control js-example-basic-single" name="tournamentClassification[]" id="tournamentClassification" autocomplete="off">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['classificationArr']->value, 'classification');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['classification']->value) {
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['classification']->value;?>
" <?php if (isset($_smarty_tpl->tpl_vars['vars']->value['classification']) && in_array($_smarty_tpl->tpl_vars['classification']->value,$_smarty_tpl->tpl_vars['vars']->value['classification'])) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['classification']->value;?>
</option>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        </select>
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label for="tournamentAdditionalClassification">Weitere:</label>
        <input class="form-control"  type="text" id="tournamentAdditionalClassification" name="tournamentAdditionalClassification" placeholder="A,B,C" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['additionalClassification'];?>
">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="tournamentDiscipline">Diziplinen:</label>
        <select multiple="multiple" class="form-control js-example-basic-single" name="tournamentDiscipline[]" id="tournamentDiscipline" autocomplete="off">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['disciplineArr']->value, 'discipline');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['discipline']->value) {
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['discipline']->value;?>
" <?php if (isset($_smarty_tpl->tpl_vars['vars']->value['discipline']) && in_array($_smarty_tpl->tpl_vars['discipline']->value,$_smarty_tpl->tpl_vars['vars']->value['discipline'])) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['discipline']->value;?>
</option>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        </select>
      </div>
    </div>
  </div>

  <h3 class="mt-5">Benachrichtigung</h3>
  <div class="row">
    <div class="col-md-6">
      <label for="tournamentReporterId">Reporter:</label>
      <select class="form-control js-example-basic-single" name="tournamentReporterId" autocomplete="off">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['players']->value, 'player');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['player']->value) {
?>
            <option value="<?php echo $_smarty_tpl->tpl_vars['player']->value['userId'];?>
" <?php if ($_smarty_tpl->tpl_vars['vars']->value['reporterId'] == $_smarty_tpl->tpl_vars['player']->value['userId']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['player']->value['fullName'];?>
</option>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

      </select>
    </div>

    <div class="col-md-6">
      <label for="tournamentTournamentType">Type des Turniers:</label>
      <select class="form-control js-example-basic-single" name="tournamentTournamentType" autocomplete="off">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tournamentType']->value, 'type');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['type']->value) {
?>
            <option value="<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
"  <?php if ($_smarty_tpl->tpl_vars['vars']->value['tournamentType'] == $_smarty_tpl->tpl_vars['type']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['type']->value;?>
</option>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

      </select>
    </div>
  </div>

  <h3 class="mt-5">Beschreibung</h3>
  <div class="row">
    <div class="col-md-12">
      <textarea id="summernote" name="tournamentDescription"><?php echo $_smarty_tpl->tpl_vars['vars']->value['description'];?>
</textarea>
    </div>
  </div>


  <?php if ($_smarty_tpl->tpl_vars['task']->value == "edit") {?>
    <div class="row mt-5 mb-5">
      <div class="col-md-12">
        <div class="alert alert-warning">
          <strong>Warnung!</strong> Hiermit löscht du dieses Turnier
        </div>
        <input type="checkbox" name="tournamentDelete" id="tournamentDelete" value="1"> <label for="tournamentDelete">Turnier löschen</label>
      </div>
    </div>
    <p></p>
  <?php }?>






  <div class="row mt-5">
    <div class="col-md-6">
      <input type="submit" name="submit" class="btn btn-success btn-wide" value="Turnier <?php if ($_smarty_tpl->tpl_vars['task']->value == "add") {?>hinzufügen<?php } else { ?>editieren<?php }?>">
    </div>
    <div class="col-md-6 text-right">
      <a class="btn btn-danger" href="https://int.bc-comet.de/pages/rankingTournament.php">Zurück</a>
    </div>
  </div>
</form>
<?php }
}
