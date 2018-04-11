<?php
/* Smarty version 3.1.31, created on 2018-03-26 23:36:12
  from "/var/www/bc-comet_de/intern/smarty/templates/support.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ab967cc9b9168_12544880',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fc49bdd4ccc215d732fc628a7d629c113d3274bb' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/support.tpl',
      1 => 1522100148,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab967cc9b9168_12544880 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h3>Support</h3>
<div class="alert alert-info">
    <p>Wenn du Fragen, Informationen, Anregungen oder dich am Portal beteiligen willst, dann schreibe uns:</p>
    <p>Deine Nachricht wird als E-Mail an uns geschickt</p>
</div>
<form action="" method="post">
  <input type="hidden" name="supportFormAction" id="supportFormAction" value="Contact Us">

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="supportSubject">Dein Betreff:</label>
        <input class="form-control"  type="text" id="supportSubject" name="supportSubject" placeholder="Dein Betreff" required>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
      <label for="sel1">Kategorie:</label>
      <select class="form-control" id="sel1">
        <option>Allgemeines</option>
        <option>Probleme beim Intern</option>
        <option>Vorstand</option>
        <option>Sosntiges</option>
      </select>
    </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="supportMessage">Deine Nachricht:</label>
        <textarea class="form-control" rows="10"  type="text" id="supportMessage" name="supportMessage" placeholder="Deine Nachricht" required><?php echo $_smarty_tpl->tpl_vars['text']->value;?>
</textarea>
      </div>
    </div>
  </div>

  <input class="btn btn-success" type="submit" name="submit" value="Nachricht senden">
</form>
<?php }
}
