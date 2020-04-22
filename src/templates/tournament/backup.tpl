<h1 class="display-1">Sicherung</h1>
<div class="alert alert-danger">
    Arbeite noch daran.
</div>
{if $diff}
  <h2> Vergleich</h2>
  <pre>
  {$diff|print_r}
  <hr>
  {$diffResult|print_r}
{/if}

<p class="text-right">
  <a class="btn btn-danger" href="?action=create_backup&id={$smarty.get.id}">Erstellen</a>
</p>


<div class="table-responsive">
  <table id="myTable" class="table table-striped table-hover">
    <tr>
      <th colspan="3">Datum 12</th>
    </tr>
    {foreach item=line from=$backup}
      <tr>
        <td>Sicherung vom {$line.date|date_format:"d.m.Y H:i"} </td>
        {if !$smarty.foreach.line.first}
          <td><a href="?action=backup&id=36&detail={$line.backupId}">Vergleichen</a></td>
        {else}
          <td></td>
        {/if}
      </tr>
    {foreachelse}
      <tr>
        <td colspan="3" class="text-center">Keine Einträge vorhanden</td>
      </tr>
    {/foreach}
  </table>
</div>
