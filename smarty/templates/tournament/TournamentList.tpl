{if $isAdmin}
  <p class="text-right">
    <a class="btn btn-success" href="?action=add_torunament">Turnier hinzufügen</a>
  </p>
{/if}
<h3>Turniere/Ranglisten</h3>
<div class="table-responsive">
<table class="table table-sm table-striped table-hover">
  <thead>
    <tr>
      <th>Name</th>
      <th>Ort</th>
      <th>Begin</th>
      <th>Ende</th>
      <th>Meldeschluss</th>
      <th>Ausschreibung</th>
      <th>Optionen</th>
    </tr>
  </thead>
  <tbody>
    {foreach item=tournament from=$list}
      <tr>
        <td><a href="?action=details&id={$tournament.tournamentID}">{$tournament.name}</a></td>
        <td>{$tournament.place}</td>
        <td>{$tournament.startdate|date_format:"%d.%m.%Y"}</td>
        <td>{$tournament.enddate|date_format:"%d.%m.%Y"}</td>
        <td class="text-{if $smarty.now < $tournament.deadline|strtotime}success{else}danger{/if}">{$tournament.deadline|date_format:"%d.%m.%Y"}</td>
        <td class="text-center">{if $tournament.link}<a href="{$tournament.link}" target="_blank">Link</a>{else}-{/if}</td>
        <td>
          {if $smarty.now < $tournament.deadline|strtotime}
            <a class="btn btn-success" href="?action=add&id={$tournament.tournamentID}">Eintragen</a></td>
          {/if}
      </tr>
    {/foreach}
  </tbody>
</table>
</div>
