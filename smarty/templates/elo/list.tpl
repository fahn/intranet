<h1 class="display-1 mb-5">Ranking {if $print}{$pageTitle}{/if}</h1>
{if $print == false}
    <div class="alert alert-info">
      <p>Dieses Ranking basiert auf dem <a href="https://de.wikipedia.org/wiki/Elo-Zahl" target="_blank">ELO-Prinzip</a></p>
    </div>

    {if $isAdmin or $isReporter}
    <div class="row">
        <div class="col-md-12 text-right">
            <div class="p-2">
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Optionen</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="?action=add_game"><i class="fas fa-reply"></i> Spiel eintragen</a>
                        <a class="dropdown-item" href="?action=backup&id={$tournament.tournamentID}"><i class="fas fa-retweet"></i> Neu berechnung</a>

                    </div>
                </div>
            </div>
            <p><a class="btn btn-primary" role="button" href="?action=download" target="_blank"><i class="fas fa-download"></i> PDF Download</a></p>
        </div>
    </div>

    {/if}
{/if}

<table class="table">
  <tr>
    <th class="text-center" colspan="2">Spieler</th>
    <th>Punkte</th>
    <th>Serie</th>
    <th>Letztes Spiel</th>
  </tr>
{foreach item=item from=$ranking}
  <tr>
    <td class="text-center {if $item.rank == "1"}text-color-gold{elseif $item.rank == "2"}text-color-silver{elseif $item.rank == "3"}text-color-bronce{/if}" >{if $item.rank < 4}<i class="fas fa-trophy"></i>{/if} {$item.rank}</td>
    <td>{$item.name}</td>
    <td>{$item.points}</td>
    <td>1-0</td>
    <td>{$item.time}</td>
  </tr>
  {/foreach}
</table>