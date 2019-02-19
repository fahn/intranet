<h2 class="display-1 mb-5">Turnier {if $task == "add"}hinzufügen{else}editieren{/if}</h2>

{if $task == "edit" && $vars.visible == 0}
  <div class="alert alert-danger">
  <strong>Achtung</strong> Das Turnier wurde gelöscht
</div>
{/if}

<form action="" method="post">
  <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="{$hidden}">
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentName">Name des Turniers:</label>
        <input class="form-control"  type="text" id="tournamentName" name="tournamentName" placeholder="B-/C-Rangliste" value="{$vars['name']}" required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentPlace">Ort:</label>
        <input class="form-control"  type="text" id="tournamentPlace" name="tournamentPlace" placeholder="Braunschweig" value="{$vars['place']}"required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <label for="tournamentStartdate">Start-Datum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentStartdate" name="tournamentStartdate" placeholder="dd.mm.YYYY" value="{$vars['startdate']|date_format:"d.m.Y"}" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <label for="tournamentEnddate">Enddatum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentEnddate" name="tournamentEnddate" placeholder="dd.mm.YYYY" value="{$vars['enddate']|date_format:"d.m.Y"}" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <label for="tournamentDeadline">Meldeschluss:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentDeadline" name="tournamentDeadline" placeholder="dd.mm.YYYY" value="{$vars['deadline']|date_format:"d.m.Y"}" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentLink">Link zur Ausschreibung:</label>
        <input class="form-control"  type="text" id="tournamentLink" name="tournamentLink" placeholder="http://" value="{$vars['link']}">
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="tournamentClassification">Altersklasse:</label>
        <select multiple="multiple" class="form-control js-example-basic-single" name="tournamentClassification[]" id="tournamentClassification" autocomplete="off">
            {foreach item=classification from=$classificationArr}
                <option value="{$classification}" {if isset($vars.classification) && $classification|in_array:$vars.classification}selected{/if}>{$classification}</option>
            {/foreach}
        </select>
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label for="tournamentAdditionalClassification">Weitere:</label>
        <input class="form-control"  type="text" id="tournamentAdditionalClassification" name="tournamentAdditionalClassification" placeholder="A,B,C" value="{$vars['additionalClassification']}">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="tournamentDiscipline">Diziplinen:</label>
        <select multiple="multiple" class="form-control js-example-basic-single" name="tournamentDiscipline[]" id="tournamentDiscipline" autocomplete="off">
            {foreach item=discipline from=$disciplineArr}
                <option value="{$discipline}" {if isset($vars.discipline) && $discipline|in_array:$vars.discipline}selected{/if}>{$discipline}</option>
            {/foreach}
        </select>
      </div>
    </div>
  </div>

  <h3 class="mt-5">Benachrichtigung</h3>
  <div class="row">
    <div class="col-md-6">
      <label for="tournamentReporterId">Reporter:</label>
      <select class="form-control js-example-basic-single" name="tournamentReporterId" autocomplete="off">
        {foreach item=player from=$players}
            <option value="{$player.userId}" {if $vars.reporterId == $player.userId}selected{/if}>{$player.fullName}</option>
        {/foreach}
      </select>
    </div>

    <div class="col-md-6">
      <label for="tournamentTournamentType">Type des Turniers:</label>
      <select class="form-control js-example-basic-single" name="tournamentTournamentType" autocomplete="off">
        {foreach item=type from=$tournamentType}
            <option value="{$type}"  {if $vars.tournamentType == $type}selected{/if}>{$type}</option>
        {/foreach}
      </select>
    </div>
  </div>

  <h3 class="mt-5">Beschreibung</h3>
  <div class="row">
    <div class="col-md-12">
      <textarea id="summernote" name="tournamentDescription">{$vars['description']}</textarea>
    </div>
  </div>


  {if $task == "edit"}
    <div class="row mt-5 mb-5">
      <div class="col-md-12">
        <div class="alert alert-warning">
          <strong>Warnung!</strong> Hiermit löscht du dieses Turnier
        </div>
        <input type="checkbox" name="tournamentDelete" id="tournamentDelete" value="1"> <label for="tournamentDelete">Turnier löschen</label>
      </div>
    </div>
    <p></p>
  {/if}






  <div class="row mt-5">
    <div class="col-md-6">
      <input type="submit" name="submit" class="btn btn-success btn-wide" value="Turnier {if $task == "add"}hinzufügen{else}editieren{/if}">
    </div>
    <div class="col-md-6 text-right">
      <a class="btn btn-danger" href="/pages/rankingTournament.php">Zurück</a>
    </div>
  </div>
</form>
