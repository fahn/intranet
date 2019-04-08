<h2 class="display-4 mb-5">Meldung<br> {$tournament.name}</h2>
{if $tournament.openSubscription == 0}
  <div class="alert alert-warning">
    <h3>Information</h3>
    <p>Leider kann man sich noch nicht anmelden</p>
  </div>

{else if $smarty.now > $tournament.deadline|strtotime}
  <p>Leider ist der Meldeschluss schon vorbei. Versuche es einfach beim nächsten mal!</p>
  <p class="text-right">
    <a class="btn btn-danger" href="?action=details&id={$tournament.tournamentId}">Zurück</a>
  </p>
{else}
  <form action="" method="post">
    <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="Insert Players">


    <div class="alert alert-info">
      <h5>Information</h5>
      <ul>
        <li>Beim gemischten Doppel immer den Herren als Erstes melden. Sonst klappt diese nicht!</li>
        <li>Sollte ein Spieler <strong>nicht</strong> in dieser Liste vorkommen, bitte an den <a href="{$linkToSupport}">Support</a> wenden. Wir tragen ihn nach!</li>
      </ul>
    </div>

    <h3 class="mt-5 mb-5">Spieler</h3>

    <div class="row initline">
      <div class="col-md-4 input-control" data-role="select">
        <label class="d-block font-weight-bold">Spieler</label>
        <select class="js-example-data-array form-control d-block" name="tournamentPlayerId[]" placeholder="Bitte wählen">
        </select>
      </div>

      <div class="col-md-4">
          <div class="form-group">
              <label class="d-block font-weight-bold">Partner</label>
              <select class="js-example-data-array form-control d-block" name="tournamentPartnerId[]" placeholder="Bitte wählen">
                <option value="0">Bitte wählen</option>
              </select>
          </div>
      </div>

      <div class="col-md-4">
        <label class="d-block font-weight-bold">Diziplin</label>
        <select class="js-example-basic-single form-control " name="tournamentDiziplin[]" placeholder="Bitte wählen">
          <option value="0">Bitte wählen</option>
          {foreach item=discipline from=$disciplines}
            <option value="{$discipline}">{$discipline}</option>
          {foreachelse}
            Leider keine Diziplinen<br>
          {/foreach}
        </select>
      </div>
    </div>

    <div id="containerClone"></div>

    <div class="row mt-5 mb-5" >
      <div class="col-md-12 text-center">
        <a href="#" class="clonerow"><i class="glyphicon glyphicon-share-alt"></i> weiteren Spieler hinzufügen</a>
      </div>
    </div>

    <p></p>

    <div class="row">
      <div class="col-md-6">
        <input type="submit" name="submit" class="btn btn-success btn-wide" value="Melden">
      </div>
      <div class="col-md-6 text-right">
        <a class="btn btn-danger" href="?action=details&id={$tournament.tournamentId}">Zurück</a>
      </div>
    </div>
    <!--
    <input type="submit" name="submitClose" class="btn btn-info btn-wide" value="Melden + Schließen ">
  -->
  </form>

{/if}

<script>
{literal}

  var data = [{id: 0, text: 'Bitte wählen'}];

  //data.push({id: 1, text: 'da'});
{/literal}
{foreach item=club from=$clubs}
  data.push({ldelim}
    "text": "{$club.name}",
    "children": [
  {foreach item=player from=$club.players}
    {ldelim}
      id: {$player.userId},
      text: '{$player.fullName}'
    {rdelim},
  {/foreach}
  ]{rdelim});
{/foreach}
</script>
