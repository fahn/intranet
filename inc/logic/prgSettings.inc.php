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

include_once __PFAD__ .'/inc/db/brdb.inc.php';
include_once __PFAD__ .'/inc/db/user.inc.php';
include_once __PFAD__ .'/inc/logic/prgPattern.inc.php';
include_once __PFAD__ .'/inc/logic/tools.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementTournament extends APrgPatternElement {

    private $brdb;
    private $tools;

    const FORM_FORM_ACTION                = "formAction";
    // insert player to tournament
    const FORM_GAME_ACTION_INSERT_PLAYERS = "Insert Players";
    const FORM_INPUT_PLAYER               = "playerId";
    const FORM_INPUT_PARTNER              = "partnerId";
    const FORM_INPUT_DISCIPLIN            = "diziplin";

    // insert tournament
    const FORM_GAME_ACTION_INSERT_TOURNAMENT   = "Insert Tournament";
    const FORM_INPUT_TOURNAMENT_NAME           = "name";
    const FORM_INPUT_TOURNAMENT_PLACE          = "place";
    const FORM_INPUT_TOURNAMENT_STARTDATE      = "startdate";
    const FORM_INPUT_TOURNAMENT_ENDDATE        = "enddate";
    const FORM_INPUT_TOURNAMENT_DEADLINE       = "deadline";
    const FORM_INPUT_TOURNAMENT_LINK           = "link";
    const FORM_INPUT_TOURNAMENT_CLASSIFICATION = "classification";
    const FORM_INPUT_TOURNAMENT_ADDITION_CLASSIFICATION = "additionalClassification";
    const FORM_INPUT_TOURNAMENT_DISCIPLINE     = "discipline";
    const FORM_INPUT_TOURNAMENT_TYPE           = "tournamentType";
    const FORM_INPUT_TOURNAMENT_REPORTER_ID    = "reporterId";


    const FORM_GAME_ACTION_UPDATE_TOURNAMENT  = "Edit Tournament";
    const FROM_TOURNAMENT_DELETE_STATUS       = "delete";



    // Errors that can be set by methods of this class
    const ERROR_GAME_MISSING_INFORMATION   = "Please provide all required information!";
    const SUCCESS_GAME_INSERT              = "Succesfully inserted game!";
    const SUCCESS_UPDATING_GAME_READ       = "Succesfully read game (ID: %d) for updating!";
    const SUCCESS_GAME_UPDATED             = "Succesfully updated game!";
    const SUCCESS_GAME_DELETE              = "Succesfully deleted game!";
    const ERROR_GAME_FAILED_TO_GET_USER_ID = "Could not identify user!";


    const ERROR_GAME_FAILED_TO_ADD_USER = "Der Spieler konnte nicht hinzugefügt werden";

    protected $prgElementLogin;

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("tournament");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;

        // add player to Tournament
        $this->registerPostSessionVariable(self::FORM_FORM_ACTION);
        $this->registerPostSessionVariable(self::FORM_INPUT_PLAYER);
        $this->registerPostSessionVariable(self::FORM_INPUT_PARTNER);
        $this->registerPostSessionVariable(self::FORM_INPUT_DISCIPLIN);

        // add Torunament
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_NAME);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_PLACE);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_LINK);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_CLASSIFICATION);
        $this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE);

        $this->tools = new Tools();

    }



    public function processPost() {

        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        $isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if (!$this->prgElementLogin->isUserLoggedIn()) {
            return;
        }

        if ($this->issetPostVariable(self::FORM_FORM_ACTION)) {
            $loginAction = strval(trim($this->getPostVariable(self::FORM_FORM_ACTION)));
            if ($loginAction === self::FORM_GAME_ACTION_INSERT_PLAYERS) {
                $this->processPostInsertPlayerToTournament();
            } else if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_INSERT_TOURNAMENT)) {
                $this->processPostInsertTournament();
            } else if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_UPDATE_TOURNAMENT)) {
                $this->processPostUpdateTournament();
            }
        }


    }

    private function getSerializeArray($var){
        return serialize($var);
    }

    /**
        INSERT TOURNAMENT
    */
    private function processPostInsertTournament() {
        if (
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_NAME) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_PLACE) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_LINK) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_CLASSIFICATION) &&
        !$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE)) {
            $this->setFailedMessage("Bitte füllen Sie das Formular aus");
            return;
        }

        $name           = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_NAME);
        $place          = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_PLACE);
        $startdate      = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE)));
        $enddate        = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE)));
        $deadline       = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE)));
        $link           = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_LINK);

        $classification = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_CLASSIFICATION));
        $additionalClassification = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ADDITION_CLASSIFICATION);
        if(isset($additionalClassification) && strpos($additionalClassification, ',')) {
          $additionalClassification = explode(",", $additionalClassification);
        }
        $additionalClassification = $this->getSerializeArray($additionalClassification);
        $discipline     = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE));

        $reporterId     = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_REPORTER_ID);
        $tournamentType = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_TYPE);


        // insert tournament
        $this->brdb->insertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        // insert class
        $this->setSuccessMessage("Turnier \"". $name ."\" wurde hinzugefügt.");
        return;
    }

    /** Insert player to Tournament
      *
      */
    public function processPostInsertPlayerToTournament() {
        // Check that all information has been posted
        if ($this->issetPostVariable(self::FORM_INPUT_PLAYER) &&
            $this->issetPostVariable(self::FORM_INPUT_PARTNER) &&
            $this->issetPostVariable(self::FORM_INPUT_DISCIPLIN)) {

            $id = $this->tools->get("id");

            $player        = $this->getPostVariable(self::FORM_INPUT_PLAYER);
            $partner       = $this->getPostVariable(self::FORM_INPUT_PARTNER);
            $disziplin     = $this->getPostVariable(self::FORM_INPUT_DISCIPLIN);

            $reporterId = $this->prgElementLogin->getLoggedInUser()->getID();

            if(empty(array_filter($player))) {
                $this->setFailedMessage(self::ERROR_GAME_FAILED_TO_ADD_USER);
                return;
            }

            foreach ($player as $key => $value) {
                $tmp_disziplin = isset($disziplin[$key]) ? $disziplin[$key] : '';
                $tmp_partner   = isset($partner[$key]) && is_numeric($partner[$key]) ? $partner[$key] : 0;
                // check if id and partner exists
                $resData = $this->brdb->getTournamentPlayerByData($id, $value, $tmp_partner, $tmp_disziplin);
                if ($this->brdb->hasError()) {
                    $this->setFailedMessage($this->brdb->getError());
                    return;
                }

                if ($resData->num_rows > 0) {
                  $this->setFailedMessage("Spieler/Paarung schon vorhanden.");
                  return;
                }

                // check player p1

                $resP1 = $this->brdb->selectUserById($value);
                $p1    = $resP1->fetch_assoc();

                if (! $this->checkPlayerAndDisciplin($p1, $tmp_disziplin, 1)) {
                  $this->setFailedMessage(sprintf("Falsche Diziplin für Spieler %s %s", $p1['firstName'], $p1['lastName']));
                  return;
                }

                // check player p2
                if($tmp_partner > 0) {
                    $resP2 = $this->brdb->selectUserById($tmp_partner);
                    $p2    = $resP2->fetch_assoc();
                    if (! $this->checkPlayerAndDisciplin($p2, $tmp_disziplin, 2)) {
                      $this->setFailedMessage(sprintf("Falsche Diziplin für Spieler %s %s", $p2['firstName'], $p2['lastName']));
                      return;
                    }
                }



                $this->brdb->insertPlayerToTournament($id, $value, $tmp_partner, $tmp_disziplin, $reporterId);
                if ($this->brdb->hasError()) {
                    $this->setFailedMessage($this->brdb->getError());
                    return;
                }
            }

            $this->setSuccessMessage("Der Spieler/Die Paarung wurde eingetragen");
            $this->tools->customRedirect(array(
              'page'   => 'rankingTournament.php',
              'action' => 'details',
              'id'     => $id,

            ));
        } else {
            $this->setFailedMessage(self::ERROR_GAME_MISSING_INFORMATION);
        }
    }

    /**
      *
      */
    private function checkPlayerAndDisciplin($player, $discipline, $first = 1) {
      if ((!isset($player) && !is_array($player)) || ($first != 1 && $first != 2)) {
        return 0;
      }

      $discipline = substr($discipline, 0, strpos($discipline, " "));

      switch ($discipline) {
        case 'HE':
        case 'JE':
        case 'HD':
        case 'JD':
            return $player['gender'] == 'Male' ? 1 : 0;
          break;

        case 'DE':
        case 'ME':
        case 'DD':
        case 'MD':
          return $player['gender'] == 'Female' ? 1 : 0;
          break;

        case 'GD':
          return ($first == 1 && $player['gender'] == 'Male') ? 1 : ($first == 2 && $player['gender'] == 'Female' ? 1 : 0);

        default:
          return 0;
          break;
      }
    }

    /**
        Delete Player from Tournament
    */
    public function deletePlayersFromTournamentId($tournamentId, $playerId) {
        // player data
        $tmp = $this->brdb->getPlayerFromTournamentById($playerId)->fetch_assoc();
        $actUser = $this->prgElementLogin->getLoggedInUser();
        if (! $actUser->isAdmin() &&
            ! $actUser->isReporter() &&
            ($tmp['partnerID'] != $actUser->userId && $tmp['playerID'] != $actUser->userId))
        {
            $this->setFailedMessage("Nicht genug Rechte: Der Spieler konnte nicht gelöscht werden");
            $url = $this->tools->linkTo(array(
              'page'   => 'rankingTournament.php',
              'action' => 'details',
              'id'     => $tournamentId,
            ))
            return;
        }

        // inform reporter
        $sql = $this->brdb->getTournamentData($tournamentId);
        $row = $sql->fetch_assoc();

        // inform reporterId
        if($this->isPast($row['deadline']) && $row['reporterId'] > 0) {
          // user mail
          $res = $this->brdb->selectUserById($row['reporterId']);
          $user = $res->fetch_assoc();





          // send to partner
          if(isset($tmp['partnerID']) && $tmp['partnerID'] > 0) {
            $res       = $this->brdb->selectUserById($tmp['partnerID']);
            $partner   = $res->fetch_assoc();
            $mail      = $user['email'];
            $subject   = sprintf("Abmeldung %s nach Deadline", $row['name']);
            $content   = sprintf("Der Spieler %s %s wurde nach Meldeschluss durch %s abgemeldet", $partner['firstName'], $partner['lastName'], $actUser->getFullName());
            $preehader = $content;

            // send
            $this->tools->sendMail($mail, $subject, $preheader, $content);
            unset($res, $partner, $mail, $subject, $preheader, $content);
          }

          $res    = $this->brdb->selectUserById($tmp['playerID']);
          $player = $res->fetch_assoc();

          // def. receiver, subject and content
          $mail      = $user['email'];
          $subject   = sprintf("Abmeldung %s nach Deadline", $row['name']);
          $content   = sprintf("Der Spieler %s %s wurde nach Meldeschluss durch %s abgemeldet abgemeldet", $player['firstName'], $player['lastName'], $actUser->getFullName());
          $preheader = $content;

          // send
          $this->tools->sendMail($mail, $subject, $preheader, $content);
        }

        $res = $this->brdb->deletePlayersFromTournamentId($tournamentId, $playerId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage("Der Spieler konnte nicht gelöscht werden");
        }



        $this->setSuccessMessage("Der Spieler wurde aus dem Turnier gelöscht");

        $this->tools->customRedirect(array(
          'page' => 'rankingTournament.php',
          'action' => 'details',
          'id'   => $tournamentId,
        ));
        return;
    }

    private function isToday($time) {
        return (strtotime($time) === strtotime('today'));
    }

    function isPast($time) {
        return (strtotime($time) < time());
    }

    function isFuture($time) {
        return (strtotime($time) > time());
    }


    /**
     * Returns the id of the suer for a given full name
     * @param BrankDB $brdb the brdb to be used for this function
     * @param $string $fullName the full name as a string
     * @return the user id as integer or 0 in case the suer was not found -1 in case of error
     */
    private function getUserIdByFullName($fullName) {
        $res = $this->brdb->getUserIdByFullName($fullName);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage(self::ERROR_GAME_FAILED_TO_GET_USER_ID . " " . $fullName);
            return -1;
        }
        $dataSet = $res->fetch_assoc();
        if ($dataSet) {
            return intval($dataSet[User::USER_CLM_ID]);
        }
        return 0;
    }



    /**
     * This post method just rpocesses if the admin match id is set.
     * If it is the emthod asks the DB for a given game and reads it.
     * It also stores the game information into the session, hence the
     * insert game page will show the details.
     */
    public function processPostUpdateTournament() {
        $id = $this->tools->get("id");
        $delete = intval(trim($this->getPostVariable(self::FROM_TOURNAMENT_DELETE_STATUS)));
        if (isset($id) && isset($delete) && $delete == 1) {
          // delete Players
          $this->brdb->deleteAllPlayersFromTournamentById($id);
          if($this->brdb->hasError()) {
            $this->setFailedMessage("Turnier konnte nicht gelöscht werden (1/3).");
            return;
          }

          // delete Tournament
          $this->brdb->deleteTournamentById($id);
          if($this->brdb->hasError()) {
            $this->setFailedMessage("Turnier konnte nicht gelöscht werden (3/3).");
            return;
          }

          $this->setSuccessMessage("Turnier wurde gelöscht");
          return;
        }


        $name      = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_NAME);
        $place     = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_PLACE);
        $startdate = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE)));
        $enddate   = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE)));
        $deadline  = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE)));
        $link      = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_LINK);

        $classification = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_CLASSIFICATION));
        $additionalClassification = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ADDITION_CLASSIFICATION);
        if(isset($additionalClassification) && strpos($additionalClassification, ",")) {
          $additionalClassification = explode(",", $additionalClassification);
        } else {
          $additionalClassification = array($additionalClassification);
        }
        $additionalClassification = $this->getSerializeArray($additionalClassification);
        #die($additionalClassification);

        $discipline     = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE));
        $reporterId     = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_REPORTER_ID);
        $tournamentType = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_TYPE);

        // lat  & long
        $address   = $place .", Deutschland"; // Google HQ
        $latlng    = $this->tools->getGoogleLatAndLng($address);
        $latitude  = $latlng['lat'];
        $longitude = $latlng['lng'];

        $this->brdb->updateTournamentById($id, $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude);
        if($this->brdb->hasError()) {
          $this->setFailedMessage("Turnier konnte nicht aktualisiert werden.");
          return;
        }

        $this->setSuccessMessage("Turnier wurde geändert.");
        return;
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        if($this->issetGetVariable("action")) {
            $action = $this->getGetVariable("action");
            if ($action == "deletePlayer" && $this->issetGetVariable("id") && $this->issetGetVariable("tournamentPlayerId")) {
                $tournamentId = $this->getGetVariable("id");
                $playerId     = $this->getGetVariable("tournamentPlayerId");
                if(is_numeric($tournamentId) && is_numeric($playerId)) {
                    $this->deletePlayersFromTournamentId($tournamentId, $playerId);
                }
            }
        }

    }
}
?>
