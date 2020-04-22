<h2 class="display-3 mb-5">{$tournament.name}</h2>

<div class="row equal">
    <div class="col-md-6 align-items-stretch">
        <div class="card">
            <h5 class="card-header">Informationen</h5>
            <div class="card-body">
                <p><strong>Ort:</strong> {$tournament.place}</p>
                <p><strong>Zeitraum:</strong>
                    {$tournament.startdate|date_format:"%d.%m.%Y %H:%M"}
                    {if $tournament.startdate|date_format:"d.m.y" != $tournament.enddate|date_format:"d.m.y"}
                        - {$tournament.enddate|date_format:"%d.%m.%Y"}
                    {/if}
                    <a href="?action=calendar&id={$tournament.tournamentId}" title="add to calendar"><i class="fas fa-calendar-check"></i></a>
                </p>
                <p><strong>Meldeschluss:</strong> <span class="text-{if $tournament.deadline|strtotime < $smarty.now}danger{else}success{/if}">{$tournament.deadline|date_format:"%d.%m.%Y"}</span></p>
                <p><strong>Ausschreibung:</strong> {if $tournament.link}<a href="{$tournament.link}" target="_blank">Link zur Ausschreibung</a>{else}-{/if}</p>
                <p><strong>Melder:</strong> <a href="/pages/user.php?id={$tournament.reporterId}">{$tournament.reporterName}</a><br>
                    {if $tournament.classification}
                    <p><strong>Altersklassen:</strong> {$tournament.classification}
                        {if $tournament.additionalClassification}{$tournament.additionalClassification|implode:","}{/if}</p>
                    {/if}
                    {if $tournament.discipline}
                    <p><strong>Disziplinen:</strong> {$tournament.discipline}</p>
                    {/if}
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3  align-items-stretch">
        <div class="card">
            <h5 class="card-header">Anfahrt</h5>
            <div class="card-body">
                <iframe style="width: 100%" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCDYXGM6sJVeOvkbn6I2uvihQfs4BVQy0k
          &q={$tournament.place}&zoom=9 " allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>

{if $tournament.description}
    <div class="row">
        <div class="col-md-12 align-items-stretch">
            <div class="card">
                <h5 class="card-header">Beschreibung</h5>
                <div class="card-body">{$tournament.description|unescape:'html'}</div>
            </div>
        </div>
    </div>
{/if}

<div class="d-flex flex-row-reverse">
    {if $isAdmin OR $isReporter}
    <div class="p-2">
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Optionen</button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="?action=export&id={$tournament.tournamentId}"><i class="fas fa-bullhorn"></i> Meldung</a>
                <a class="dropdown-item" href="?action=backup&id={$tournament.tournamentId}"><i class="fas fa-cloud"></i> Sicherungen</a>
                <a class="dropdown-item" href="?action=lock&id={$tournament.tournamentId}{$link.linkUnlockAll}"><i class="fas fa-lock"></i> Alle gemeldet</a>
                <a class="dropdown-item" href="?action=unlock&id={$tournament.tournamentId}{$link.linkLockAll}"><i class="fas fa-lock-open"></i> Alle ungemeldet</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="?action=edit_tournament&id={$tournament.tournamentId}"><i class="fas fa-edit"></i> Turnier bearbeiten</a>
            </div>
        </div>
    </div>
    {/if}

    {if $tournament.openSubscription == 1 && $smarty.now < $tournament.deadline|strtotime}
    <div class="p-2">
        <a class="btn btn-success" href="?action=add_player&id={$tournament.tournamentId}">Spieler melden</a>
    </div>
    {/if}
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table" data-pagination="true" data-search="true">
        <thead>
            <tr>
                <th data-sortable="true" data-field="player">Spieler</th>
                <th data-sortable="true" data-field="classification">Disziplin</th>
                <th>Melder</th>
                <th class="text-center">Option</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=player from=$players}
            {if $player.visible == 1}
            <tr>
                <td>
                    {if $player.locked == 1}
                        <i class="fas fa-lock" data-toggle="tooltip" data-placement="bottom" title="Spieler wurde bereits gemeldet"></i>
                    {/if}
                    <a href="{$player.linkPlayer}" title="Profil von {$player.playerName}">{$player.playerName}</a>
                    {if isset($player.partnerId)}//
                    {if $player.partnerName == 'FREI'}
                        <span class="text-danger font-weight-bold">{$player.partnerName}</span>
                    {else}
                        <a href="{$player.partnerLink}" title="Profil von {$player.partnerName}">{$player.partnerName}</a>
                    {/if}
                    {/if}
                </td>
                <td>{$player.classification}</td>
                <td><a href="{$player.linkReporter}" title="gemeldet von {$player.reporterName} am {$player.fillingDate|date_format:" d.m.Y H:i"}">{$player.reporterName}</a> ({$player.fillingDate|date_format:"d.m.Y"})</td>
                <td class="text-center">
                    {if $isAdmin or $isReporter}
                        {if $player.locked == 1}
                            <a class="btn btn-danger" href="{$player.linkUnlock}" data-toggle="tooltip" data-placement="bottom" title="Spieler noch nicht als gemeldet setzen"><i class="fas fa-lock-open"></i></a>
                        {else}
                            <a class="btn btn-danger" href="{$player.linkLock}" data-toggle="tooltip" data-placement="bottom" title="Spieler als gemeldet setzen"><i class="fas fa-lock"></i></a>
                        {/if}
                    {/if}
                    {if $isAdmin or $isReporter or $player.playerNr == $userPlayerId or $player.partnerNr == $userPlayerId}
                        <a class="btn btn-danger" href="{$player.linkDelete}" title="Spieler abmelden" onclick="return confirm('Möchtest du wirklich den Spieler abmelden ?');"><i class="fas fa-window-close"></i> <span>Abmelden</span></a>
                    {/if}
                </td>
            </tr>
            {/if}
            {foreachelse}
            <tr>
                <td colspan="4" class="text-center font-weight-bold">Keine Einträge</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
