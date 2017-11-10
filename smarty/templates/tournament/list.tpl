<table class="table table-sm table-striped table-hover">
  <thead>
    <tr>
      <th>Name</th>
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
        <td>{$tournament.name}</td>

        <td>{$tournament.startdate|date_format:"%d.%m.%Y"}</td>
        <td>{$tournament.enddate|date_format:"%d.%m.%Y"}</td>
        <td>{$tournament.deadline|date_format:"%d.%m.%Y"}</td>
        <td class="text-center"><a href="{$tournament.link}" target="_blank">Link</a></td>
        <td><a href="?details={$tournament.tournamentID}">Details</a> - <a href="?add={$tournament.tournamentID}">Eintragen</a></td>
      </tr>
    {/foreach}
  </tbody>
</table>
