<?php
/* Smarty version 3.1.31, created on 2018-03-26 11:30:23
  from "/var/www/bc-comet_de/intern/smarty/templates/admin/UserList.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ab8bdaf34e6c6_92797162',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8e95c0189368b01a98d5adb3034e6f2d099e8df9' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/admin/UserList.tpl',
      1 => 1522056618,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_pagination.tpl' => 2,
  ),
),false)) {
function content_5ab8bdaf34e6c6_92797162 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.truncate.php';
?>
<h1 class="display-1">Benutzerverwaltung</h1>
<div class="alert alert-info">
    <p>Hier werden <strong>alle</strong> Spieler nach dem Nachnamen aufgelistet. Egal, ob Sie vom BC Comet sind  oder von anderen Vereinen.<br></p>
 </div>

<p class="text-right">
  <a class="btn btn-success" href="?action=add_player">SpielerInnen hinzufügen</a>
</p>

<?php if ($_smarty_tpl->tpl_vars['pagination']->value) {?>
  <?php $_smarty_tpl->_subTemplateRender("file:_pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php }?>

<div class="table-responsive">
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>E-Mail</th>
        <th>Verein</th>
        <th>m/w</th>
        <th class="text-center">Reporter</th>
        <th class="text-center">Admin</th>
        <th class="text-center">Optionen</th>
      </tr>
    </thead>
    <tbody>
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['users']->value, 'user');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
?>
        <tr>
          <td><?php if (!$_smarty_tpl->tpl_vars['user']->value['email']) {?><span data-toggle="tooltip" data-placement="top" title="Benutzer kann sicht ohne gültige E-Mail-Adresse nicht einloggen."><i class="text-danger fas fa-exclamation-triangle" ></i></span> <?php }
echo $_smarty_tpl->tpl_vars['user']->value['firstName'];?>
</td>
          <td><?php echo $_smarty_tpl->tpl_vars['user']->value['lastName'];?>
</td>
          <td><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['user']->value['email'],10,"...",true);?>
</td>
          <td><?php echo $_smarty_tpl->tpl_vars['user']->value['clubName'];?>
</td>
          <td class="text-center"><?php if ($_smarty_tpl->tpl_vars['user']->value['gender'] == "Male") {?><i class="fas fa-male"></i><?php } else { ?><i class="fas fa-female"></i><?php }?></td>
          <td class="text-center"><?php if ($_smarty_tpl->tpl_vars['user']->value['isReporter']) {?><i class="text-success far fa-check-circle"></i><?php } else { ?><i class="text-danger far fa-times-circle"></i><?php }?></td>
          <td class="text-center"><?php if ($_smarty_tpl->tpl_vars['user']->value['isAdmin']) {?><i class="text-success far fa-check-circle"></i><?php } else { ?><i class="text-danger far fa-times-circle"></i><?php }?></td>
          <td class="text-center"><a class="btn btn-info" href="?action=edit&id=<?php echo $_smarty_tpl->tpl_vars['user']->value['userId'];?>
">Editieren</a> <a class="btn btn-danger" href="?action=delete&id=<?php echo $_smarty_tpl->tpl_vars['user']->value['userId'];?>
">Löschen</a></td>
        </tr>
      <?php
}
} else {
?>

        <tr>
          <td colspan="8">Failed to get all User from data base. Reason: <?php if ($_smarty_tpl->tpl_vars['error']->value) {?> <?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 <?php }?></td>
        </tr>
      <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </tbody>
  </table>
</div>

<?php if ($_smarty_tpl->tpl_vars['pagination']->value) {?>
  <?php $_smarty_tpl->_subTemplateRender("file:_pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

<?php }
}
}
