<h1 class="display-2 mb-5">Vereine</h1>
<p class="text-right">
    <a class="btn btn-success" href="?action=add_club"><i class="fas fa-plus"></i> Club hinzufügen</a>
</p>

<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table" data-pagination="true" data-search="true">
        <thead>
            <tr>
                <th data-sortable="true" data-field="name">Name</th>
                <th data-sortable="true" data-field="clubNr">Vereinsnummer</th>
                <th>Verband</th>
                <th class="text-center">Option</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=club from=$clubs}
            <tr>
                <td>{$club.name}</td>
                <td>{$club.clubNr}</td>
                <td>{$club.association}</td>
                <td class="text-center"><a class="btn btn-info" href="?action=edit&id={$club.clubId}">Editieren</a> <a class="btn btn-danger" href="?action=delete&id={$club.clubId}">Löschen</a></td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="4" class="text-center">Failed to get all clubs.</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
