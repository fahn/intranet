<h1 class="display-1">Spieler</h1>

<p class="text-right">
    <a class="btn btn-success" href="?action=add_player">SpielerInnen hinzufügen</a>
    <a class="btn btn-danger" href="?action=sync">Sync</a>
</p>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Spielernummer</th>
                <th>Verein</th>
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
                <td>{$user.gender}</td>
                <td class="text-center">
                    <a class="btn btn-info" href="?action=edit&id={$user.userId}">Editieren</a>
                    <a class="btn btn-danger" href="?action=delete&id={$user.userId}">Löschen</a>
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
