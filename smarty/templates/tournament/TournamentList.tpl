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

  {if $isAdmin or $isReporter}
  <div class="col-md-6 text-right">
        <a class="btn btn-success" href="?action=add_tournament">Turnier hinzufügen</a>
  </div>
  {/if}
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
      {foreach item=tournament from=$tournamentList}
        <tr>
          <td>{$tournament.classification}</td>
          <td><a  class="text-{if $smarty.now < $tournament.deadline|strtotime}success{else}danger{/if}" href="?action=details&id={$tournament.tournamentID}">{$tournament.name}</a></td>
          <td>{$tournament.place}</td>
          <td>{$tournament.startdate|date_format:"%d.%m.%Y"}{if $tournament.startdate != $tournament.enddate} - {$tournament.enddate|date_format:"%d.%m.%Y"}{/if}</td>
          <td class="text-{if $smarty.now < $tournament.deadline|strtotime}success{else}danger{/if}">{$tournament.deadline|date_format:"%d.%m.%Y"}</td>
          <td class="text-center">{if $tournament.link}<a href="{$tournament.link}" target="_blank">Link</a>{else}-{/if}</td>
          <td class="text-center">{$tournament.userCounter} <i class="fas fa-users"></i></td>
          <td>
            {if $tournament.openSubscription == 1 && $smarty.now < $tournament.deadline|strtotime}
                <a class="btn btn-success" href="?action=add_player&id={$tournament.tournamentID}">Eintragen</a></td>
            {else}
                <a class="btn btn-primary btn-block" href="?action=details&id={$tournament.tournamentID}">Details</a>
            {/if}
        </tr>
      {/foreach}
    </tbody>
  </table>
</div>

{if $isAdmin or $isReporter}
  <h2 class="display-2 mt-5">Alte Turniere</h2>
  <ul>
    {foreach item=tournament from=$oldTournamentList}
      <li><a href="?action=details&id={$tournament.tournamentID}">{$tournament.name}</a> in {$tournament.place} vom {$tournament.startdate|date_format:"%d.%m.%Y"} - {$tournament.enddate|date_format:"%d.%m.%Y"}</li>
    {/foreach}
  </ul>
{/if}



<script>
{literal}
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

        {/literal}

        {foreach item=tournament from=$tournamentList}
            features.push({ldelim}
              id:  '{$tournament.tournamentID}',
              name: '{$tournament.name}',
              place: '{$tournament.place}',
              start: '{$tournament.startdate}',
              end:   '{$tournament.enddate}',
              deadline: '{$tournament.deadline}',
              position: new google.maps.LatLng({$tournament.latitude}, {$tournament.longitude}),
              type: 'library',
            {rdelim});
        {/foreach}

        {literal}
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


{/literal}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt9xKhw9W6PJtpAn8CppDFFwPK_RjFetk&callback=initMap"></script>
