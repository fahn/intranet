#!/usr/bin/php

<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
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
include_once(BASE_DIR .'/inc/logic/prgPattern.inc.php');

class Api extends APrgPatternElement
{
    protected BrankDB $brdb;

    private string $content = "";

    public function __construct()
    {
        //global $argv;
        if (!empty($argv[1])) {
            parse_str($argv[1], $_GET);
        }

        /* SQL CONNECTION */
        $this->brdb = new BrankDB();

        $action = $this->getGetVariable('action');
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
      $tournamentList = $this->brdb->APIGetTournamentFromToday();
      if (isset($tournamentList) && !empty($tournamentList)) {
          foreach($tournamentList as $row) {
              if(isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                  $subject   = sprintf("Meldeschluss für %s", $row['name']);
                  // content
                  $toUser    = $row['email'];
                  $name      = $row['name'];

                  $link      = $this->linkTo(array(
                      'page'   => 'tournament.php',
                      'action' => 'details',
                      'id'     => $row['tournamentId'],
                  ));
                  $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weiteren Informationen gibt es <a href='%s'>hier</a>.", $row['reporterName'], $row['name'], $link);
                  if ($this->sendMail($toUser, $name, $subject, $subject, $content)) {
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
