<h2 class="display-3 mb-5">{$tournament.name}</h2>

<div class="row equal">
  <div class="col-md-6 align-items-stretch">
    <div class="card">
        <h5 class="card-header">Informationen</h5>
        <div class="card-body">
        <p><strong>Ort:</strong> {$tournament.place}</p>
        <p><strong>Zeitraum:</strong> {$tournament.startdate|date_format:"%d.%m.%Y"} - {$tournament.enddate|date_format:"%d.%m.%Y"}</p>
        <p><strong>Meldeschluss:</strong> <span class="text-{if $tournament.deadline|strtotime < $smarty.now}danger{else}success{/if}">{$tournament.deadline|date_format:"%d.%m.%Y"}</span></p>
        <p><strong>Ausschreibung:</strong> {if $tournament.link}<a href="{$tournament.link}" target="_blank">Link zur Ausschreibung</a>{else}-{/if}</p>
        <p><strong>Melder:</strong> <a href="https://int.bc-comet.de/pages/user.php?id={$tournament.reporterId}">{$tournament.reporterName}</a><br>
        {if $tournament.classification}
          <p><strong>Altersklassen:</strong> {$tournament.classification}
          {if $tournament.additionalClassification}{$tournament.additionalClassification|implode:","}{/if}</p>
        {/if}
        {if $tournament.discipline}
        <p><strong>Disziplinen:</strong> {$tournament.discipline}</p>
        {/if}
      </div>
    </div>
  </div>

  <div class="col-md-6 mb-3  align-items-stretch">
    <div class="card">
        <h5 class="card-header">Anfahrt</h5>
      <div class="card-body">
        <iframe
        style="width: 100%"
        height="300"
        frameborder="0" style="border:0"
        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCDYXGM6sJVeOvkbn6I2uvihQfs4BVQy0k
          &q={$tournament.place}&zoom=9 " allowfullscreen>
        </iframe>
      </div>
    </div>
  </div>
</div>

{if $tournament.description}
  <div class="row">
    <div class="col-md-12 align-items-stretch">
      <div class="card">
          <h5 class="card-header">Beschreibung</h5>
          <div class="card-body">{$tournament.description|unescape:'html'}</div>
      </div>
    </div>
  </div>
{/if}

<div class="d-flex flex-row-reverse">
    {if $isAdmin OR $isReporter}
        <div class="p-2">
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Optionen</button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="?action=export&id={$tournament.tournamentID}"><i class="fas fa-bullhorn"></i> Meldung</a>
                <a class="dropdown-item" href="?action=backup&id={$tournament.tournamentID}"><i class="fas fa-cloud"></i> Sicherungen</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="?action=edit_tournament&id={$tournament.tournamentID}"><i class="fas fa-edit"></i> Turnier bearbeiten</a>
            </div>
        </div>
        </div>
    {/if}

    {if $tournament.openSubscription == 1 && $smarty.now < $tournament.deadline|strtotime}
        <div class="p-2">
            <a class="btn btn-success" href="?action=add_player&id={$tournament.tournamentID}">Spieler melden</a>
        </div>
    {/if}
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover">
    <tr>
      <th>Spieler</th>
      <th>Disziplin</th>
      {if $isAdmin || $isReporter}
      <th>Melder</th>
      {/if}
        <th class="text-center">Option</th>
    </tr>
    {foreach item=player from=$players}
      {if $player.visible == 1}
        <tr>
          <td><a href="/pages/user.php?id={$player.playerID}" title="Profil von {$player.playerName}">{$player.playerName}</a> {if $player.partnerName}// {if $player.partnerName == 'FREI'}<span class="text-danger font-weight-bold">{$player.partnerName}</span> {else} <a href="/pages/user.php?id={$player.partnerID}" title="Profil von {$player.partnerName}">{$player.partnerName}</a>{/if}{/if}</td>
          <td>{$player.classification}</td>
          {if $isAdmin || $isReporter}
            <td><a href="/pages/user.php?id={$player.reporterID}" title="gemeldet von {$player.reporterName} am {$player.fillingDate|date_format:"d.m.Y H:i"}">{$player.reporterName}</a> ({$player.fillingDate|date_format:"d.m.Y"})</td>
          {/if}
          <td class="text-center">
            {if $isAdmin or $isReporter or $player.playerID == $userId or $player.partnerId == $userId}
              <a class="btn btn-danger" href="?action=deletePlayer&id={$tournament.tournamentID}&tournamentPlayerId={$player.tournamentPlayerId}" onclick="return confirm('Möchtest du wirklich den Spieler abmelden ?');">Abmelden</a>
            {/if}
          </td>
        </tr>
      {/if}
    {foreachelse}
      <tr>
        <td colspan="4" class="text-center font-weight-bold">Keine Einträge</td>
      </tr>
    {/foreach}
  </table>
</div>
