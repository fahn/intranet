<h1 class="display-1 mb-5">Turniere / Ranglisten</h1>

<ul class="nav nav-tabs" style="margin-bottom: 20px;">
  <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#home">Aktuelle</a>
  </li>
  <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#calendar">Kalender</a>
  </li>
  <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#old">Alte</a>
  </li>
</ul>

<div class="tab-content">
  <div id="home" class="tab-pane container active">
    {if isset($tournamentList) && $tournamentList|count > 0}
        <div id="map" class="mb-5" style="width: 100%; height: 400px !important"></div>
    {/if}


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
    {include file="tournament/table.tpl" data=$tournamentList}


  </div>
  
  <div id="calendar" class="tab-pane container">
      <h2 class="display-2 mt-5">Kalender</h2>
      {include file="tournament/calendar.tpl" data=$calendar}
  </div>
  
  <div id="old" class="tab-pane container">
      <h2 class="display-2 mt-5">Alte Turniere</h2>
      {include file="tournament/table.tpl" data=$oldTournamentList}
  </div>
</div>

{if isset($googleMaps.key) && $googleMaps.key|count_characters > 0}
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
            icon: iconBase + 'home.png',

          }
        };

        var features = [
          {
            name: '{/literal}{$googleMaps.HomeMarkerName}{literal}',
            place: '{/literal}{$googleMaps.HomeMarkerPlace}{literal}',
            position: new google.maps.LatLng({/literal}{$googleMaps.HomeMarkerLat}{literal}, {/literal}{$googleMaps.HomeMarkerLng}{literal}),
            type: 'info'
          },
        ];

        {/literal}

        {foreach item=tournament from=$tournamentList}
            features.push({ldelim}
              id:  '{$tournament.tournamentId}',
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
          '<p><a href="/pages/rankingTournament.php?action=details&id='+ feature.id +'">Details</a></p>'+
          '</div>'+
          '</div>';

          google.maps.event.addListener(marker, 'click', function(){
                  if (feature.id) {
                    content = "<div id='infowindow'><a href='pages/rankingTournament.php?action=details&id="+ feature.id +"'>"+ feature.name +" in "+ feature.place +"</a><br></div>";
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

  <script async defer src="https://maps.googleapis.com/maps/api/js?key={$googleMaps.key}&callback=initMap"></script>
{/if}
