{if isset($isUserLoggedIn) and $isUserLoggedIn == 1}
    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">

            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
            </button>
            <a class="navbar-brand" href="index.php"><img src="../design/img/badminton.png" width="30" alt="Welcome to Mail App">BC Comet BS</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Rangliste <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="statsPlayerAlltime.php">Alltime</a><li>
                        <li><a href="statsPlayerOverall.php">Singe Overall</a><li>
                        <li><a href="statsPlayerMen.php">Singe Men</a><li>
                        <li><a href="statsPlayerWomen.php">Singe Woman</a><li>
                        <li class="divider"></li>
                        <li><a href="statsTeamOverall.php">Double Overall</a><li>
                        <li><a href="statsTeamMen.php">Double Men</a><li>
                        <li><a href="statsTeamWomen.php">Double Woman</a><li>
                        <li class="divider"></li>
                        <li><a href="statsTeamMixed.php">Double Mixed</a><li>
                    </ul>
                </li>
              <li><a href="rankingTournament.php">Tuniere</a><li>

              {if isAdmin}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      {if isAdmin}
                        <li><a href="reportAllGame.php"><i class="glyphicon glyphicon-th-list"></i> List my games</a><li>
                        <li><a href="adminAllUser.php"><i class="glyphicon glyphicon-user"></i> Users</a></li>
                        <li><a href="myRegistration.php">Registration</a></li>
                      {/if}
                    </ul>
                </li>
              {/if}
           </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#"" class="dropdown1-toggle" data-toggle="dropdown">{$currentUserName} <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/pages/user.php?id={$userId}"><i class="glyphicon glyphicon-user"></i> My Account</a></li>
                <li><a href="myAccount.php"><i class="glyphicon glyphicon-pencil"></i> Edit Account</a></li>
                <li class="divider"></li>
                <li> <a href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Logout</a></li>
              </ul>
            </li>
          </ul>

        </div><!-- /.navbar-collapse -->
      </nav>
{/if}
