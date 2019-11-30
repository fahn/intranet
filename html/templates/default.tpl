<div class="card mt-5 mb-5">
    <h5 class="card-header">Willkommen {$currentUserName}</h5>
    <div class="card-body">
        <p class="card-text">in unserem Intranet. Hier kannst du deine interne Rangliste pflegen und dich für kommende Turniere anmelden.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-lg-pull-4 mb-5">
        {if $widgetUpcomingTournaments}
            {$widgetUpcomingTournaments}
        {/if}

        {if $newsEnable == "on" && $widgetLatestNews}
                {$widgetLatestNews}
        {/if}
    </div>
    <div class="col-lg-4 col-lg-push-8">
        {if $rankingEnable == "on" and $widgetRankingLatestGames}
            {$widgetRankingLatestGames}
        {/if}

        {if $widgetShowTeam}
            {$team}
        {/if}

        {if $isAdmin}
            {$widgetShowBdays}
        {/if}

        {if $social.socialHomepage || $social.socialFacebook || $social.socialYoutube || $social.socialTwitter}
            <div class="card mt-4 mb-4">
                <h5 class="card-header">Social</h5>
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
</div>
