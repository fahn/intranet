#!/usr/bin/php

<?php

/**
  Version 1.0
  @author: Stefan Metzner
  Description:

*/


// test if __PFAD__ is defined
if(!defined("__PFAD__")) {
  define("__PFAD__", dirname(dirname(__FILE__)));
}

require_once(__PFAD__ ."/inc/db/brdb.inc.php");
require_once(__PFAD__ ."/inc/logic/tools.inc.php");

class Api {
  protected $brdb;

  private $tools;

  private $content = "";

  public function __construct() {
    global $argv;
    if (!empty($argv[1])) {
        parse_str($argv[1], $_GET);
    }

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
      if($res->num_rows > 0 ) {
          while($row = $res->fetch_assoc()) {
              if(isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                  $subject   = sprintf("Meldeschluss für %s", $row['name']);
                  $preheader = $subject;
                  // content
                  $to        = $row['email'];
                  $name      = $row['name'];
                  $link      = $this->tools->linkTo(array(
                      'page'   => 'rankingTournament.php',
                      'action' => 'details',
                      'id'     => $row['tournamentID'],
                  ));
                  $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weiteren Informationen gibt es <a href='%s'>hier</a>.", $row['reporterName'], $row['name'], $link);
                  if($this->tools->sendMail($to, $name, $subject, $preheader, $content)) {
                      $row['mail'] = "success";
                  }

                  if(isset($row) && is_array($row) && count($row) > 0) {
                      $this->content .= implode(", ", $row);
                  }
              }
          }
      }
  }

  function __toString() {
    return $this->content;
  }
}

/* OUTPUT */
$api = new Api();
echo $api;
echo "\n";
exit(0);

?>
