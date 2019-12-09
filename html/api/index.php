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
$_SERVER['BASE_DIR'] = $path;

require_once $_SERVER['BASE_DIR'] ."/inc/db/brdb.inc.php";
require_once $_SERVER['BASE_DIR'] ."/inc/logic/tools.inc.php";

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
      $res = $this->brdb->APIGetTournamentFromToday();
      if($res->num_rows > 0 ) {
          while($row = $res->fetch_assoc()) {
              if(isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                  $subject   = sprintf("Meldeschluss für %s", $row['name']);
                  // content
                  $to        = $row['email'];
                  $name      = $row['name'];
                  $link      = $this->tools->linkTo(array(
                      'page'   => 'tournament.php',
                      'action' => 'details',
                      'id'     => $row['tournamentId'],
                  ));
                  $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weiteren Informationen gibt es <a href='%s'>hier</a>.", $row['reporterName'], $row['name'], $link);
                  if($this->tools->sendMail($to, $name, $subject, $subject, $content)) {
                      $row['mail'] = "success";
                  }

                  if(isset($row) && is_array($row) && count($row) > 0) {
                      $this->content .= implode(", ", $row);
                  }
              }
          }
      }
  }

    private function importTournament() {
        // load Tournament
        $content = exec('../tools/getNbvTournament.py');
        try {
            $xml = simplexml_load_string($content);
        } catch (Exception $e) {
            echo "Import failed "+ $e;
            return;
        }
        $data = $this->getTournamentData();

        $stats=array('new' => 0, 'skipped' => 0);

        foreach ($xml->entry as $entry) {
            if (is_array($data) && count($data) > 0) {
                foreach($data as $tournament) {
                    similar_text($entry->title, $tournament['name'], $percent);
                    if ($percent > 50) {
                        echo "Skip $entry->title \n";
                        echo sprintf("\t%s VS %s = %d\n", $entry->title, $tournament['name'], $percent);
                        $stats['skipped'] +=1;
                        continue 2;
                    }
                }
            }

            # form dates()
            $startdate = $this->changeDate($entry->startdate);
            $enddate   = $this->changeDate($entry->enddate);
            $deadline  = $this->changeDate($entry->deadline);


            $classification = strval($entry->classification);
            $pos = strpos($classification, "-");
            if($pos) {
                $arr = explode("-", $classification);
                $prefix=substr($arr[0], 0, 1);
                $min=substr($arr[0], 1);
                $max=substr($arr[1], 1);
                // @TODO: U - Tourniere
                for($i=$min; $i<=$max; $i+=5) {
                    $temp[] =$prefix.$i;
                }
                $classification = serialize($temp);
            } else {
                $classification = serialize(array($classification));
            }

            try {
                $place     = $entry->place;
                $address   = $place .", Deutschland"; // Google HQ
                $latlng    = $this->tools->getGoogleLatAndLng($address);
                $latitude  = $latlng['lat'];
                $longitude = $latlng['lng'];
            } catch (Exception $e) {
                $latitude  = "";
                $longitude = "";
            }

            echo "Insert $entry->title\n";
            $this->brdb->APIinsertTournament($entry->title, $entry->place, $startdate, $enddate, $deadline, $entry->link, $classification, $entry->tournamentType, $entry->description, $latitude, $longitude);
            $stats['new'] +=1;

        }
        echo sprintf("STATS: NEW: %s SKIPPED: %s", $stats['new'], $stats['skipped']);
    }

    private function getTournamentData() {
        $data = array();
        $res = $this->brdb->APIGetTournamentList();
        while($dataSet = $res->fetch_assoc()) {
            $data[] = $dataSet;
        }

        return $data;
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
