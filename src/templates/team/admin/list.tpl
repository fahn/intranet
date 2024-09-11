<!-- STAFF ADMIN -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">Staff</h1>
<p class="text-right">
    <a class="btn btn-success" href="/admin/staff/add"><i class="fas fa-plus"></i> Staff hinzufügen</a>
</p>
<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table" data-pagination="false" data-search="true">
        <thead>
            <tr>
                <th data-sortable="true" data-field="firstName">Name</th>
                <th class="text-center">Reihe / Spalte</th>
                <th class="text-center">Optionen</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=user from=$staff}
            <tr>
                <td>{$user.name}</td>
                <td>{$user.row} / {$user.position}</td>
                <td class="text-center">
                    <a class="btn btn-info" href="/admin/staff/update/{$user.staffId}"><i class="fas fa-pencil-alt"></i> Editieren</a>
                    <a class="btn btn-danger" href="/admin/staff/delete/{$user.staffId}"><i class="fas fa-trash-alt"></i> Löschen</a>
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="3" class="text-center">Failed to get Staff from data base. {if $error}Reason: {$error} {/if}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>

{include file="page_wrap_footer.tpl"}