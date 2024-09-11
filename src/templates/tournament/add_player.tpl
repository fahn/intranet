<!-- STAFF ADMIN -->

{include file="page_wrap_header.tpl"}

<h2 class="display-3 mb-5">Meldung: {$tournament.name|default:''}</h2>
{if isset($tournament.openSubscription) && $tournament.openSubscription == 0}
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
        <li>Sollte ein Spieler <strong>nicht</strong> in dieser Liste vorkommen, bitte an den <a href="/support">Support</a> wenden. Wir tragen ihn nach!</li>
      </ul>
    </div>

    <h3 class="mt-5 mb-5">Spieler</h3>

    <div class="row initline mb-5">
      <div class="col-md-4 input-control" data-role="select">
        <label class="d-block font-weight-bold">Spieler</label>
        <select class="form-control js-data-ajax" type="text" id="tournamentPlayerId" data-item="player" name="tournamentPlayerId"></select>
      </div>

      <div class="col-md-4">
          <div class="form-group">
              <label class="d-block font-weight-bold">Partner</label>
              <select class="form-control js-data-ajax" type="text" id="tournamentPartnerId" name="tournamentPartnerId" data-item="player"></select>
          </div>
      </div>

      <div class="col-md-4">
        <label class="d-block font-weight-bold">Diziplin</label>
        <select class="js-example-basic-single form-control " name="tournamentDiziplin" placeholder="Bitte wählen">
          <option value="0">Bitte wählen</option>
          {foreach item=discipline from=$disciplines}
            <option value="{$discipline}">{$discipline}</option>
          {foreachelse}
            Leider keine Diziplinen<br>
          {/foreach}
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <input type="submit" name="submit" class="btn btn-success btn-wide" value="Melden">
      </div>
      <div class="col-md-6 text-right">
        <a class="btn btn-danger" href="?action=details&id={$tournament.tournamentId}">Zurück</a>
      </div>
    </div>
  </form>
{/if}


{include file="page_wrap_footer.tpl"}