<?php
/* Smarty version 3.1.31, created on 2018-03-26 11:36:58
  from "/var/www/bc-comet_de/intern/smarty/templates/tournament/PlayerAdd.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ab8bf3a343a42_98412246',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '685d6f20e5fe78ade74e97b770d8e2f1436a123d' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/tournament/PlayerAdd.tpl',
      1 => 1522057016,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab8bf3a343a42_98412246 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h2 class="display-4 mb-5">Meldung<br> <?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
</h2>
<?php if ($_smarty_tpl->tpl_vars['tournament']->value['openSubscription'] == 0) {?>
  <div class="alert alert-warning">
    <h3>Information</h3>
    <p>Leider kann man sich noch nicht anmelden</p>
  </div>

<?php } elseif (time() > strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline'])) {?>
  <p>Leider ist der Meldeschluss schon vorbei. Versuche es einfach beim nächsten mal!</p>
  <p class="text-right">
    <a class="btn btn-danger" href="?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
">Zurück</a>
  </p>
<?php } else { ?>
  <form action="" method="post">
    <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="Insert Players">


    <div class="alert alert-info">
      <h5>Information</h5>
      <ul>
        <li>Beim gemischten Doppel immer den Herren als Erstes melden. Sonst klappt diese nicht!</li>
        <li>Sollte ein Spieler <strong>nicht</strong> in dieser Liste vorkommen, bitte an den <a href="<?php echo $_smarty_tpl->tpl_vars['linkToSupport']->value;?>
">Support</a> wenden. Wir tragen ihn nach!</li>
      </ul>
    </div>

    <h3 class="mt-5 mb-5">Spieler</h3>

    <div class="row initline">
      <div class="col-md-4 input-control" data-role="select">
        <label class="d-block font-weight-bold">Spieler</label>
        <select class="js-example-data-array form-control d-block" name="tournamentPlayerId[]" placeholder="Bitte wählen">
        </select>
      </div>

      <div class="col-md-4">
          <div class="form-group">
              <label class="d-block font-weight-bold">Partner</label>
              <select class="js-example-data-array form-control d-block" name="tournamentPartnerId[]" placeholder="Bitte wählen">
                <option value="0">Bitte wählen</option>
              </select>
          </div>
      </div>

      <div class="col-md-4">
        <label class="d-block font-weight-bold">Diziplin</label>
        <select class="js-example-basic-single form-control " name="tournamentDiziplin[]" placeholder="Bitte wählen">
          <option value="0">Bitte wählen</option>
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['disciplines']->value, 'discipline');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['discipline']->value) {
?>
            <option value="<?php echo $_smarty_tpl->tpl_vars['discipline']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['discipline']->value;?>
</option>
          <?php
}
} else {
?>

            Leider keine Diziplinen<br>
          <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        </select>
      </div>
    </div>

    <div id="containerClone"></div>

    <div class="row mt-5 mb-5" >
      <div class="col-md-12 text-center">
        <a href="#" class="clonerow"><i class="glyphicon glyphicon-share-alt"></i> weiteren Spieler hinzufügen</a>
      </div>
    </div>

    <p></p>

    <div class="row">
      <div class="col-md-6">
        <input type="submit" name="submit" class="btn btn-success btn-wide" value="Melden">
      </div>
      <div class="col-md-6 text-right">
        <a class="btn btn-danger" href="?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
">Zurück</a>
      </div>
    </div>
    <!--
    <input type="submit" name="submitClose" class="btn btn-info btn-wide" value="Melden + Schließen ">
  -->
  </form>

<?php }?>

<?php echo '<script'; ?>
>


  var data = [{id: 0, text: 'Bitte wählen'}];

  //data.push({id: 1, text: 'da'});

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['clubs']->value, 'club');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['club']->value) {
?>
  data.push({
    "text": "<?php echo $_smarty_tpl->tpl_vars['club']->value['name'];?>
",
    "children": [
  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['club']->value['players'], 'player');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['player']->value) {
?>
    {
      id: <?php echo $_smarty_tpl->tpl_vars['player']->value['userId'];?>
,
      text: '<?php echo $_smarty_tpl->tpl_vars['player']->value['fullName'];?>
'
    },
  <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

  ]});
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php echo '</script'; ?>
>
<?php }
}
