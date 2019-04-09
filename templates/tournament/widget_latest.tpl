<div class="table-responsive">
  <table class="table table-striped table-hover">
    {foreach item=tn from=$tournament}
      <tr>
        <td><a href="/pages/rankingTournament.php?action=details&id={$tn.tournamentId}">{$tn.name}</td>
        <td>{$tn.startdate|date_format:"d.m.y"} - {$tn.enddate|date_format:"d.m.y"} </td>
      </tr>
    {/foreach}
  </table>
</div>
