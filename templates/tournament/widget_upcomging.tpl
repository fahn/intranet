<div class="card">
    <h5 class="card-header">Kommende 5 Turniere</h5>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Altersklasse</th>
                    <th>Name</th>
                    <th>Datum</th>
                    <th>Ort</th>
                </tr>
                {foreach item=tournament from=$data}
                <tr>
                    <td>{$tournament.classification}</td>
                    <td><a {if $tournament.deadline|strtotime < $smarty.now}class="text-danger"{else}class="text-success"{/if} href="{$tournament.linkTo}" title="{$tournament.name}: vom {$tournament.startdate|date_format:"d.m.y"} - {$tournament.enddate|date_format:'d.m.y'}">{$tournament.name}</a></td>
                    <td {if $tournament.deadline|strtotime < $smarty.now}class="text-danger"{else} class="text-success"{/if}>{if $tournament.startdate == $tournament.enddate}{$tournament.startdate|date_format:"d.m.y"}{else}{$tournament.startdate|date_format:"d.m.y"} - {$tournament.enddate|date_format:"d.m.y"}{/if}</td>
                    <td>{$tournament.place}</td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="4">Leider keine Turniere in der kommenden Zeit.</td>
                </tr>
                {/foreach}
            </table>
        </div>
        <hr>
        <a href="/pages/rankingTournament.php" title="Alle Turniere"><i class="fas fa-list-ul"></i> alle Turniere</a>
    </div>
</div>
