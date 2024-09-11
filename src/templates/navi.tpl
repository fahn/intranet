{if isset($isUserLoggedIn) and $isUserLoggedIn == 1}

    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-2 mb-3">
        <a class="navbar-brand" href="/"><img src="/static/img/logo.png" width="30" alt="Intern">{$logoTitle}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav mr-auto nav-pills">
                {if isset($tournamentEnable) && $tournamentEnable == "on"}
                    <li class="nav-item"><a class="nav-link" href="/tournament"><i class="fas fa-trophy"></i> Tuniere</a></li>
                {/if}
                
                {if $rankingEnable == "on"}
                  <li class="nav-item"><a class="nav-link" href="/ranking"><i class="fas fa-list-ol"></i> Ranking</a></li>
                {/if}

                {if $faqEnabled == "on"}
                    <li class="nav-item"><a class="nav-link" href="/faq"><i class="fas fa-question"></i> FAQ</a></li>
                {/if}

                <li class="nav-item"><a class="nav-link" href="/staff"><i class="fas fa-users"></i> Team</a></li>

                {if $isAdmin}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/admin/user"><i class="fas fa-users"></i> Users</a>
                            <a class="dropdown-item" href="/admin/staff"><i class="fas fa-user-tie"></i> Staff</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/admin/images"><i class="fas fa-images"></i> Bilder</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/admin/player"><i class="fas fa-user-friends"></i> Player</a>
                            <a class="dropdown-item" href="/admin/club"><i class="fas fa-shield-alt"></i> Vereine</a>
                            <a class="dropdown-item" href="/admin/sync"><i class="fas fa-sync"></i> Sync</a>
                            <div class="dropdown-divider"></div>
                            {if $faqEnabled == "on"}
                                <a class="dropdown-item" href="/admin/faq"><i class="fas fa-question-circle"></i> FAQ</a>
                            {/if}
                            <a class="dropdown-item" href="/admin/news"><i class="fas fa-newspaper"></i> News</a>
                            <a class="dropdown-item" href="/admin/category"><i class="far fa-list-alt"></i> Kategorien</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/admin/settings"><i class="fas fa-wrench"></i> Einstellungen</a>
                            <a class="dropdown-item" href="/admin/log"><i class="fas fa-list"></i> Logs</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/admin/setup"><i class="fas fa-wrench"></i> Setup</a>
                        </div>
                    </li>
                {/if}
            </ul>

            <ul class="nav navbar-nav navbar-right">
                {if isset($notificationEnable) && $notificationEnable == "on"}
                    <li class="nav-item">
                        {if isset($notification)}
                            {include file="notification.tpl"}
                        {else}
                            <i class="fas fa-bell"></i>
                        {/if}
                    </li>
                {/if}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="avatar avatar-online"><img width="33px" style="border-radius: 1000px" src="{$currentUserImage}" alt="avatar"><i></i></span>
                        {$currentUserName|default:""}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">

                        <a class="dropdown-item" href="/user/myAccount"><i class="fas fa-user-circle" aria-hidden="true"></i> My Account</a>
                        <a class="dropdown-item" href="/user/edit/{$userId}"><i class="fas fa-edit"></i> Edit Account</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/support"><i class="fas fa-medkit"></i> Support</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/about"><i class="fas fa-bookmark"></i> About</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout"><i class="fas fa-power-off"></i> Logout</a>
                    </div>
                </li>
                
            </ul>
        </div>
    </nav>
{/if}
