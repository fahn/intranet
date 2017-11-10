<?php

include_once '../inc/html/brdbHtmlTournamentPage.inc.php';

session_start();

$page = new BrdbHtmlTournamentPage();
$page->processPage();

/**
  LOGIK
  =====

  Tabelle: Tournament
  - tournamentID
  - name
  - place
  - startdate
  - enddate
  - link

  Tabelle: TournamentClass
  - classID
  - tournamentID
  - name
  - description
  - modus: HE,DE,DD,HD,MX


  Tabelle: TournamentPlayer
  - playerID
  - classID
  - date
  - reporter
  - description


*/

?>
