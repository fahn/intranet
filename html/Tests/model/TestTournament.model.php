<?php 
declare(strict_types=1);

require_once("/var/www/html/inc/model/tournament.mdl.php");

#try {
    $tournament = new Tournament();
    $tournament->setTournamentId(1);
    $tournament->setName("Test-Turnier");
    $tournament->setPlace("Hannover");
    $tournament->setLink("http://www.google.de");

    echo $tournament;
/*
} catch (Exception $e) {
    echo $e;
}
*/