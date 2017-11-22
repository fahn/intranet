<div id="formReportInsertGame">
{include file="messages.tpl"}
  <h3>Report a Game for Badminton Ranking</h3>
  <p>Tell the date, players, set points and winner of the played game.</p>
  <hr/>
  <form action="{$to}" method="post">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group datepicker"  data-provide="datepicker">
          <label for = "{$variable['NameDate']}">Date and Time:</label>
          <input class="form-control" type="input" id="{$variable['NameDate']}" name="{$variable['NameDate']}" value="{if $variable['NameDateValue']}{$variable['NameDateValue']}{else}{$smarty.now|date_format:"Y-m-d"} {/if}" tabindex=1 value="">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group" >
          <label for = "{$variable['NameTime']}">Uhrzeit:</label>
          <input class="form-control" type="time" id="{$variable['NameTime']}" name="{$variable['NameTime']}" value="{if $variable['NameTimeValue']}{$variable['NameTimeValue']}{else}{$smarty.now|date_format:"H:i"}{/if}" tabindex=2 value="">
        </div>
      </div>
    </div>

  <div class="row">
    <div class="col-md-6">
    <h4>Team 1</h4>
      <label for="{$variable['NamePlayerA1']}" >Spieler A:</label>
      <select class="select-selectize" name="{$variable['NamePlayerA1']}" id="{$variable['NamePlayerA1']}" tabindex=3 value="{$variable['NamePlayerA1Value']}">
        <option value=""><br>
        {foreach item=player from=$players}
          <option value="{$player.userId}">{$player.fullName}<br>
        {/foreach}
      </select>
      <!-- Spieler B -->
      <label for="{$variable['NamePlayerA2']}">Spieler B:</label>
      <select class="select-selectize" name="{$variable['NamePlayerA2']}" id="{$variable['NamePlayerA2']}" tabindex=4 value="{$variable['NamePlayerA2Value']}">
        <option value=""><br>
        {foreach item=player from=$players}
          <option value="{$player.userId}">{$player.fullName}<br>
        {/foreach}
      </select>

      <hr>
      <h4>Satz 1</h4>
      <div class="form-group">
        <label for="{$variable['NameSetA1']}">Punkte</label>
        <input class="form-control" type="number" min="0" max="30" name="{$variable['NameSetA1']}" id="{$variable['NameSetA1']}" value="{$variable['NameSetA1Value']}" class="p11" tabindex=7>
      </div>

      <h4>Satz 2</h4>
      <div class="form-group">
        <label for="{$variable['NameSetA2']}">Punkte</label>
        <input class="form-control" type="number" min="0" max="30" name="{$variable['NameSetA2']}" id="{$variable['NameSetA2']}" value="{$variable['NameSetA2Value']}" class="p21" tabindex=9>
      </div>

      <h4>Satz 3</h4>
      <div class="form-group">
        <label for="{$variable['NameSetA3']}">Punkte</label>
        <input class="form-control" type="number" min="0" max="30" name="{$variable['NameSetA3']}" id="{$variable['NameSetA3']}" value="{$variable['NameSetA3Value']}" class="p31" tabindex=11>
      </div>
    </div>

    <div class="col-md-6" style="border-left: 3px solid #c3c3c3">
    <h4>Team 2</h4>
      <label for="{$variable['NamePlayerB1']}">Spieler A:</label>
      <select class="select-selectize" name="{$variable['NamePlayerB1']}" id="{$variable['NamePlayerB1']}" value="{$variable['NamePlayerB1Value']}" tabindex=5>
        <option value=""><br>
        {foreach item=player from=$players}
          <option value="{$player.userId}">{$player.fullName}<br>
        {/foreach}
      </select>
      <!-- Spieler B -->
      <label for="{$variable['NamePlayerB2']}">Spieler B:</label>
      <select class="select-selectize" name="{$variable['NamePlayerB2']}" id="{$variable['NamePlayerB2']}" value="{$variable['NamePlayerB2Value']}" tabindex=6>
        <option value=""><br>
        {foreach item=player from=$players}
          <option value="{$player.userId}">{$player.fullName}<br>
        {/foreach}
      </select>

      <hr>
      <h4>Satz 1</h4>
      <div class="form-group">
        <label for="{$variable['NameSetB1']}">Punkte</label>
        <input class="form-control" type="number" min="0" max="30" name="{$variable['NameSetB1']}" id="{$variable['NameSetB1']}" value="{$variable['NameSetB1Value']}" class="p12" tabindex=8>
      </div>

      <h4>Satz 2</h4>
      <div class="form-group">
        <label for="{$variable['NameSetB2']}">Punkte</label>
        <input class="form-control" type="number" min="0" max="30" name="{$variable['NameSetB2']}" id="{$variable['NameSetB2']}" value="{$variable['NameSetB2Value']}" class="p22" tabindex=10>
      </div>

      <h4>Satz 3</h4>
      <div class="form-group">
        <label for="{$variable['NameSetB3']}">Punkte</label>
        <input class="form-control" type="number" min="0" max="30" name="{$variable['NameSetB3']}" id="{$variable['NameSetB3']}" value="{$variable['NameSetB3Value']}" class="p32" tabindex=12>
      </div>
    </div>
  </div>
  <hr>
  <input type="hidden" id="userRegisterGameWinner" name="userRegisterGameWinner" value="">
  <input type="hidden" id="formAction" name="formAction" value="Insert Game">
  <input class="btn btn-success" type="submit" name="{$variable['NameAction']}" value="{$variable['NameActionInsertGame']}">
</form>
</div>
