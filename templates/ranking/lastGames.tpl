{if $print}
<div class="page_break"></div>
{/if}

<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table">
        <thead>
            <tr>
                <th data-sortable="true" data-field="player">Spieler</th>
                <th data-sortable="true" data-field="opponent">Gegner</th>
                <th>Sätze</th>
                <th data-sortable="true" data-field="date">Datum</th>
                <th class="d-print-none text-center">Optionen</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=item from=$games}
            <tr>
                <td><a href="{$item.playerLink}">{$item.playerName}</a></td>
                <td><a href="{$item.opponentLink}">{$item.opponentName}</a></td>
                <td>{$item.sets}</td>
                <td>{$item.time|date_format:$dateFormat}</td>
                <td class="d-print-none text-center"><a href="{$item.deleteLink}"><i class="fas fa-trash-alt"></i></a></td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" class="text-center">keine Spiele bisher</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
