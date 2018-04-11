<?php
/* Smarty version 3.1.31, created on 2018-02-20 11:31:39
  from "/var/www/bc-comet_de/intern/smarty/templates/admin/UserDelete.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a8bf90b9e1dc3_02813086',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '86cc980852e61c743343da7686edd8af9b3b701d' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/admin/UserDelete.tpl',
      1 => 1517491537,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a8bf90b9e1dc3_02813086 (Smarty_Internal_Template $_smarty_tpl) {
?>
<form action="" method="post">
  <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="<?php echo $_smarty_tpl->tpl_vars['hidden']->value;?>
">
  <input type="hidden" name="userRegisterAccountAdminUserId" id="userRegisterAccountAdminUserId" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['userId'];?>
">
  <h4>Möchtest du den folgenden Benutzer löschen ?</h4>
  <p>
    Vorname: <?php echo $_smarty_tpl->tpl_vars['user']->value['firstName'];?>
 <br>
    Nachname: <?php echo $_smarty_tpl->tpl_vars['user']->value['lastName'];?>

  </p>
  <div class="form-check mb-5 mt-5 text-center">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input">
      Ja, bitte löschen
    </label>
  </div>

  <button type="submit" class="btn btn-danger">Benutzer löschen</button>

  <a class="btn btn-info  float-right" href="">Zurück</a>
</form>
<?php }
}
