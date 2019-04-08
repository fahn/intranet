{if $print}
    <div class="page_break"></div>
{/if}

<table class="table">
  <tr>
    <th>Spieler</th>
    <th>Gegner</th>
    <th>Sätze</th>
    <th>Datum</th>
    {if ! $print}
        <th class="text-center">Optionen</th>
    {/if}
  </tr>
{foreach item=item from=$games}
  <tr>
    <td><a href="{$item.playerLink}">{$item.playerName}</a></td>
    <td><a href="{$item.opponentLink}">{$item.opponentName}</a></td>
    <td>{$item.sets}</td>
    <td>{$item.time|date_format:$dateFormat}</td>
    {if ! $print}
        <td class="text-center"><a href="{$item.deleteLink}"><i class="fas fa-trash-alt"></i></a></td>
    {/if}
  </tr>
  {foreachelse}
      <tr>
          <td colspan="5" class="text-center">keine Spiele bisher</td>
      </tr>
  {/foreach}
</table>
