<h2>Hinzufügen zum Turnier</h2>

<form method="post">
  Turnier: {$tournament.name}

  <h3>Disziplin</h3>
  {foreach item=discipline from=$disciplines}
    <label class="radio" for="discipline{$discipline.classID}">
      <input type="radio" name="discipline{$discipline.classID}" value="{$discipline.classID}" id="discipline" data-toggle="radio" class="custom-radio">
      <span class="icons">
        <span class="icon-checked"></span>
        <span class="icon-unchecked"></span>
      </span>
      {$discipline.name} {$discipline.modus}
    </label>
  {foreachelse}
    Leider keine Diziplinen<br>
  {/foreach}


  <h3>Spieler</h3>
  <div row>
    <div class="col-md-6">
      <div class="input-group date">
        <input type="text" class="form-control" placeholder="Spieler">
        <div class="input-group-addon">
          <span class="glyphicon glyphicon-user"></span>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="input-group date">
        <input type="text" class="form-control" placeholder="Partner">
        <div class="input-group-addon">
          <span class="glyphicon glyphicon-user"></span>
        </div>
      </div>
    </div>
  </div>

  <br><br><br>

  <input type="submit" class="btn btn-success btn-wide" value="Melden">
  <input type="submit" class="btn btn-info btn-wide" value="Melden + Schließen ">
</form>
