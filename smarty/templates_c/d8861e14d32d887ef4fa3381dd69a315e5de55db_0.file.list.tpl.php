<?php
/* Smarty version 3.1.31, created on 2018-03-16 17:45:04
  from "/var/www/bc-comet_de/intern/smarty/templates/team/list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5aabf490444738_74403980',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd8861e14d32d887ef4fa3381dd69a315e5de55db' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/team/list.tpl',
      1 => 1521218703,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5aabf490444738_74403980 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h1 class="display-1 mb-5">Das Team</h1>
<div class="alert alert-info">
  <h2></h2>
  <p>Auf dieser Seite findet ihr alle Personen rund um den Vorstand, weitere Personen und die technischen Ansprechpartner. </p>
  <p>Diese Informationen findet ihr alternativ auf der <a href="http://www.bc-comet.de/training/vorstand/" target="_blank">BC Comet Webseite</a>.</p>
</div>
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value, 'list', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['list']->value) {
?>
  <?php if ($_smarty_tpl->tpl_vars['key']->value == 1) {?>
   <h2 class="display-3">Vorstand</h2>
  <?php } elseif ($_smarty_tpl->tpl_vars['key']->value == 2) {?>
    <h3 class="display-3">Weitere Personen<h3>
  <?php } else { ?>
    <h3 class="display-3">Technische Ansprechpartner</h3>
  <?php }?>

  <div class="row mt-5 mb-5">
  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'user');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
?>
  <div class="col-md-3 mb-5 ">
  <div class="card">
    <img class="card-img-top" src="/static/img/user/<?php if ($_smarty_tpl->tpl_vars['user']->value['image']) {
echo $_smarty_tpl->tpl_vars['user']->value['image'];
} else { ?>default_<?php if ($_smarty_tpl->tpl_vars['user']->value['gender'] == "Male") {?>m<?php } else { ?>w<?php }?>.png<?php }?>" alt="<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
">
    <div class="card-body">
      <h5 class="card-title"><a href="/pages/user.php?id=<?php echo $_smarty_tpl->tpl_vars['user']->value['userId'];?>
"><?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
</a></h5>
      <p class="card-text"><strong><?php echo $_smarty_tpl->tpl_vars['user']->value['position'];?>
</strong> <?php if ($_smarty_tpl->tpl_vars['user']->value['description']) {?>// <?php echo $_smarty_tpl->tpl_vars['user']->value['description'];
}?></p>
    </div>
  </div>
  </div>
  <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

  </div>
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
