<?php
/* Smarty version 3.1.31, created on 2018-03-17 10:29:30
  from "/var/www/bc-comet_de/intern/smarty/templates/user/myaccount.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5aacdffa178220_50901770',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ec33612831d142db57aab5e6cb970878e08f3472' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/user/myaccount.tpl',
      1 => 1521278969,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5aacdffa178220_50901770 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h2 class="display-1 mb-5">Profil aktualisieren</h2>

<ul class="nav justify-content-center">
  <li class="nav-item">
    <a class="nav-link active" href="#">Profil</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#">Bild</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#">Password</a>
  </li>
</ul>

<hr>

<div id="formUserRegister">

  <form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="Update My Account ">


    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountFirstName">Vorname:</label>
          <input class="form-control"  type="text" id="userRegisterAccountFirstName" name="userRegisterAccountFirstName" placeholder="Dein Vorname" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['FNameValue'];?>
">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountLastName">Nachname:</label>
          <input class="form-control"  type="text" id="userRegisterAccountLastName" name="userRegisterAccountLastName" placeholder="Dein Nachname" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['LNameValue'];?>
">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountEmail">E-Mail-Adresse:</label>
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
          <label for="userRegisterAccountGender">Geschlecht:</label>
          <select class="custom-select" name="userRegisterAccountGender">
            <option value="Male" <?php if ($_smarty_tpl->tpl_vars['vars']->value['genderValue'] == 'Male') {?>selected<?php }?>> Mann</option>
            <option value="Female" <?php if ($_smarty_tpl->tpl_vars['vars']->value['genderValue'] == 'Female') {?>selected<?php }?>> Frau</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group" data-toggle="tooltip" title="Kann nur ein Admin ändern">
          <label for = "userRegisterAccountPlayerId">Spielernummer:</label>
          <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['playerIdValue'];?>
" disabled="disabled" >
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group" data-toggle="tooltip" title="Kann nur ein Admin ändern">
          <label for="userRegisterAccountClub">Verein:</label>
          <input class="form-control"  type="text" id="userRegisterAccountClub" name="userRegisterAccountClub" placeholder="" value="<?php echo $_smarty_tpl->tpl_vars['vars']->value['clubNameValue'];?>
" disabled="disabled">
        </div>
      </div>
    </div>



    <!-- Success / Default -->
    <h2 class="mt-5 mb-2">Passwort</h2>
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

    <input class="btn btn-success" type="submit" name="submit" value="Daten ändern">
  </form>
</div>
<?php }
}
