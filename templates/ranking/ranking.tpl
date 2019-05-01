<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="text-center" colspan="2">Spieler</th>
                <th>Punkte</th>
                <th>Serie</th>
                <th>Letztes Spiel</th>
            </tr>
        </thead>
        <tbody>
            {foreach key=rank item=item from=$ranking}
            <tr>
                <td class="text-center {if $rank == " 1"}text-color-gold{elseif $rank=="2" }text-color-silver{elseif $rank=="3" }text-color-bronce{/if}">{if $rank < 4}<i class="fas fa-trophy"></i>{/if} {$rank}</td>
                <td><a href="{$item.playerLink}">{$item.name}</a></td>
                <td>{$item.points}</td>
                <td>{$item.win}-{$item.loss}</td>
                <td>{$item.lastMatch|date_format:$dateFormat}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" class="text-center">keine Spiele bisher</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
