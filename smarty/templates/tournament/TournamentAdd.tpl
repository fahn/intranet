<h2>Turnier hinzufügen</h2>

<form action="" method="post">
  <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="Insert Tournament">
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for = "tournamentName">Name des Turniers:</label>
        <input class="form-control"  type="text" id="tournamentName" name="tournamentName" placeholder="B-/C-Rangliste" value="{$variable['name']}" required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for = "tournamentPlace">Ort:</label>
        <input class="form-control"  type="text" id="tournamentPlace" name="tournamentPlace" placeholder="Braunschweig" value="{$variable['place']}"required>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <label for="tournamentStartdate">Start-Datum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentStartdate" name="tournamentStartdate" placeholder="dd.mm.YYYY" value="{$variable['startdate']}" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <label for = "tournamentEnddate">Enddatum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentEnddate" name="tournamentEnddate" placeholder="dd.mm.YYYY" value="{$variable['enddate']}" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <label for = "tournamentDeadline">Meldeschluss:</label>
      <div class="input-group date" data-provide="datepicker">
        <input class="form-control"  type="text" id="tournamentDeadline" name="tournamentDeadline" placeholder="dd.mm.YYYY" value="{$variable['deadline']}" required>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for = "tournamentLink">Link zur Ausschreibung:</label>
        <input class="form-control"  type="text" id="tournamentLink" name="tournamentLink" placeholder="http://" value="{$variable['link']}">
      </div>
    </div>
  </div>

  <hr>
  <h3>Diziplinen</h3>
  <div class="row initline">
    <div class="col-md-6">
      <div class="form-group">
        <label for = "tournamentClass">Klasse:</label>
        <input class="form-control"  type="text" id="tournamentClass" name="tournamentClass[]" placeholder="A" value="" required>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="tournamentMode">Diziplin:</label>
        <select class="form-control"  type="text" id="tournamentMode" name="tournamentMode[]">
          <option value="MX">HE<br>
          <option value="MX">DE<br>
          <option value="MX">HD<br>
          <option value="MX">DD<br>
          <option value="MX">MX<br>
        </select>
      </div>
    </div>
  </div>

  <div calss="row">
    <div class="col-md-12">
      <p><a href="#" class="clonerow"><i class="glyphicon glyphicon-share-alt"></i> weitere Klasse hinzufügen</a></p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <input type="submit" name="submit" class="btn btn-success btn-wide" value="Turnier hinzufügen">
    </div>
    <div class="col-md-6 text-right">
      <a class="btn btn-danger" href="javascript:history.back()">Zurück</a>
    </div>
  </div>
</form>
