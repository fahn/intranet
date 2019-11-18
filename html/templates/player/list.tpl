<h1 class="display-1">Spieler</h1>

<p class="text-right">
    <a class="btn btn-success" href="?action=add_player">SpielerInnen hinzufügen</a>
</p>

<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table" data-pagination="true" data-search="true">
        <thead>
            <tr>
                <th data-sortable="true" data-field="firstName">Vorname</th>
                <th data-sortable="true" data-field="lastName">Nachname</th>
                <th data-sortable="true" data-field="playerNr">Spielernummer</th>
                <th data-sortable="true" data-field="club">Verein</th>
                <th>Gender</th>
                <th>Optionen</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=user from=$player}
            <tr>
                <td>{$user.firstName}</td>
                <td>{$user.lastName}</td>
                <td>{$user.playerNr}</td>
                <td>{$user.clubName}</td>
                <td class="text-center">{if $user.gender == "Male"}<i class="fas fa-male"></i>{else}<i class="fas fa-female"></i>{/if}</td>
                <td class="text-center">
                    <!--
                    <a class="btn btn-info" href="?action=edit&id={$user.playerId}">Editieren</a>
                    <a class="btn btn-danger" href="?action=delete&id={$user.playerId}">Löschen</a>
                    -->
                    coming soon
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td class="text-center" colspan="5">No Player available</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
