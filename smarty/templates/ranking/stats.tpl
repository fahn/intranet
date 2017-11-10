

<h2>{$tableTitle}</h2>

{if isset($explain)}
  <div class="alert alert-info">
      {$explain}
  </div>

{/if}

<hr/>
<table class="table table-sm table-striped" data-toggle="table"
       data-url="/gh/get/response.json/wenzhixin/bootstrap-table/tree/master/docs/data/data1/"
       data-search="true"
       data-show-refresh="true"
       data-show-toggle="true"
       data-show-columns="true">
    <thead>
        <tr class="thead-inverse">
            <th class="text-center" colspan="2">Rank</th>
            <th class="text-center" colspan="2">Player</th>
            <th class="text-center" colspan="4">Games</th>
            <th class="text-center" colspan="4">Sets</th>
            <th class="text-center" colspan="4">Points</th>
        </tr>
        <tr>
            <th data-field="position">Position</th>
            <th>Rank</th>
            <th>Firstname</th>
            <th>Lastname</th>
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
    <tbody>
    {foreach key=key item=player from=$players}
        <tr>
            <tr>
                    <td>{$key+1}</td>
                    <td>{$player.rankPoints}</td>
                    <td>{$player.lastName}</td>
                    <td>{$player.firstName}</td>
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
        </tr>
    {/foreach}
    </tbody>
</table>
