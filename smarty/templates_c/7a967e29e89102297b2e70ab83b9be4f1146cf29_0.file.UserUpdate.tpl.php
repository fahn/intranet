<?php
/* Smarty version 3.1.31, created on 2018-02-25 20:45:48
  from "/var/www/bc-comet_de/intern/smarty/templates/admin/UserUpdate.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a93126c5f0db1_61949489',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7a967e29e89102297b2e70ab83b9be4f1146cf29' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/admin/UserUpdate.tpl',
      1 => 1519479088,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a93126c5f0db1_61949489 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h1 class="display-1 mb-5">Spieler <?php if ($_smarty_tpl->tpl_vars['task']->value == "edit") {?>bearbeiten<?php } else { ?>Hinzufügen<?php }?></h1>

<form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="<?php echo $_smarty_tpl->tpl_vars['hidden']->value;?>
">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountFirstName">Vorname</label>
                <input class="form-control"  type="text" id="userRegisterAccountFirstName" name="userRegisterAccountFirstName" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['firstName'];?>
" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountLastname">Nachname</label>
                <input class="form-control"  type="text" id="userRegisterAccountLastname" name="userRegisterAccountLastName" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['lastName'];?>
" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountEmail">E-Mail</label>
                <input class="form-control"  type="text" id="userRegisterAccountEmail" name="userRegisterAccountEmail" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['email'];?>
">
            </div>
        </div>

        <div class="col-md-6">
          <label class="d-block">Geschlecht</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="userRegisterAccountGender" id="inlineRadio1" value="Male" <?php if ($_smarty_tpl->tpl_vars['info']->value['gender'] == "Male") {?>checked<?php }?>>
            <label class="form-check-label" for="inlineRadio1">Mann</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="userRegisterAccountGender" id="inlineRadio2" value="Female" <?php if ($_smarty_tpl->tpl_vars['info']->value['gender'] == "Female") {?>checked<?php }?>>
            <label class="form-check-label" for="inlineRadio2">Frau</label>
          </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="userRegisterAccountBday">Geburtsdatum</label>
            <div class="input-group date" data-provide="datepicker">
                <input class="form-control" type="text" id="userRegisterAccountBday" name="userRegisterAccountBday" placeholder="dd.mm.YYYY" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['info']->value['bday'],'d.m.Y');?>
">
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountPhone">Telefon</label>
                <input class="form-control"  type="text" id="userRegisterAccountPhone" name="userRegisterAccountPhone" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['phone'];?>
">

            </div>
        </div>
    </div>

    <h3 class="display-3 mt-5 mb-5">Verein</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountPlayerId">Spielernummer</label>
                <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['playerId'];?>
">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountClubId">Verein</label>
                <select class="form-control" name="userRegisterAccountClubId">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['clubs']->value, 'club');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['club']->value) {
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['club']->value['clubId'];?>
" <?php if ($_smarty_tpl->tpl_vars['club']->value['clubId'] == $_smarty_tpl->tpl_vars['info']->value['clubId']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['club']->value['name'];?>
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

    <h3 class="display-3 mt-5 mb-5">Generelles</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="checkbox" for="userRegisterAccountIsPlayer"><input type="checkbox" value="1" id="userRegisterAccountIsPlayer" name="userRegisterAccountIsPlayer" data-toggle="checkbox" class="custom-checkbox" <?php if ($_smarty_tpl->tpl_vars['info']->value['activePlayer'] == 1) {?>checked<?php }?>>
                <span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span> Aktiver Spieler
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="checkbox" for="userRegisterAccountIsReporter"><input type="checkbox" value="1" id="userRegisterAccountIsReporter" name="userRegisterAccountIsReporter" data-toggle="checkbox" class="custom-checkbox" <?php if ($_smarty_tpl->tpl_vars['info']->value['reporter'] == 1) {?>checked<?php }?>>
                <span class="icons">
                  <span class="icon-unchecked"></span>
                  <span class="icon-checked"></span>
                </span> Reporter
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="checkbox" for="userRegisterAccountIsAdmin"><input type="checkbox" value="1" id="userRegisterAccountIsAdmin" name="userRegisterAccountIsAdmin" data-toggle="checkbox" class="custom-checkbox" <?php if ($_smarty_tpl->tpl_vars['info']->value['admin'] == 1) {?>checked<?php }?>>
                <span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span> Admin
            </div>
        </div>
    </div>
    <p></p>

    <div class="row initline">
        <div class="col-md-6">
            <input type="submit" name="submit" class="btn btn-success btn-wide" value="<?php if ($_smarty_tpl->tpl_vars['task']->value == "edit") {?>Bearbeiten<?php } else { ?>Hinzufügen<?php }?>">
        </div>
    </div>
</form>
<?php }
}
