<h2>{$tournament.name}</h2>

<div class="row">
  <div class="col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">Informationen</div>
      <div class="panel-body">
        <p><strong>Ort:</strong> {$tournament.place}</p>
        <p><strong>Zeitraum:</strong> {$tournament.startdate|date_format:"%d.%m.%Y"} - {$tournament.enddate|date_format:"%d.%m.%Y"}</p>
        <p><strong>Meldeschluss:</strong> <span class="text-{if $tournament.deadline|strtotime < $smarty.now}danger{else}success{/if}">{$tournament.deadline|date_format:"%d.%m.%Y"}</span></p>
        <p><strong>Ausschreibung:</strong> <a href="{$tournament.link}" target="_blank">Link zur Ausschreibung</a></p>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">Disziplinen</div>
      <div class="panel-body">
        {foreach item=discipline from=$disciplines}
          {$discipline.name} {$discipline.modus},
        {foreachelse}
          Leider keine Diziplinen<br>
        {/foreach}
      </div>
    </div>
  </div>
</div>

<p class="text-right">
  {if $smarty.now < $tournament.deadline|strtotime}
    <a class="btn btn-info" href="?action=add&id={$tournament.tournamentID}">Melden</a>
  {/if}
  {if $isAdmin}
    <a class="btn btn-success" href="?export={$tournament.tournamentID}">Export als Excel-Datei</a>
  {/if}
</p>
<div class="table-responsive">
  <table class="table table-sm table-striped table-hover">
    <tr>
      <th>Spieler</th>
      <th>Disziplin</th>
      <th>Option</th>
    </tr>
    {foreach item=player from=$players}
      {if $player.visible == 1}
        <tr>
          <td><a href="/pages/user.php?id={$player.playerID}">{$player.playerName}</a> {if $player.partnerName}// <a href="/pages/user.php?id={$player.partnerID}">{$player.partnerName}</a> {/if}</td>
          <td>{$player.disciplineName} {$player.disciplineModus}</td>
          <td>
            {if isAdmin or isReporter}
              <a class="btn btn-danger" href="?action=deletePlayer&id={$tournament.tournamentID}&tournamentPlayerId={$player.tournamentPlayerId}">Abmelden</a>
            {/if}
          </td>
        </tr>
      {/if}
    {foreachelse}
      <tr>
        <td colspan="3" class="text-center">Keine Einträge</td>
      </tr>
    {/foreach}
  </table>
</div>
