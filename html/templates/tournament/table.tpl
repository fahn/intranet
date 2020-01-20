<div class="table-responsive">
    <table id="myTable" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Altersklasse</th>
                <th>Name</th>
                <th>Ort</th>
                <th>Zeitraum</th>
                <th>Meldeschluss</th>
                <th>Links</th>
                <th>Teilnehmer</th>
                <th>Optionen</th>
            </tr>
        </thead>
        <tbody>
            {if isset($data) && count($data) > 1}
                {foreach item=tournament from=$data}
                <tr>
                    <td>{$tournament.classification}</td>
                    <td><a class="text-{if $smarty.now < $tournament.deadline|strtotime}success{else}danger{/if}" href="?action=details&id={$tournament.tournamentId}">{$tournament.name}</a></td>
                    <td>{$tournament.place}</td>
                    <td>
                        {if $tournament.startdate|date_format:"d.m.y" == $tournament.enddate|date_format:"d.m.y"}
                            {$tournament.startdate|date_format:"%d.%m.%Y"}
                        {else}
                            {$tournament.startdate|date_format:"d.m.y"} - {$tournament.enddate|date_format:"%d.%m.%Y"}
                        {/if}

                    </td>
                    <td class="text-{if $smarty.now < $tournament.deadline|strtotime}success{else}danger{/if}">{$tournament.deadline|date_format:"%d.%m.%Y"}</td>
                    <td class="text-center">
                        {if $tournament.link}<a href="{$tournament.link}" target="_blank" title="Download Ausschreibung"><i class="fas fa-file-pdf"></i></a> {/if}
                        {if isset($tournament.calLink)}<a href="{$tournament.calLink}" target="_blank" title="Als Termin zum Kalendar hinzufügen"><i class="fas fa-calendar-alt"></i></a>{/if}
                    </td>
                    <td class="text-center">{if isset($tournament.userCounter)}{$tournament.userCounter} <i class="fas fa-users"></i>{/if}</td>
                    <td>
                    {if $tournament.openSubscription == 1 && $smarty.now < $tournament.deadline|strtotime} 
                        <a class="btn btn-success" href="?action=add_player&id={$tournament.tournamentId}">Eintragen</a></td>
                    {else}
                        <a class="btn btn-primary btn-block" href="?action=details&id={$tournament.tournamentId}">Details</a>
                    {/if}
                </tr>
                {/foreach}
            {else}
                <tr><td colspan="8" class="text-center">Keine Einträge</td></tr>
            {/if}
        </tbody>
    </table>
</div>
