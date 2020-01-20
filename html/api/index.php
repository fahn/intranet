#!/usr/bin/php

<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

$path=dirname(dirname(__FILE__));
define(BASE_DIR, $path);

require_once BASE_DIR ."/inc/db/brdb.inc.php";
require_once BASE_DIR ."/inc/logic/tools.inc.php";

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

        /* load TOOLS */
        $this->tools = new Tools();

        $action = $this->tools->get('action');
        switch ($action) {
            case 'tournament':
                $this->reminderTournament();
                break;

            case 'importTournament':
                $this->importTournament();
                break;

            default:
                # code...
                break;
        }

    }

  private function reminderTournament() {
      $tournamentList = $this->brdb->APIGetTournamentFromToday();
      if (isset($tournamentList) && !empty($tournamentList)) {
          foreach($tournamentList as $row) {
              if(isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                  $subject   = sprintf("Meldeschluss für %s", $row['name']);
                  // content
                  $toUser        = $row['email'];
                  $name      = $row['name'];

                  $link      = $this->tools->linkTo(array(
                      'page'   => 'tournament.php',
                      'action' => 'details',
                      'id'     => $row['tournamentId'],
                  ));
                  $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weiteren Informationen gibt es <a href='%s'>hier</a>.", $row['reporterName'], $row['name'], $link);
                  if($this->tools->sendMail($toUser, $name, $subject, $subject, $content)) {
                      $row['mail'] = "success";
                  }

                  if(isset($row) && is_array($row) && count($row) > 0) {
                      $this->content .= implode(", ", $row);
                  }
              }
          }
      }
  }


    private function getTournamentData() {
        return $this->brdb->APIGetTournamentList();
    }

    private function changeDate($date) {
        return date("Y-m-d", strtotime($date));
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
