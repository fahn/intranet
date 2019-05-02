<h1 class="display-1">Benutzerverwaltung</h1>
<div class="alert alert-info">
    <p>Hier werden <strong>alle</strong> Spieler nach dem Nachnamen aufgelistet. Egal, ob Sie vom BC Comet sind  oder von anderen Vereinen.<br></p>
 </div>

<p class="text-right">
  <a class="btn btn-success" href="?action=add_player">Benutzer hinzufügen</a>
</p>

{if $pagination}
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
          <td>{if not $user.email}<span data-toggle="tooltip" data-placement="top" title="Benutzer kann sicht ohne gültige E-Mail-Adresse nicht einloggen."><i class="text-danger fas fa-exclamation-triangle" ></i></span> {/if}{$user.firstName}</td>
          <td>{$user.lastName}</td>
          <td>{$user.email|truncate:10:"...":true}</td>
          <td class="text-center">{if $user.gender == "Male"}<i class="fas fa-male"></i>{else}<i class="fas fa-female"></i>{/if}</td>
          <td class="text-center">
              {if $user.isReporter}<i class="text-success far fa-check-circle"></i>{else}<i class="text-danger far fa-times-circle"></i>{/if} /
              {if $user.isAdmin}<i class="text-success far fa-check-circle"></i>{else}<i class="text-danger far fa-times-circle"></i>{/if}
          </td>
          <td class="text-center"><a class="btn btn-info" href="?action=edit&id={$user.userId}">Editieren</a> <a class="btn btn-danger" href="?action=delete&id={$user.userId}">Löschen</a></td>
        </tr>
      {foreachelse}
        <tr>
          <td colspan="8">Failed to get all User from data base. Reason: {if $error} {$error} {/if}</td>
        </tr>
      {/foreach}
    </tbody>
  </table>
</div>

{if $pagination}
  {include file="_pagination.tpl"}
{/if}
