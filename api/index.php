<?php

if(!defined("__PFAD__")) {
  define("__PFAD__", dirname(dirname(__FILE__)));
}

require(__PFAD__ ."/inc/db/brdb.inc.php");
require(__PFAD__ ."/inc/logic/tools.inc.php");

class Api{
  protected $brdb;

  private $tools;

  private $content = "";

  public function __construct() {
    /* SQL CONNECTION */
    $this->brdb = new BrankDB();
    $this->brdb->connectAndSelectDB();
    $this->brdb->prepareCommands();

    $this->tools = new Tools();

    $action = $this->tools->get('action');

    switch ($action) {
      case 'tournament':
        $this->reminderTournament();
        break;

      default:
        # code...
        break;
    }

  }

  private function reminderTournament() {
    $res = $this->brdb->APIGetTournamentFromToday();
    while($row = $res->fetch_assoc()) {
      if(isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL))
      $to = $row['email'];
      $subject = sprintf("Meldeschluss für %s", $row['name']);
      $preheader = $subject;
      $ini = $this->tools->getini();
      $link = $ini['baseUrl'] ."pages/rankingTournament.php?action=details&id=". $row['tournamentID'];
      $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weitern Informationen gibt es <a href=''>hier</a>.", $row['reporterName'], $row['name']);
      if ( $this->tools->sendMail($to, $subject, $preheader, $content)) {
        $row['mail'] = "success";
      }

      $this->content .= implode(", ", $row);
    }
  }

  function __toString() {
    return $this->content;
  }
}


$api = new Api();
echo $api;
echo "\n";
?>
