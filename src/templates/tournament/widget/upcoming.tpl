<div class="card">
    <h5 class="card-header"><i class="fas fa-trophy"></i> Kommende 5 Turniere</h5>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <tr>
                    <th>Altersklasse</th>
                    <th>Name</th>
                    <th>Datum</th>
                    <th>Ort</th>
                    <th>TN</th>
                </tr>
                {foreach item=tournament from=$data}
                <tr>
                    <td>{$tournament.classification}</td>
                    <td><a {if $tournament.deadline|strtotime < $smarty.now}class="text-danger" {else}class="text-success" {/if} href="{$tournament.linkTo}" title="{$tournament.name}: vom {$tournament.startdate|date_format:" d.m.y"} -
                            {$tournament.enddate|date_format:'d.m.y'}">{$tournament.name|default:''}</a></td>
                    <td {if $tournament.deadline|strtotime < $smarty.now}class="text-danger" {else} class="text-success" {/if}>
                        {if $tournament.startdate|date_format:"d.m.y" == $tournament.enddate|date_format:"d.m.y"}
                            {$tournament.startdate|date_format:"d.m.y"}
                        {else}
                            {$tournament.startdate|date_format:"d.m.y"} - {$tournament.enddate|date_format:"d.m.y"}
                        {/if}
                    </td> <td>{$tournament.place}</td>
                    <td>{if isset($tournament.participant) && $tournament.participant > 0}<span class="badge badge-success" data-toggle="tooltip" data-placement="bottom" title="{$tournament.participant} Teilnehmer">{$tournament.participant}</span>{else}-{/if}</td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="5">Leider keine Turniere in der kommenden Zeit.</td>
                </tr>
                {/foreach}
            </table>
        </div>
        <hr>
        {if isset($linkToTournament)}
            <a href="{$linkToTournament}" title="Alle Turniere"><i class="fas fa-list-ul"></i> alle Turniere</a>
        {/if}
    </div>
</div>
