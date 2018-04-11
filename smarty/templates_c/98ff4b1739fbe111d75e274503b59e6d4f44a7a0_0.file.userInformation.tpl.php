<?php
/* Smarty version 3.1.31, created on 2018-02-20 16:10:16
  from "/var/www/bc-comet_de/intern/smarty/templates/userInformation.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a8c3a58beb5b1_88541865',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '98ff4b1739fbe111d75e274503b59e6d4f44a7a0' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/userInformation.tpl',
      1 => 1519139415,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a8c3a58beb5b1_88541865 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
if (!$_smarty_tpl->tpl_vars['user']->value) {?>
    <div class="alert alert-danger text-center">Bitte wählen Sie einen gültigen User aus</div>
<?php } else { ?>


    <div class="card card-profile text-center pt-2">
        <h4 class="card-title"><?php echo $_smarty_tpl->tpl_vars['user']->value['firstName'];?>
 <?php echo $_smarty_tpl->tpl_vars['user']->value['lastName'];?>
</h4>
        <div class="card-block">
            <?php if ($_smarty_tpl->tpl_vars['user']->value['image']) {?>
                <img src="/static/img/user/<?php echo $_smarty_tpl->tpl_vars['user']->value['image'];?>
" name="aboutme" width="140" height="140" border="0" class="card-img-profile"">
            <?php } else { ?>
                <img src="/static/img/user/default_<?php if ($_smarty_tpl->tpl_vars['user']->value['gender'] == "Male") {?>m<?php } else { ?>w<?php }?>.png" name="about <?php echo $_smarty_tpl->tpl_vars['user']->value['firstName'];?>
 <?php echo $_smarty_tpl->tpl_vars['user']->value['lastName'];?>
" width="140" height="140" border="0" class="card-img-profile"">
            <?php }?>
        </div>

    </div>

<?php if ($_smarty_tpl->tpl_vars['user']->value['userId'] == $_smarty_tpl->tpl_vars['userId']->value || $_smarty_tpl->tpl_vars['isAdmin']->value) {?>
    <p class="text-right">
        <a class="btn btn-danger" href="/pages/myAccount.php">Editieren</a>
        <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value) {?>
            <a class="btn btn-danger" href="/pages/adminAllUser.php?action=edit&id=<?php echo $_smarty_tpl->tpl_vars['user']->value['userId'];?>
">Admin-Edit</a>
        <?php }?>
    </p>
<?php }?>

<div class="row">
    <div class="col-md-12">
        <div class="card mt-4 mb-4">
            <h5 class="card-header">Informationen</h5>
            <div class="card-body">
                <p class="card-text">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if (intval($_smarty_tpl->tpl_vars['user']->value['bday']) != 0) {?>
                              <p><strong>Geburtstag:</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['user']->value['bday'],"d.m.Y");?>
</p>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReported']->value) {?>
                              <?php if ($_smarty_tpl->tpl_vars['user']->value['email']) {?>
                                <p><strong>E-Mail:</strong> <?php echo $_smarty_tpl->tpl_vars['user']->value['email'];?>
</p>
                              <?php }?>
                              <?php if ($_smarty_tpl->tpl_vars['user']->value['phone']) {?>
                                <p><strong>Telefon:</strong> <?php echo $_smarty_tpl->tpl_vars['user']->value['phone'];?>
</p>
                              <?php }?>
                            <?php }?>
                        </div>
                        <div class="col-md-6">
                            <?php if ($_smarty_tpl->tpl_vars['user']->value['playerId']) {?>
                                <p><strong>Spielernummer:</strong> <?php echo $_smarty_tpl->tpl_vars['user']->value['playerId'];?>
</p>
                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['club']->value['name']) {?>
                                <p><strong>Verein: </strong> <?php echo $_smarty_tpl->tpl_vars['club']->value['name'];?>
</p>
                            <?php }?>
                        </div>
                    </div>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if ($_smarty_tpl->tpl_vars['user']->value['clubId'] == 1) {?>
        <div class="col-md-6">
            <div class="card mt-4 mb-4">
                <h5 class="card-header">Letzte interne Ranglistenspiele</h5>
                <div class="card-body">
                    <p class="card-text">
                      <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Datum</th>
                            <th>Gegner</th>
                            <th colspan="2" class="text-center">Ergebnis</th>
                          </tr>
                        </thead>
                        <tbody>
                      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['games']->value, 'game');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['game']->value) {
?>
                          <tr>
                            <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['game']->value['datetime'],"d.m.Y");?>
</td>
                            <td><a href="#"><?php echo $_smarty_tpl->tpl_vars['game']->value['opponent'];?>
</a></td>
                            <td><?php echo $_smarty_tpl->tpl_vars['game']->value['chicken'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['game']->value['result'];?>
</td>
                          </tr>
                      <?php
}
} else {
?>

                        <tr>
                          <td>Es wurden noch keine Spiele gemacht.</td>
                        </tr>
                      <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                      </tbody>
                    </table>
                    </p>
                </div>
            </div>
        </div>
    <?php }?>
    <div class="col-md-6">
        <div class="card mt-4 mb-4">
            <h5 class="card-header">Letzten 10 offizielle Turniere/Ranglisten</h5>
            <div class="card-body">
                <?php if ($_smarty_tpl->tpl_vars['tournament']->value) {?>
                <div class="table-responsive">
                  <table class="table table-sm table-striped table-hover">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tournament']->value, 'tn');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tn']->value) {
?>
                      <tr>
                        <td><a href="/pages/rankingTournament.php?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tn']->value['tournamentID'];?>
"><?php echo $_smarty_tpl->tpl_vars['tn']->value['name'];?>
</td>
                        <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tn']->value['startdate'],"d.m.y");?>
 - <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tn']->value['enddate'],"d.m.y");?>
 </td>
                      </tr>
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                  </table>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>

<p class="text-right">
  <a class="btn btn-danger" href="javascript:history.back()">Zurück</a>
</p>
<?php }
}
}
