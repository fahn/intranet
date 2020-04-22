<h1 class="display-1 mb-5">Ranking {if $print}{$pageTitle}{/if}</h1>

{$message|default:""}

<div class="alert alert-info d-print-none">
  <p>Dieses Ranking basiert auf dem <a href="https://de.wikipedia.org/wiki/Elo-Zahl" target="_blank">ELO-Prinzip</a></p>
</div>

<div class="row d-print-none">
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

<ul class="nav nav-tabs d-print-none" style="margin-bottom: 20px;">
  <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#ranking">Ranking</a>
  </li>
  <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#games">Letzte Spiele</a>
  </li>
  {if isset($stats) && $stats == "on"}
      <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#stats">Statistik</a>
      </li>
  {/if}
</ul>

{if $print == "true"}
    {$dateFormat = "%d.%m.%y %H:%M"}
{else}
    {$dateFormat = "%d. %B %Y %H:%M"}

{/if}


<div class="tab-content">
    <div id="ranking" class="tab-pane container active">
        {include file="ranking/ranking.tpl" ranking=$ranking}
    </div>
    <div id="games" class="tab-pane container">
        {include file="ranking/lastGames.tpl" games=$games}
    </div>
    {if $stats == "on"}
        <div id="stats" class="tab-pane container">
            {include file="ranking/stats.tpl" labels=$labels}
        </div>
    {/if}
</div>


{if $print}
    <footer>
        <div class="pagenum-container">Seite <span class="pagenum"></span></div>
    </footer>
{/if}
