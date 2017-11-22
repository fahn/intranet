<h2>{$user.firstName} {$user.lastName}</h2>

<p><strong>Geburtstag:</strong> {$user.bday|date_format:"d.m.Y"}</p>
{if isAdmin}
  <p><strong>E-Mail:</strong> {$user.email}</p>
  <p><strong>Telefon:</strong> {$user.phone}</p>
{/if}
<div class="row">
  {if $user.clubId == 1}
    <div class="col-md-6">
      <div class="panel panel-primary">
        <div class="panel-heading">Letzte interne Ranglistenspiele</div>
        <div class="panel-body">
          coming soon
        </div>
      </div>
    </div>
  {/if}
  <div class="col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">Letzte off. Turniere/Ranglisten</div>
      <div class="panel-body">
        <p><strong>Spielernummer:</strong> {$user.playerId}</p>
        <p><strong>Verein: </strong> {$club.name}</p>
        more coming soon
      </div>
    </div>
  </div>
</div>

<p class="text-right">
  <a class="btn btn-danger" href="javascript:history.back()">Zurück</a>
</p>
