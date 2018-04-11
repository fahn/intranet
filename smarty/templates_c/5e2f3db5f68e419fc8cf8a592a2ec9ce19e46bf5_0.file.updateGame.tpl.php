<?php
/* Smarty version 3.1.31, created on 2018-02-20 23:22:16
  from "/var/www/bc-comet_de/intern/smarty/templates/ranking/updateGame.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a8c9f982cbbf3_74111597',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e2f3db5f68e419fc8cf8a592a2ec9ce19e46bf5' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/ranking/updateGame.tpl',
      1 => 1519165330,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a8c9f982cbbf3_74111597 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h3>Report a Game for Badminton Ranking</h3>
<?php if ($_smarty_tpl->tpl_vars['action']->value == "update") {?>
<div class="alert alert-danger">
  >> Bearbeiten klappt noch nicht!
</div>
<?php }?>
<hr/>
<form action="" method="post">
  <input type="hidden" id="rankingGameWinner" name="rankingGameWinner" value="">
  <input type="hidden" id="rankingFormAction" name="rankingFormAction" value="<?php if ($_smarty_tpl->tpl_vars['action']->value == "update") {?>Update Game<?php } else { ?>Insert Game<?php }?>">
  <div class="row">
    <div class="col-md-6">
      <label for="rankingGameDate">Datum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input type="text" class="form-control" id="rankingGameDate" name="rankingGameDate" placeholder="" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['game']->value['datetime'],"d.m.Y");?>
">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group" >
        <label for="rankingGameTime">Uhrzeit:</label>
        <input class="form-control" type="text" id="rankingGameTime" name="rankingGameTime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['game']->value['datetime'],"H:i");?>
">
      </div>
    </div>
  </div>

<div class="row">
  <div class="col-md-6">
        <div class="card">
            <h5 class="card-header">Team 1</h5>
            <div class="card-body">
                <p class="card-text">
                  <label for="rankingGamePlayerA1" class="d-block">Spieler A:</label>
                  <select class="js-example-data-array form-control d-block" name="rankingGamePlayerA1" id="rankingGamePlayerA1" placeholder="Bitte wählen">
                    <option value="0">Bitte wählen</option>
                  </select>

                  <label for="rankingGamePlayerA2" class="d-block">Spieler A:</label>
                  <select class="js-example-data-array form-control d-block" name="rankingGamePlayerA2" id="rankingGamePlayerA2" placeholder="Bitte wählen">
                    <option value="0">Bitte wählen</option>
                  </select>
                </p>
            </div>
        </div>
  </div>

  <div class="col-md-6">
    <div class="card">
        <h5 class="card-header">Team 2</h5>
        <div class="card-body">
          <label for="rankingGamePlayerB1" class="d-block">Spieler B:</label>
          <select class="js-example-data-array form-control d-block" name="rankingGamePlayerB1" id="rankingGamePlayerB1" placeholder="Bitte wählen">
            <option value="0">Bitte wählen</option>
          </select>

          <label for="rankingGamePlayerB2" class="d-block">Spieler B:</label>
          <select class="js-example-data-array form-control d-block" name="rankingGamePlayerB2" id="rankingGamePlayerB2" placeholder="Bitte wählen">
            <option value="0">Bitte wählen</option>
          </select>
        </div>
      </div>
    </div>
  </div>


  <div class="row mt-5">
    <div class="col-md-4">
      <div class="card">
        <h5 class="card-header">Satz 1</h5>
        <div class="card-body">
          <input class="form-control text-center" type="text" name="rankingGameSet1" id="rankingGameSet1" value="<?php echo $_smarty_tpl->tpl_vars['game']->value['set1'];?>
" placeholder="21:19" pattern="^[0-9]{1,2}:[0-9]{1,2}$" required >
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <h5 class="card-header">Satz 2</h5>
        <div class="card-body">
          <input class="form-control text-center" type="text" name="rankingGameSet2" id="rankingGameSet2" value="<?php echo $_smarty_tpl->tpl_vars['game']->value['set2'];?>
" placeholder="21:19" pattern="^[0-9]{1,2}:[0-9]{1,2}$" required >
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <h5 class="card-header">Satz 3</h5>
        <div class="card-body">
          <input class="form-control text-center" type="text" name="rankingGameSet3" id="rankingGameSet3" value="<?php echo $_smarty_tpl->tpl_vars['game']->value['set3'];?>
" placeholder="21:19" pattern="^[0-9]{1,2}:[0-9]{1,2}$" >
        </div>
      </div>
    </div>
  </div>

<input class="btn btn-success mt-5 mb-5" type="submit" name="submit" value="Eintragen">
</form>
</div>


<?php echo '<script'; ?>
>

var data = [{id: 0, text: 'Bitte wählen'}];


<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['players']->value, 'player');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['player']->value) {
?>
    data.push({
        id: <?php echo $_smarty_tpl->tpl_vars['player']->value['userId'];?>
,
        text: '<?php echo $_smarty_tpl->tpl_vars['player']->value['fullName'];?>
'
    });
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


$( document ).ready(function() {
  <?php if ($_smarty_tpl->tpl_vars['game']->value['playerA1']) {?>
    $('#rankingGamePlayerA1').val(144).trigger('change');
  <?php }?>
});
<?php echo '</script'; ?>
>
<?php }
}
