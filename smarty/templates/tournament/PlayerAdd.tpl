<h2>Hinzufügen zum Turnier</h2>
{if $smarty.now > $tournament.deadline|strtotime}
  <p>Leider ist der Meldeschluss schon vorbei. Versuche es einfach beim nächsten mal!</p>
  <p class="text-right">
    <a class="btn btn-danger" href="?action=details&id={$tournament.tournamentID}">Zurück</a>
  </p>
{else}
  <form action="https://rl.weinekind.de/pages/rankingTournament.php?action=add&id={$tournament.tournamentID}" method="post">
    <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="Insert Players">
    Turnier: {$tournament.name}

    <h3>Spieler</h3>
    <div class="row initline">
      <div class="col-md-4">
        <label>Spieler</label>
        <select class="select-selectize" name="tournamentPlayerId[]" placeholder="Bitte wählen">
          <option value=""><br>
          {foreach item=player from=$players}
            <option value="{$player.userId}">{$player.fullName}
          {/foreach}
        </select>
      </div>

      <div class="col-md-4">
        <label>Partner</label>
        <select class="select-selectize" name="tournamentPartnerId[]" placeholder="Bitte wählen">
          <option value=""><br>
          {foreach item=player from=$players}
            <option value="{$player.userId}">{$player.fullName}<br>
          {/foreach}
        </select>
      </div>

      <div class="col-md-4">
        <label>Diziplin</label>
        <select class="select-selectize" name="tournamentDiziplin[]" placeholder="Bitte wählen">
          <option value=""><br>
          {foreach item=discipline from=$disciplines}
            <option value="{$discipline.classID}">{$discipline.name} {$discipline.modus}<br>
          {foreachelse}
            Leider keine Diziplinen<br>
          {/foreach}
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <a href="#" class="clonerow"><i class="glyphicon glyphicon-share-alt"></i> weiteren Spieler hinzufügen</a>
      </div>
    </div>

    <div class="row initline">
      <div class="col-md-6">
        <input type="submit" name="submit" class="btn btn-success btn-wide" value="Melden">
      </div>
      <div class="col-md-6 text-right">
        <a class="btn btn-danger" href="?action=details&id={$tournament.tournamentID}">Zurück</a>
      </div>
    </div>
    <!--
    <input type="submit" name="submitClose" class="btn btn-info btn-wide" value="Melden + Schließen ">
  -->
  </form>

{/if}
