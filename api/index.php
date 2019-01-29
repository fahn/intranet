<?php


require($_SERVER['BASE_DIR'] ."/inc/db/brdb.inc.php");
require($_SERVER['BASE_DIR'] ."/inc/logic/tools.inc.php");

class Api{
  protected $brdb;

  private $tools;

  private $content = "";

  public function __construct() {
    /* SQL CONNECTION */
    $this->brdb = new BrankDB();

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
      if(isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
          $subject = sprintf("Meldeschluss für %s", $row['name']);
          $link = $this->tools->linkTo('page' => 'rankingTournament.php', 'action' => 'details', 'id' => $row['tournamentID']) ;
          $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weitern Informationen gibt es <a href='%s'>hier</a>.", $row['reporterName'], $row['name'], $link);
          if ( $this->tools->sendMail($row['email'], $subject, $subject, $content)) {
                $row['mail'] = "success";
          }
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
