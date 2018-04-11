<?php
/* Smarty version 3.1.31, created on 2017-11-28 16:16:55
  from "/var/www/bc-comet_de/intern/smarty/templates/admin/register.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a1d7de7805282_70874146',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'af16e731ec95e3b892584435c716fc5ecfbeab6c' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/admin/register.tpl',
      1 => 1511771596,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1d7de7805282_70874146 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>


<div id="formUserRegister">
  <h3>Update Your Account for Badminton Ranking</h3>
  <p>Change your email, full name, gender and password.</p>
  <hr/>
  <form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="Update My Account ">


    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountFirstName">First Name:</label>
          <input class="form-control"  type="text" id="userRegisterAccountFirstName" name="userRegisterAccountFirstName" placeholder="Dein Vorname" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['FNameValue'];?>
">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountLastName">Last Name:</label>
          <input class="form-control"  type="text" id="userRegisterAccountLastName" name="userRegisterAccountLastName" placeholder="Dein Nachname" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['LNameValue'];?>
">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountEmail">E-mail:</label>
          <input class="form-control" type="text" id="userRegisterAccountEmail" name="userRegisterAccountEmail" placeholder="your.name@bc-comet.de" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['EmailValue'];?>
">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="userRegisterAccountPhone">Telefon:</label>
          <input class="form-control"  type="text" id="userRegisterAccountPhone" name="userRegisterAccountPhone" placeholder="0162 ..." value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['phoneValue'];?>
">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label for="userRegisterAccountBday">Geburtstag:</label>
        <div class="input-group date" data-provide="datepicker">
          <input type="text" class="form-control" id="userRegisterAccountBday" name="userRegisterAccountBday" placeholder="" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['vars']->value['bdayValue'],"d.m.Y");?>
">
          <div class="input-group-addon">
              <span class="glyphicon glyphicon-th"></span>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label style="display: block"  for="userRegisterAccountLastName">Geschlecht:</label>
          <input type="checkbox" checked data-toggle="switch" name="info-square-switch" data-on-color="success" id="switch-gender" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountPlayerId">Spielernr:</label>
          <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['playerIdValue'];?>
">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="userRegisterAccountClub">Verein:</label>
          <select class="form-control" id="userRegisterAccountClub" name="userRegisterAccountClub">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['clubs']->value, 'club');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['club']->value) {
?>
              <option value="<?php echo $_smarty_tpl->tpl_vars['club']->value['clubId'];?>
"><?php echo $_smarty_tpl->tpl_vars['club']->value['name'];?>

            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

          </select>
        </div>
      </div>
    </div>



    <!-- Success / Default -->
    <h4>Passwort</h4>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountPassword">Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountPassword" name="userRegisterAccountPassword" placeholder="" value="">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountPassword2">Repeat Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountPassword2" name="userRegisterAccountPassword2" placeholder="" value="">
        </div>
      </div>
    </div>

    <h4>Weiteres</h4>

    <input class="btn btn-success" type="submit" name="submit" value="Spieler anlegen">
  </form>
</div>
<?php }
}
