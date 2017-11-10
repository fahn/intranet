<h2>{$tournament.name}</h2>

Ort: {$tournament.place}<br>
Zeitraum: {$tournament.startdate|date_format:"%d.%m.%Y"} - {$tournament.enddate|date_format:"%d.%m.%Y"}<br>
Meldeschluss: {$tournament.deadline|date_format:"%d.%m.%Y"}<br>
<hr>
<h3>Disziplinen</h3>
{foreach item=discipline from=$disciplines}
  {$discipline.name} {$discipline.modus}, 
{foreachelse}
  Leider keine Diziplinen<br>
{/foreach}
<p class="text-right">
  <a class="btn btn-info" href="?add={$tournament.tournamentID}">Melden</a>
  {if $isAdmin}
    <a class="btn btn-success" href="?export={$tournament.tournamentID}">Export als Excel-Datei</a>
  {/if}
</p>

<table class="table table-sm table-striped table-hover">
  <tr>
    <th>Spieler</th>
    <th>Disziplin</th>
    <th>Option</th>
  </tr>
  {foreach item=player from=$players}
    <tr>
      <td>{$player.playerName} {if $player.partnerName}// {$player.partnerName} {/if}</td>
      <td>{$player.disciplineName} {$player.disciplineModus}</td>
      <td>
        <a class="btn btn-danger" href="?delete={$tournament.tournamentID}&player={$player.playerID}">Abmelden</a>
      </td>
    </tr>
  {foreachelse}
    <tr>
      <td colspan="3">Keine Einträge</td>
    </tr>
  {/foreach}
</table>
