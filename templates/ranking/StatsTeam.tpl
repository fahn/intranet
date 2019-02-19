<h2>{$tableTitle}</h2>


{if isset($explain)}
  <div class="alert alert-info">
      {$explain}
  </div>
{/if}
{if $isAdmin or $isReporter}
  <p class="text-right">
    <a class="btn btn-success" href="/pages/reportInsertGame.php?formAction=NewGame">Spiel eintragen</a>
  </p>
{/if}

<div class="table-responsive">
  <table class="table table-sm table-striped" data-toggle="table"
    data-url="/gh/get/response.json/wenzhixin/bootstrap-table/tree/master/docs/data/data1/"
    data-search="true"
    data-show-refresh="true"
    data-show-toggle="true"
    data-show-columns="true">
    <thead>
      <tr class="thead-inverse">
        <th colspan = "2">Rank</th>
        <th colspan = "1">Team</th>
        <th colspan = "4">Games</th>
        <th colspan = "4">Sets</th>
        <th colspan = "4">Points</th>
      </tr>
      <tr>
        <th data-field="position">Position</th>
        <th>Rank</th>
        <th>Games</th>
        <th>Won</th>
        <th>Lost</th>
        <th>Ratio</th>
        <th>Sets</th>
        <th>Won</th>
        <th>Lost</th>
        <th>Ratio</th>
        <th>Poinst</th>
        <th>Won</th>
        <th>Lost</th>
        <th>Ratio</th>
      </tr>
    </thead>
    </thead>
    <tbody>
      {foreach key=key item=player from=$players}
      <tr>
        <td>{$player.position}</td>
        <td>{$player.rankPoints}</td>
        <td><a href="/pages/user.php?id={$player.player1Id}">{$player.player1FirstName} {$player.player1LastName}</a> // <a href="/pages/user.php?id={$player.player2Id}">{$player.player2FirstName} {$player.player2LastName}</a></td>
        <td>{$player.games}</td>
        <td>{$player.gamesWon}</td>
        <td>{$player.gamesLost}</td>
        <td>{$player.gamesRatio}</td>
        <td>{$player.sets}</td>
        <td>{$player.setsWon}</td>
        <td>{$player.setsLost}</td>
        <td>{$player.setsRatio}</td>
        <td>{$player.points}</td>
        <td>{$player.pointsWon}</td>
        <td>{$player.pointsLost}</td>
        <td>{$player.pointsRatio}</td>
      </tr>
      {/foreach}
    </tbody>
  </table>
</div>
