<!-- ADMIN CLUB UPDATE -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">Verein {if $action == 'edit'}Editieren{else}hinzufügen{/if}</h1>

<form action="" method="post">
  <input type="hidden" name="clubFormAction" id="clubFormAction" value="{if $action == 'edit'}Update Club{else}Insert Club{/if}">
  <input type="hidden" name="clubClubId" id="clubClubId" value="{if $variable['clubId']}{$variable['clubId']}{/if}">

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for = "clubName">Name des Clubs:</label>
        <input class="form-control"  type="text" id="clubName" name="clubName" placeholder="BC Comet Braunschweig" value="{$variable['name']|default:''}" required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for = "clubClubNumber">Vereinsnummer</label>
        <input class="form-control"  type="text" id="clubClubNumber" name="clubClubNumber" placeholder="100000" value="{$variable['clubNumber']|default:''}" required>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for = "clubAssociation">Verband</label>
        <input class="form-control"  type="text" id="clubAssociation" name="clubAssociation" placeholder="BS-BS" value="{$variable['association']|default:''}" required>
      </div>
    </div>
  </div>


  <div class="row initline mt-5">
    <div class="col-md-6">
      <input type="submit" name="submit" class="btn btn-success btn-wide" value="{if $action == 'edit'}Editieren{else}Hinzufügen{/if}">
    </div>
    <div class="col-md-6 text-right">
      <a class="btn btn-danger" href="/pages/adminAllClub.php">Zurück</a>
    </div>
  </div>
  <!--
  <input type="submit" name="submitClose" class="btn btn-info btn-wide" value="Melden + Schließen ">
-->
</form>

{include file="page_wrap_footer.tpl"}