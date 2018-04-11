<?php
/* Smarty version 3.1.31, created on 2018-03-17 10:22:46
  from "/var/www/bc-comet_de/intern/smarty/templates/tournament/TournamentList.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5aacde6655d426_54590792',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6e531f8d446cc6863a0dd57d15e1a878cef15eb4' => 
    array (
      0 => '/var/www/bc-comet_de/intern/smarty/templates/tournament/TournamentList.tpl',
      1 => 1521278566,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5aacde6655d426_54590792 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/bc-comet_de/intern/smarty/libs/plugins/modifier.date_format.php';
?>
<h1 class="display-1 mb-5">Turniere / Ranglisten</h1>

<div id="map" class="mb-5" style="width: 100%; height: 400px !important"></div>


<div class="row">
  <div class="col-md-6">
    <div class="form-group">
        <select class="form-control"  id="mySelector">
          <option value="">Filterung der Turniere: Bitte wählen</option>
          <option value='U'>Jugend (Bis U19)</option>
          <option value='O19'>Erwachsene (O19)</option>
          <option value='O'>Alters (Ab O35)</option>
        </select>
    </div>
  </div>

  <?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value) {?>
  <div class="col-md-6 text-right">
        <a class="btn btn-success" href="?action=add_tournament">Turnier hinzufügen</a>
  </div>
  <?php }?>
</div>

<div class="table-responsive">
  <table id="myTable" class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Altersklasse</th>
        <th>Name</th>
        <th>Ort</th>
        <th>Datum</th>
        <th>Meldeschluss</th>
        <th>Ausschreibung</th>
        <th>Teilnehmer</th>
        <th>Optionen</th>
      </tr>
    </thead>
    <tbody>
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tournamentList']->value, 'tournament');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tournament']->value) {
?>
        <tr>
          <td><?php echo $_smarty_tpl->tpl_vars['tournament']->value['classification'];?>
</td>
          <td><a  class="text-<?php if (time() < strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline'])) {?>success<?php } else { ?>danger<?php }?>" href="?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
"><?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
</a></td>
          <td><?php echo $_smarty_tpl->tpl_vars['tournament']->value['place'];?>
</td>
          <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['startdate'],"%d.%m.%Y");
if ($_smarty_tpl->tpl_vars['tournament']->value['startdate'] != $_smarty_tpl->tpl_vars['tournament']->value['enddate']) {?> - <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['enddate'],"%d.%m.%Y");
}?></td>
          <td class="text-<?php if (time() < strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline'])) {?>success<?php } else { ?>danger<?php }?>"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['deadline'],"%d.%m.%Y");?>
</td>
          <td class="text-center"><?php if ($_smarty_tpl->tpl_vars['tournament']->value['link']) {?><a href="<?php echo $_smarty_tpl->tpl_vars['tournament']->value['link'];?>
" target="_blank">Link</a><?php } else { ?>-<?php }?></td>
          <td class="text-center"><?php echo $_smarty_tpl->tpl_vars['tournament']->value['userCounter'];?>
 <i class="fas fa-users"></i></td>
          <td>
            <?php if ($_smarty_tpl->tpl_vars['tournament']->value['openSubscription'] == 1 && time() < strtotime($_smarty_tpl->tpl_vars['tournament']->value['deadline'])) {?>
                <a class="btn btn-success" href="?action=add_player&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
">Eintragen</a></td>
            <?php } else { ?>
                <a class="btn btn-primary btn-block" href="?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
">Details</a>
            <?php }?>
        </tr>
      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </tbody>
  </table>
</div>

<?php if ($_smarty_tpl->tpl_vars['isAdmin']->value || $_smarty_tpl->tpl_vars['isReporter']->value) {?>
  <h2 class="display-2 mt-5">Alte Turniere</h2>
  <ul>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['oldTournamentList']->value, 'tournament');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tournament']->value) {
?>
      <li><a href="?action=details&id=<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
"><?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
</a> in <?php echo $_smarty_tpl->tpl_vars['tournament']->value['place'];?>
 vom <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['startdate'],"%d.%m.%Y");?>
 - <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['tournament']->value['enddate'],"%d.%m.%Y");?>
</li>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

  </ul>
<?php }?>



<?php echo '<script'; ?>
>

var map;
      function initMap() {
        var infowindow = new google.maps.InfoWindow(); /* SINGLE */

        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 7,
          center: new google.maps.LatLng(52.2448374, 10.477203),
          mapTypeId: 'roadmap'
        });

        var iconBase = '/static/img/marker/';
        var icons = {
          library: {
            icon: iconBase + 'nbv.png',
            scaledSize: new google.maps.Size(32, 32)
          },
          info: {
            icon: iconBase + 'bccomet.png',

          }
        };

        var features = [
          {
            name: 'BC Comet Hauptquatier',
            place: 'Braunschweig',
            position: new google.maps.LatLng(52.2448374, 10.477203),
            type: 'info'
          },
        ];

        

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tournamentList']->value, 'tournament');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tournament']->value) {
?>
            features.push({
              id:  '<?php echo $_smarty_tpl->tpl_vars['tournament']->value['tournamentID'];?>
',
              name: '<?php echo $_smarty_tpl->tpl_vars['tournament']->value['name'];?>
',
              place: '<?php echo $_smarty_tpl->tpl_vars['tournament']->value['place'];?>
',
              start: '<?php echo $_smarty_tpl->tpl_vars['tournament']->value['startdate'];?>
',
              end:   '<?php echo $_smarty_tpl->tpl_vars['tournament']->value['enddate'];?>
',
              deadline: '<?php echo $_smarty_tpl->tpl_vars['tournament']->value['deadline'];?>
',
              position: new google.maps.LatLng(<?php echo $_smarty_tpl->tpl_vars['tournament']->value['latitude'];?>
, <?php echo $_smarty_tpl->tpl_vars['tournament']->value['longitude'];?>
),
              type: 'library',
            });
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


        
        // Create markers.
        features.forEach(function(feature) {
          var marker = new google.maps.Marker({
            position: feature.position,
            icon: {
              url: icons[feature.type].icon,
              scaledSize: new google.maps.Size(32, 32),
            },
            map: map
          });

          contentString = '<div id="infowindow">'+
           '<div id="siteNotice">'+
           '</div>'+
           '<h1 id="firstHeading" class="firstHeading">'+ feature.name +'</h1>'+
           '<div id="bodyContent">'+
           '<p>Von '+ feature.start +' - '+ feature.end +' ('+ feature.deadline +')</p>'+
           '<p>Ort: '+ feature.place +'</p>'+
           '<p><a href="https://int.bc-comet.de/pages/rankingTournament.php?action=details&id='+ feature.id +'">Details</a></p>'+
           '</div>'+
           '</div>';

          google.maps.event.addListener(marker, 'click', function(){
                  if (feature.id) {
                    content = "<div id='infowindow'><a href='https://int.bc-comet.de/pages/rankingTournament.php?action=details&id="+ feature.id +"'>"+ feature.name +" in "+ feature.place +"</a><br></div>";
                  } else {
                    content = "<div id='infowindow'>"+ feature.name +" in "+ feature.place +"</div>";
                  }
                   infowindow.close(); // Close previously opened infowindow
                   infowindow.setContent(content);
                   infowindow.open(map, marker);
               });
        });
      }



<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt9xKhw9W6PJtpAn8CppDFFwPK_RjFetk&callback=initMap"><?php echo '</script'; ?>
>
<?php }
}
