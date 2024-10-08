<!-- ADMIN CLUB LIST -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">Benutzerverwaltung</h1>
<p class="text-right">
    <a class="btn btn-success" href="/admin/user/add"><i class="fas fa-plus"></i> Benutzer hinzufügen</a>
</p>

{if isset($pagination)}
    {include file="_pagination.tpl"}
{/if}

<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table" data-pagination="false" data-search="true">
        <thead>
            <tr>
                <th data-sortable="true" data-field="firstName">Vorname</th>
                <th data-sortable="true" data-field="lastName">Nachname</th>
                <th>E-Mail</th>
                <th>Geschlecht</th>
                <th class="text-center">Reporter/Admin</th>
                <th class="text-center">Optionen</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=user from=$users}
            <tr>
                <td>{if not $user.email}<span data-toggle="tooltip" data-placement="top" title="Benutzer kann sicht ohne gültige E-Mail-Adresse nicht einloggen."><i class="text-danger fas fa-exclamation-triangle"></i></span> {/if}{$user.firstName}
                </td>
                <td>{$user.lastName}</td>
                <td>{$user.email|truncate:10:"...":true}</td>
                <td class="text-center">{if $user.gender == "Male"}<i class="fas fa-male"></i>{else}<i class="fas fa-female"></i>{/if}</td>
                <td class="text-center">
                    {if $user.isReporter}<i class="text-success far fa-check-circle"></i>{else}<i class="text-danger far fa-times-circle"></i>{/if} /
                    {if $user.isAdmin}<i class="text-success far fa-check-circle"></i>{else}<i class="text-danger far fa-times-circle"></i>{/if}
                </td>
                <td class="text-center"><a class="btn btn-info" href="/admin/user/update/{$user.userId}">Editieren</a> <a class="btn btn-danger" href="/admin/user/delete/{$user.userId}">Löschen</a></td>
            </tr>
            {foreachelse}
                <tr>
                    <td colspan="8">Failed to get all User from data base. Reason: {if $error} {$error} {/if}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

{if isset($pagination)}
    {include file="_pagination.tpl"}
{/if}

{include file="page_wrap_footer.tpl"}