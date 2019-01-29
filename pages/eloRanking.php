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

require_once '_options.php';
include_once __PFAD__ .'/inc/html/eloRanking.inc.php';

$page = new EloRanking();
$page->processPage();
die();
?>


<?php


class Player {
  private $name;
  private $points = 1000;

  function __construct() {
  }

  function setName($name) {
    $this->name = $name;
  }

  function getName() {
    return $this->name;
  }

  /**
    set Name
  */
  function setPoints($points) {
    $this->points = $points;
  }

  /**
    get Points
  */
  function getPoints() {
    return $this->points;
  }

  function output() {
    return sprintf("<b>%s</b> have %s Points.<br>", $this->name, $this->points);
  }
}

class Elo {
  private $playerA; # = Player();
  private $playerB; # = Player();
  private $winner;

  function __construct() {

  }

  function setPlayers($playerA, $playerB)  {
    if ( ! is_a($playerA, "Player") || ! is_a($playerB, "Player")) {
      die("no player");
    }
    $this->playerA = $playerA;
    $this->playerB = $playerB;
  }

  function setScore($set1, $set2, $set3 = null) {
    $a = 0;
    $b = 0;
    $this->checkScore($set1) == true ? $a++ : $b++;
    $this->checkScore($set2) == true ? $a++ : $b++;
    $this->checkScore($set3) == true ? $a++ : $b++;

    if ($a > $b) {
      $this->winner = true;
    } else {
      $this->winner = false;
    }

  }

  function setElo() {
      $eloDiff = $this->playerB->getPoints() - $this->playerA->getPoints();
      echo $eloDiff ."<br><br>";
      $elo = 1 / (1+10^($eloDiff/200));
      echo $elo ."<br><br>";

      $this->playerA->setPoints((int) ($this->playerA->getPoints() + 15 * (1 - $elo)));

      $this->playerB->setPoints((int) ($this->playerB->getPoints() + 15 * (0 - $elo)));


  }




  private function checkScore($set) {
    $arr = explode(":", $set);
    if ($arr[0] > $arr[1]) {
      return true;
    }

    return false;
  }
}




$playerA = new Player();
$playerA->setName("Spieler 1");

$playerB = new Player();
$playerB->setName("Spieler 2");

print($playerA->output());
print($playerB->output());

$elo = new Elo();

$elo->setPlayers($playerA, $playerB);
$elo->setScore("21:12", "12:21", "21:12");
$elo->setElo();



print($playerA->output());
print($playerB->output());

$elo->setPlayers($playerA, $playerB);
$elo->setScore("21:12", "12:21", "21:12");
$elo->setElo();

print($playerA->output());
print($playerB->output());

$playerC = new Player();
$playerC->setName("Spieler 3");

$elo->setPlayers($playerA, $playerC);
$elo->setScore("21:12", "21:12");
$elo->setElo();


print($playerA->output());
print($playerC->output());


$elo->setPlayers($playerB, $playerC);
$elo->setScore("21:12", "21:12");
$elo->setElo();

print($playerB->output());
print($playerC->output());




?>
