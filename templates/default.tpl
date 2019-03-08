<div class="card mt-5 mb-5">
  <h5 class="card-header">Willkommen {$currentUserName}</h5>
  <div class="card-body">
    <p class="card-text">in unserem Intranet. Hier kannst du deine interne Rangliste pflegen und dich für kommende Turniere anmelden.</p>
  </div>
</div>

<div class="row">
    <div class="col-md-4">
        {if $widgetEloRankingLatestGames}
            {$widgetEloRankingLatestGames}
        {/if}

        {if $widgetShowTeam}
            {$team}
        {/if}

        {if !empty($social)}
          <div class="card mt-4 mb-4">
              <h5 class="card-header">Social Comet</h5>
              <div class="card-body">
                  <p class="card-text text-center">
                      {if $social.socialHomepage}
                          <a href="{$social.socialHomepage}" target="_blank"><i class="fas fa-home fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                      {/if}
                      {if $social.socialFacebook}
                          <a href="{$social.socialFacebook}" target="_blank"><i class="fab fa-facebook fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                      {/if}
                      {if $social.socialYoutube}
                          <a href="{$social.socialYoutube}" target="_blank"><i class="fab fa-youtube fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                      {/if}
                      {if $social.socialTwitter}
                          <a href="{$social.socialTwitter}" target="_blank"><i class="fab fa-twitter fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                      {/if}
                  </p>
              </div>
          </div>
        {/if}
    </div>

  <div class="col-md-8">
    {if $widgetUpcomingTournaments}
        {$widgetUpcomingTournaments}
    {/if}

    <div class="card mt-3 last news">
        <h5 class="card-header">Letzte technischen Neuigkeiten</h5>
        <ul class="list-group list-group-flush active">
            <li class="list-group-item">16.03.2018 // Fix Api error and fix sending mails</li>
            <li class="list-group-item">23.02.2018 // Fix and add icons to Menu</li>
            <li class="list-group-item">20.02.2018 // fixed serveral bugs</li>
            <li class="list-group-item">09.02.2018 // Marker in Turnieransicht verändert</li>
            <li class="list-group-item">08.02.2018 // Optimierungen, Überprüfung, ob Meldung von Spieler bei Turnieren berechtigt sind</li>
            <li class="list-group-item">07.02.2018 // Neue Turniere hinzugefügt & Änderungen am Meldesystem</li>
            <li class="list-group-item">31.01.2018 // Kompletter Austausch des Designs</li>
            <li class="list-group-item">28.01.2018 // Version für alle Mitglieder frei geschalten.</li>
        </ul>

</div>