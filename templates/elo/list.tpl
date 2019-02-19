<h1 class="display-1 mb-5">Ranking {if $print}{$pageTitle}{/if}</h1>

{if $print == "true"}
    {$dateFormat = "%d.%m.%y %H:%M"}
{else}
    {$dateFormat = "%d. %B %Y %H:%M"}

{/if}

{if $stats == "on"}

    <canvas id="myChart"></canvas>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
    var ctx = document.getElementById('myChart').getContext('2d');

    var labels=[{$labels}]

    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'line',

        // The data for our dataset
        data: {
            labels: labels,
            datasets: [{
                label: "My First dataset",
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: [0, 10, 5, 2, 20, 30, 45],
            }]
        },

        // Configuration options go here
        options: {}
    });

    </script>
{/if}

{$message}
{if $print == false}
    <div class="alert alert-info">
      <p>Dieses Ranking basiert auf dem <a href="https://de.wikipedia.org/wiki/Elo-Zahl" target="_blank">ELO-Prinzip</a></p>
    </div>

    <div class="row">
        <div class="col-md-12 text-right mb-3">
            <div class="btn-toolbar pull-right" role="toolbar" aria-label="Toolbar with button groups">
                {if $isAdmin or $isReporter}
                    <div class="btn-group mr-2 pull-right" role="group" aria-label="First group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Optionen</button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="?action=add_game"><i class="fas fa-reply"></i> Spiel eintragen</a>
                            <a class="dropdown-item" href="?action=renewRanking"><i class="fas fa-retweet"></i> Neu berechnung</a>
                        </div>
                    </div>
                {/if}
                <div class="btn-group mr-2" role="group" aria-label="Second group">
                    <a class="btn btn-primary" role="button" href="?action=download" target="_blank"><i class="fas fa-download"></i> PDF Download</a>
                </div>
            </div>
        </div>
    </div>


{/if}

<table class="table">
  <tr>
    <th class="text-center" colspan="2">Spieler</th>
    <th>Punkte</th>
    <th>Serie</th>
    <th>Letztes Spiel</th>
  </tr>
{foreach key=rank item=item from=$ranking}
  <tr>
    <td class="text-center {if $rank == "1"}text-color-gold{elseif $rank == "2"}text-color-silver{elseif $rank == "3"}text-color-bronce{/if}" >{if $rank < 4}<i class="fas fa-trophy"></i>{/if} {$rank}</td>
    <td><a href="{$item.playerLink}">{$item.name}</a></td>
    <td>{$item.points}</td>
    <td>{$item.win}-{$item.loss}</td>
    <td>{$item.lastMatch|date_format:$dateFormat}</td>
  </tr>
  {foreachelse}
      <tr>
          <td colspan="5" class="text-center">keine Spiele bisher</td>
      </tr>
  {/foreach}
</table>

{if $print}
    <div class="page_break"></div>
{/if}

<h2 class="display-2 mb-5">Letzte Spiele </h2>
<table class="table">
  <tr>
    <th>Spieler</th>
    <th>Gegner</th>
    <th>Sätze</th>
    <th>Datum</th>
    {if ! $print}
        <th class="text-center">Optionen</th>
    {/if}
  </tr>
{foreach item=item from=$games}
  <tr>
    <td><a href="{$item.playerLink}">{$item.playerName}</a></td>
    <td><a href="{$item.opponentLink}">{$item.opponentName}</a></td>
    <td>{$item.sets}</td>
    <td>{$item.time|date_format:$dateFormat}</td>
    {if ! $print}
        <td class="text-center"><a href="{$item.deleteLink}"><i class="fas fa-trash-alt"></i></a></td>
    {/if}
  </tr>
  {foreachelse}
      <tr>
          <td colspan="5" class="text-center">keine Spiele bisher</td>
      </tr>
  {/foreach}
</table>


{if $print}
    <footer>
        <div class="pagenum-container">Seite <span class="pagenum"></span></div>
    </footer>
{/if}
