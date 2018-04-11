<?php
/* Smarty version 3.1.31, created on 2018-01-25 09:14:33
  from "/var/www/bc-comet_de/intern/smarty/templates/admin/ClubEdit.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a6991e95b7f05_52899528',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bd111ae8f1bd4651a2ff3721d833b9b443c3fae8' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/admin/ClubEdit.tpl',
      1 => 1511771596,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a6991e95b7f05_52899528 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h2>Verein <?php if ($_smarty_tpl->tpl_vars['action']->value == 'edit') {?>Editieren<?php } else { ?>hinzufügen<?php }?></h2>

<form action="" method="post">
  <input type="hidden" name="clubFormAction" id="clubFormAction" value="<?php if ($_smarty_tpl->tpl_vars['action']->value == 'edit') {?>Update Club<?php } else { ?>Insert Club<?php }?>">
  <input type="hidden" name="clubClubId" id="clubClubId" value="<?php if ($_smarty_tpl->tpl_vars['variable']->value['clubId']) {
echo $_smarty_tpl->tpl_vars['variable']->value['clubId'];
}?>">

  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for = "clubName">Name des Clubs:</label>
        <input class="form-control"  type="text" id="clubName" name="clubName" placeholder="BC Comet Braunschweig" value="<?php if ($_smarty_tpl->tpl_vars['variable']->value['name']) {
echo $_smarty_tpl->tpl_vars['variable']->value['name'];
}?>" required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for = "clubClubNumber">Vereinsnummer</label>
        <input class="form-control"  type="text" id="clubClubNumber" name="clubClubNumber" placeholder="100000" value="<?php if ($_smarty_tpl->tpl_vars['variable']->value['clubNumber']) {
echo $_smarty_tpl->tpl_vars['variable']->value['clubNumber'];
}?>" required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for = "clubAssociation">Verband</label>
        <input class="form-control"  type="text" id="clubAssociation" name="clubAssociation" placeholder="BS-BS" value="<?php if ($_smarty_tpl->tpl_vars['variable']->value['association']) {
echo $_smarty_tpl->tpl_vars['variable']->value['association'];
}?>" required>
      </div>
    </div>
  </div>


  <div class="row initline">
    <div class="col-md-6">
      <input type="submit" name="submit" class="btn btn-success btn-wide" value="<?php if ($_smarty_tpl->tpl_vars['action']->value == 'edit') {?>Editieren<?php } else { ?>hinzufügen<?php }?>">
    </div>
    <div class="col-md-6 text-right">
      <a class="btn btn-danger" href="/pages/adminAllClub.php">Zurück</a>
    </div>
  </div>
  <!--
  <input type="submit" name="submitClose" class="btn btn-info btn-wide" value="Melden + Schließen ">
-->
</form>
<?php }
}
