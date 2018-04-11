<h3>Report a Game for Badminton Ranking</h3>
{if $action == "update"}
<div class="alert alert-danger">
  >> Bearbeiten klappt noch nicht!
</div>
{/if}
<hr/>
<form action="" method="post">
  <input type="hidden" id="rankingGameWinner" name="rankingGameWinner" value="">
  <input type="hidden" id="rankingFormAction" name="rankingFormAction" value="{if $action == "update"}Update Game{else}Insert Game{/if}">
  <div class="row">
    <div class="col-md-6">
      <label for="rankingGameDate">Datum:</label>
      <div class="input-group date" data-provide="datepicker">
        <input type="text" class="form-control" id="rankingGameDate" name="rankingGameDate" placeholder="" value="{$game.datetime|date_format:"d.m.Y"}">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group" >
        <label for="rankingGameTime">Uhrzeit:</label>
        <input class="form-control" type="text" id="rankingGameTime" name="rankingGameTime" value="{$game.datetime|date_format:"H:i"}">
      </div>
    </div>
  </div>

<div class="row">
  <div class="col-md-6">
        <div class="card">
            <h5 class="card-header">Team 1</h5>
            <div class="card-body">
                <p class="card-text">
                  <label for="rankingGamePlayerA1" class="d-block">Spieler A:</label>
                  <select class="js-example-data-array form-control d-block" name="rankingGamePlayerA1" id="rankingGamePlayerA1" placeholder="Bitte wählen">
                    <option value="0">Bitte wählen</option>
                  </select>

                  <label for="rankingGamePlayerA2" class="d-block">Spieler A:</label>
                  <select class="js-example-data-array form-control d-block" name="rankingGamePlayerA2" id="rankingGamePlayerA2" placeholder="Bitte wählen">
                    <option value="0">Bitte wählen</option>
                  </select>
                </p>
            </div>
        </div>
  </div>

  <div class="col-md-6">
    <div class="card">
        <h5 class="card-header">Team 2</h5>
        <div class="card-body">
          <label for="rankingGamePlayerB1" class="d-block">Spieler B:</label>
          <select class="js-example-data-array form-control d-block" name="rankingGamePlayerB1" id="rankingGamePlayerB1" placeholder="Bitte wählen">
            <option value="0">Bitte wählen</option>
          </select>

          <label for="rankingGamePlayerB2" class="d-block">Spieler B:</label>
          <select class="js-example-data-array form-control d-block" name="rankingGamePlayerB2" id="rankingGamePlayerB2" placeholder="Bitte wählen">
            <option value="0">Bitte wählen</option>
          </select>
        </div>
      </div>
    </div>
  </div>


  <div class="row mt-5">
    <div class="col-md-4">
      <div class="card">
        <h5 class="card-header">Satz 1</h5>
        <div class="card-body">
          <input class="form-control text-center" type="text" name="rankingGameSet1" id="rankingGameSet1" value="{$game.set1}" placeholder="21:19" pattern="{literal}^[0-9]{1,2}:[0-9]{1,2}${/literal}" required >
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <h5 class="card-header">Satz 2</h5>
        <div class="card-body">
          <input class="form-control text-center" type="text" name="rankingGameSet2" id="rankingGameSet2" value="{$game.set2}" placeholder="21:19" pattern="{literal}^[0-9]{1,2}:[0-9]{1,2}${/literal}" required >
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <h5 class="card-header">Satz 3</h5>
        <div class="card-body">
          <input class="form-control text-center" type="text" name="rankingGameSet3" id="rankingGameSet3" value="{$game.set3}" placeholder="21:19" pattern="{literal}^[0-9]{1,2}:[0-9]{1,2}${/literal}" >
        </div>
      </div>
    </div>
  </div>

<input class="btn btn-success mt-5 mb-5" type="submit" name="submit" value="Eintragen">
</form>
</div>


<script>
{literal}
var data = [{id: 0, text: 'Bitte wählen'}];
{/literal}

{foreach item=player from=$players}
    data.push({ldelim}
        id: {$player.userId},
        text: '{$player.fullName}'
    {rdelim});
{/foreach}

$( document ).ready(function() {ldelim}
  {if $game.playerA1}
    $('#rankingGamePlayerA1').val(144).trigger('change');
  {/if}
{rdelim});
</script>
