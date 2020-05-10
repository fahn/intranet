{include file="page_wrap_header.tpl"}

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
        {if isset($widgetRankingLatestGames) && $rankingEnable == "on"}
            {$widgetRankingLatestGames}
        {/if}


        {if isset($widgetShowTeam) && isset($team)}
            {$widgetShowTeam|default:""}
        {/if}

        {if isset($isAdmin) && isset($widgetShowBdays)}
            {$widgetShowBdays}
        {/if}

        {if isset($social.socialHomepage) || isset($social.socialFacebook) || isset($social.socialYoutube) || isset($social.socialTwitter)}
            <div class="card mt-4 mb-4">
                <h5 class="card-header">Social</h5>
                <div class="card-body">
                    <p class="card-text text-center">
                        {if isset($social.socialHomepage)}
                            <a href="{$social.socialHomepage}" target="_blank"><i class="fas fa-home fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                        {/if}
                        {if isset($social.socialFacebook)}
                            <a href="{$social.socialFacebook}" target="_blank"><i class="fab fa-facebook fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                        {/if}
                        {if isset($social.socialYoutube)}
                            <a href="{$social.socialYoutube}" target="_blank"><i class="fab fa-youtube fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                        {/if}
                        {if isset($social.socialTwitter)}
                            <a href="{$social.socialTwitter}" target="_blank"><i class="fab fa-twitter fa-2x" aria-hidden="true"></i></a>&nbsp;&nbsp;
                        {/if}
                    </p>
                </div>
            </div>
        {/if}
    </div>
</div>

{include file="page_wrap_footer.tpl"}