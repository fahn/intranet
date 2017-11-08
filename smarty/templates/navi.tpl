1 {$isUserLoggedIn}
{if isset($isUserLoggedIn) and $isUserLoggedIn == 1}
    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
            </button>
            <a class="navbar-brand" href="index.php"><img src="../design/img/badminton.png" width="30" alt="Welcome to Mail App">BC Comet Rangliste</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Ranking <b class="caret"></b></a>
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
            <li class="dropdown active">
                <a href="#"  class="dropdown-toggle" data-toggle="dropdown">Report  <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="reportInsertGame.php?formAction=NewGame">Add a game</a><li>
                    <li><a href="reportAllGame.php">List my games</a><li>
                </ul>
            </li>
           </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown1-toggle" data-toggle="dropdown">{$currentUserName} <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="myAccount.php">my Account</a></li>
                <li><a href="adminAllUser.php">Users</a></li>
                <li><a href="myRegistration.php">Registration</a></li>
                <li class="divider"></li>
                <li> <a href="logout.php"> Logout <i class="glyphicon glyphicon-log-out"></i></a></li>
              </ul>
            </li>
          </ul>
          
        </div><!-- /.navbar-collapse -->
      </nav>
{/if}