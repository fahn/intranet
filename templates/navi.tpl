{if isset($isUserLoggedIn) and $isUserLoggedIn == 1}

    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-2 mb-3">
        <a class="navbar-brand" href="index.php"><img src="/static/img/logo.png" width="30" alt="Intern">{$logoTitle}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav mr-auto nav-pills">
                {if $tournamentEnable == "on"}
                    <li class="nav-item"><a class="nav-link" href="tournament.php"><i class="fas fa-trophy"></i> Tuniere</a><li>
                {/if}

                {if $rankingEnable == "on"}
                  <li class="nav-item"><a class="nav-link" href="ranking.php"><i class="fas fa-list-ol"></i> Ranking</a></li>
                {/if}

                {if $faqEnabled == "on"}
                    <li class="nav-item"><a class="nav-link" href="faq.php"><i class="fas fa-question"></i> FAQ</a></li>
                {/if}

                <li class="nav-item"><a class="nav-link" href="team.php"><i class="fas fa-users"></i> Team</a></li>

                {if $isAdmin}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="adminAllUser.php"><i class="fas fa-users"></i> Users</a>
                            <a class="dropdown-item" href="adminAllClub.php"><i class="fas fa-shield-alt"></i> Vereine</a><!--
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="myRegistration.php"><i class="fas fa-registered"></i> Registration</a> -->
                            <div class="dropdown-divider"></div>
                            {if $faqEnabled == "on" || $userId == 1}
                                <a class="dropdown-item" href="/pages/adminFaq.inc.php"><i class="fas fa-question-circle"></i> FAQ</a>
                            {/if}
                            <!-- <a class="dropdown-item" href="settings.php"><i class="fas fa-wrench"></i> Einstellungen</a> -->
                        </div>
                    </li>
                {/if}
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {$currentUserName}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right text-right">
                        <a class="dropdown-item" href="/pages/user.php?id={$userId}"><i class="fas fa-user-circle" aria-hidden="true"></i> My Account</a>
                        <a class="dropdown-item" href="myAccount.php"><i class="fas fa-edit"></i> Edit Account</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/pages/support.php"><i class="fas fa-medkit"></i> Support</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/pages/about.php"><i class="fas fa-bookmark"></i> About</a>
                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout</a>
                    </div>
                </li>
                {if $notificationEnable == "on"}
                    <li class="nav-item">
                        {if $notification}
                            {include file="notification.tpl"}
                        {else}
                            <i class="fas fa-bell"></i>
                        {/if}
                    </li>
                {/if}
            </ul>
        </div>
    </nav>
{/if}
