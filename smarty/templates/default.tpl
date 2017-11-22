<div class="well">
  <h4>Willkommen {$currentUserName},</h4>
  in unserem Intranet. Hier kannst du deine interne Rangliste pflegen und dich für kommende Turniere anmelden.
</div>
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-primary">
      <div class="panel-heading">Deine letzten 5 Spiele</div>
      <div class="panel-body">
        <table class="table table-sm table-striped table-hover">
        {foreach item=game from=$games}
        <tr>
          <td>{$game.datetime|date_format:"d.m.Y"}</td>
          <td>{$game.opponent}</td>
          <td>{$game.result}</td>
        </tr>

        {foreachelse}
          Leider hast du noch keine Spiele gemacht.
        {/foreach}
      </table>
      <hr>
      <a href="/pages/statsPlayerAlltime.php"><i class="glyphicon glyphicon-list"></i> komplette Rangliste</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-primary">
      <div class="panel-heading">Kommende 5 Turniere</div>
      <div class="panel-body">
        <table class="table table-sm table-striped table-hover">
        {foreach item=tournament from=$tournaments}
        <tr>
          <td><a {if $tournament.deadline|strtotime < $smarty.now}class="text-warning"{/if} href="/pages/rankingTournament.php?action=details&id={$tournament.tournamentID}">{$tournament.name}</a></td>
          <td>{$tournament.startdate|date_format:"d.m.y"} - {$tournament.enddate|date_format:"d.m.y"} </td>
        </tr>
        {foreachelse}
          <tr>
            <td colspan="2">Leider keine Turniere in der kommenden Zeit.</td>
          </tr>
        {/foreach}
      </table>
      <hr>
      <a href="/pages/rankingTournament.php"><i class="glyphicon glyphicon-list"></i> alle Turniere</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-primary">
      <div class="panel-heading">Das Team</div>
      <div class="panel-body">
        {foreach item=user from=$users}
          <a href="/pages/user.php?id={$user['userId']}">
            {$user['fullName']}
          </a><br>
        {foreachelse}
          Fehler. Bitte einen Admin kontaktieren
        {/foreach}
        <hr>
        Bei diesen Personen kannst du dich jederzeit melden.
      </div>
    </div>

    <div class="panel panel-primary">
      <div class="panel-heading">Social Comet</div>
      <div class="panel-body text-center">
        <a href="http://bc-comet.de" target="_blank"><i class="glyphicon glyphicon-home"></i></a>&nbsp;&nbsp;
        <a href="https://www.facebook.com/BC.Comet/" target="_blank"><span class="fui-facebook"></span></a>&nbsp;&nbsp;
        <a href="https://www.youtube.com/channel/UCJhuBsKc55YdTNznSORIEQg" target="_blank"><span class="fui-youtube"></span></a>&nbsp;&nbsp;
      </div>
    </div>

  </div>
</div>
