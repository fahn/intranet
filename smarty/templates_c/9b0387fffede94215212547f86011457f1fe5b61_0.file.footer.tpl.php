<?php
/* Smarty version 3.1.31, created on 2018-02-20 17:38:16
  from "/var/www/bc-comet_de/intern/smarty/templates/footer.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a8c4ef8b5d100_57598759',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9b0387fffede94215212547f86011457f1fe5b61' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/footer.tpl',
      1 => 1519144687,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a8c4ef8b5d100_57598759 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['isUserLoggedIn']->value) && $_smarty_tpl->tpl_vars['isUserLoggedIn']->value == 1) {?>
  <footer>
      <nav class="navbar navbar-default" role="navigation">
          <!-- Brand and toggle get grouped for better mobile display -->

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                  <li><a href="#">Badminton Ranking - Version 1.0.2 BETA</a></li>
                  <li><a href="infoManual.php">Manual</a></li>
                  <li><a href="infoLicense.php">License</a></li>
                  <li><a href="infoThirdPartyLicenses.php">TPLicenses</a></li>
                  <li><a href="infoChangeLog.php">Changelog</a></li>
                  <li><a href="infoImpressum.php">Impressum</a></li>
              </ul>
          </div>
      </nav>
  </footer>
  </div>
<?php }?>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <?php echo '<script'; ?>
 src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"><?php echo '</script'; ?>
>


    <!-- datepicker -->
    <?php echo '<script'; ?>
 src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.de.min.js"><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.standalone.css" />


    <!-- font awesome -->
    <?php echo '<script'; ?>
 defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"><?php echo '</script'; ?>
>

    <!-- select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />
    <?php echo '<script'; ?>
 src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js"><?php echo '</script'; ?>
>


    <!-- include summernote css/js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <?php echo '<script'; ?>
 src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"><?php echo '</script'; ?>
>



    <!-- custom -->
    <?php echo '<script'; ?>
 src="/static/js/custom.js"><?php echo '</script'; ?>
>

    <link rel="stylesheet" href="/static/css/custom.css">

</body>
</html>
<?php }
}
