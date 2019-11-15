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

include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/model/user.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';


# export
include_once $_SERVER['BASE_DIR'] .'/vendor/autoload.php';
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;


/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementTournament extends APrgPatternElement {

    private $brdb;
    private $tools;

    private $page = 'tournament.php';

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
    const FORM_INPUT_TOURNAMENT_DESCRIPTION    = "description";


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
        // check if user is login
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        if(! $this->issetPostVariable(self::FORM_FORM_ACTION)) {
            $this->setFailedMessage("no form");
            return;
        }
        $loginAction = strval(trim($this->getPostVariable(self::FORM_FORM_ACTION)));

        // INSERT PLAYER TO Tournament
        if ($loginAction == self::FORM_GAME_ACTION_INSERT_PLAYERS) {
                $this->processPostInsertPlayerToTournament();
                return;
        }

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array('reporter', 'admin'), 'or');

        switch ($loginAction) {
            case self::FORM_GAME_ACTION_INSERT_TOURNAMENT:
                $this->processPostInsertTournament();
                break;

            case  $loginAction === self::FORM_GAME_ACTION_UPDATE_TOURNAMENT:
                $this->processPostUpdateTournament();
                break;

            default:
                return;
                break;
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
        if(isset($additionalClassification) && strpos($additionalClassification, ",")) {
            $additionalClassification = explode(",", $additionalClassification);
        } else {
            $additionalClassification = array($additionalClassification);
        }
        $additionalClassification = $this->getSerializeArray($additionalClassification);

        $discipline     = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE));
        $reporterId     = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_REPORTER_ID);
        $tournamentType = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_TYPE);

        // lat  & long
        $address   = $place .", Deutschland"; // Google HQ
        $latlng    = $this->tools->getGoogleLatAndLng($address);
        $latitude  = $latlng['lat'];
        $longitude = $latlng['lng'];

        // Description
        $description = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DESCRIPTION);


        // insert tournament
        $this->brdb->insertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType,  $latitude, $longitude, $description);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        // insert class
        $this->setSuccessMessage(sprintf("Turnier %s wurde hinzugefügt.", $name));
        $this->tools->customRedirect(array('page' => $this->page));
    }

    /** Insert player to Tournament
      *
      */
    private function processPostInsertPlayerToTournament() {
        // Check that all information has been posted
        if (! $this->issetPostVariable(self::FORM_INPUT_PLAYER) ||
            ! $this->issetPostVariable(self::FORM_INPUT_DISCIPLIN)) {
                $this->setFailedMessage(self::ERROR_GAME_MISSING_INFORMATION);
                return;
            }

        $id = $this->tools->get("id");

        $player        = $this->getPostVariable(self::FORM_INPUT_PLAYER);
        $partner       = $this->getPostVariable(self::FORM_INPUT_PARTNER);
        $disziplin     = $this->getPostVariable(self::FORM_INPUT_DISCIPLIN);

        $reporterId = $this->prgElementLogin->getLoggedInUser()->getID();

        $tmp_disziplin = isset($disziplin) ? $disziplin : '';

        $tmp_partner   = isset($partner) && is_numeric($partner) ? $partner : 0;
        // check if id and partner exists
        $resData = $this->brdb->getTournamentPlayerByData($id, $player, $tmp_partner, $tmp_disziplin);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        if ($resData->num_rows > 0) {
          $this->setFailedMessage("Spieler/Paarung schon vorhanden.");
          return;
        }

        // check player p1
        $resP1 = $this->brdb->selectPlayerById($player);
        $p1    = $resP1->fetch_assoc();
        
        if (! $this->checkPlayerAndDisciplin($p1, $tmp_disziplin, 1)) {
          $this->setFailedMessage(sprintf("Falsche Diziplin für Spieler %s %s", $p1['firstName'], $p1['lastName']));
          return;
        }

        // check player p2
        if($tmp_partner > 0) {
            $resP2 = $this->brdb->selectPlayerById($tmp_partner);
            $p2    = $resP2->fetch_assoc();
            if (! $this->checkPlayerAndDisciplin($p2, $tmp_disziplin, 2)) {
              $this->setFailedMessage(sprintf("Falsche Diziplin für Spieler %s %s", $p2['firstName'], $p2['lastName']));
              return;
            }
        }


        $this->brdb->insertPlayerToTournament($id, $player, $tmp_partner, $tmp_disziplin, $reporterId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }


        $this->setSuccessMessage("Der Spieler/Die Paarung wurde eingetragen");
        $this->tools->customRedirect(array(
          'page'   => $this->page,
          'action' => 'details',
          'id'     => $id,
        ));
    }

    /**
      *
      */
    private function checkPlayerAndDisciplin($player, $discipline, $first = 1) {

      if ((!isset($player) && !is_array($player)) || ($first != 1 && $first != 2)) {
        return 0;
      }
      
      $discipline = explode(" ", $discipline);
      
      switch ($discipline[0]) {
        case 'HE':
        case 'JE':
        case 'HD':
        case 'JD':
            return $player['gender'] == 'Male' ? true : false;
          break;

        case 'DE':
        case 'ME':
        case 'DD':
        case 'MD':
          return $player['gender'] == 'Female' ? true : false;
          break;

        case 'GD':
          return ($first == 1 && $player['gender'] == 'Male') ? 1 : ($first == 2 && $player['gender'] == 'Female' ? true : false);

        default:
          return 0;
          break;
      }
    }

    /**
        Delete Player from Tournament
    */
    private function deletePlayersFromTournamentId($tournamentId, $playerId) {
        // player data
        $tmp = $this->brdb->getPlayerFromTournamentById($playerId)->fetch_assoc();
        $actUser = $this->prgElementLogin->getLoggedInUser();
        if (! $actUser->isAdmin() &&
            ! $actUser->isReporter() &&
            $tmp['reporterId'] != $actUser->userId)
        {
            $this->setFailedMessage("Nicht genug Rechte: Der Spieler konnte nicht gelöscht werden");
            $url = $this->customRedirect(array(
              'page'   => $this->page,
              'action' => 'details',
              'id'     => $tournamentId,
            ));
            return;
        }


        // inform reporter
        $sql = $this->brdb->getTournamentData($tournamentId);
        $row = $sql->fetch_assoc();

        // inform reporterId
        if($this->isPast($row['deadline']) && $row['reporterId'] > 0) {
          // SEND MAIL
          /// reporter
            $this->informUser($row['reporterId']);
          /// player
            $this->informUser($row['playerId']);
          /// partner
          if(isset($tmp['partnerId']) && $tmp['partnerId'] > 0) {
              $this->informUser($tmp['partnerId']);
          }
        }


        $res = $this->brdb->deletePlayersFromTournamentId($tournamentId, $playerId);

        if ($this->brdb->hasError()) {
            $this->setFailedMessage("Der Spieler konnte nicht gelöscht werden");
        } else {
            $this->setSuccessMessage("Der Spieler wurde aus dem Turnier gelöscht");
        }

        $this->tools->customRedirect(array(
          'page'   => $this->page,
          'action' => 'details',
          'id'     => $tournamentId,
        ));
    }

    private function informUser($userId = null) {
        if ($userId > 0) {
            $res       = $this->brdb->selectPlayerById($userId);
            $partner   = $res->fetch_assoc();
            $mail      = $user['email'];
            if (! filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
            $subject   = sprintf("Abmeldung %s nach Deadline", $row['name']);
            $name      = $partner['firstName'] ." ". $partner['lastName'];
            $content   = sprintf("Der Spieler %s wurde nach Meldeschluss durch %s abgemeldet", $name, $actUser->getFullName());
            $preehader = $content;

            // send
            return $this->tools->sendMail($mail, $name, $subject, $preheader, $content);
        }
        return false;
    }

    // TODO: move to tools
    private function isToday($time) {
        return (strtotime($time) === strtotime('today'));
    }

    // TODO: move to tools
    private function isPast($time) {
        return (strtotime($time) < time());
    }

    // TODO: move to tools
    private function isFuture($time) {
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
    private function processPostUpdateTournament() {
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
          $this->tools->customRedirect(array('page' => $this->page));
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

        $discipline     = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE));
        $reporterId     = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_REPORTER_ID);
        $tournamentType = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_TYPE);

        // lat  & long
        $address   = $place .", Deutschland"; // Google HQ
        $latlng    = $this->tools->getGoogleLatAndLng($address);
        $latitude  = $latlng['lat'];
        $longitude = $latlng['lng'];

        // Description
        $description = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DESCRIPTION);

        $this->brdb->updateTournamentById($id, $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description);
        if($this->brdb->hasError()) {
          $this->setFailedMessage("Turnier konnte nicht aktualisiert werden.");
          return;
        }

        $this->setSuccessMessage("Turnier wurde geändert.");
        $this->tools->customRedirect(array('page' => $this->page, 'action' => 'details', 'id' => $id));
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        if($this->issetGetVariable("action")) {
            $action = $this->getGetVariable("action");
            $id = $this->getGetVariable("id");

            switch ($action) {
                case 'deletePlayer':
                    if($this->issetGetVariable("id") && $this->issetGetVariable("tournamentPlayerId")) {
                      $this->deletePlayersFromTournamentId($this->getGetVariable("id"), $this->getGetVariable("tournamentPlayerId"));
                    }
                    break;

                case 'create_backup':
                    if ($this->createBackup($id)) {
                      $this->setFailedMessage("Backup konnte nicht erstellt werden");
                    } else {
                        $this->setSuccessMessage("Backup wurde erstellt");
                    }
                    $this->tools->customRedirect(array(
                      'page'   => $this->page,
                      'action' => 'backup',
                      'id'     => $id,
                    ));
                    break;

                case 'export':
                    // create backup
                    $this->createBackup($this->getGetVariable("id"));
                    // create xls
                    $this->export($this->getGetVariable("id"));
                    break;

                case 'unlock':
                    $this->unlockPlayerFromTournament();
                    break;

                case 'lock':
                    $this->lockPlayerFromTournament();
                    break;

                default:
                    # code...
                    break;
            }
        }
    }


    private function unlockPlayerFromTournament() {
        $id = $this->getGetVariable("id");
        if (!isset($id) || !is_numeric($id)) {
            $this->setFailedMessage("Unlocked failed");
            return false;
        }
        $playerId = $this->getGetVariable("tournamentPlayerId");
        if (!isset($playerId) || !is_numeric($playerId)) {
            $this->brdb->unlockAllPlayerFromTournament($id);
        } else {
            $this->brdb->unlockPlayerFromTournament($id, $playerId);
        }

        if ($this->brdb->hasError()) {
          $this->setFailedMessage("Spieler/Spieler konnte/konnten nicht unlocked werden");
          return;
        }

        $this->setSuccessMessage("Spieler wurde geunlocked");
        $this->tools->customRedirect(array('page' => $this->page, 'action' => 'details', 'id' => $id));
    }

    private function lockPlayerFromTournament() {
        $id = $this->getGetVariable("id");
        $playerId = $this->getGetVariable("tournamentPlayerId");
        if (!isset($id) || !is_numeric($id) || !isset($playerId) || !is_numeric($playerId)) {
            $this->setFailedMessage("locked failed");
            return false;
        }
        $this->brdb->lockPlayerFromTournament($id, $playerId);
        if ($this->brdb->hasError()) {
          $this->setFailedMessage("Spieler/Spieler konnte/konnten nicht locked werden");
          return;
        }

        $this->setSuccessMessage("Spieler wurde als gemeldet gesetzt.");
        $this->tools->customRedirect(array('page' => $this->page, 'action' => 'details', 'id' => $id));
    }

    /** Export tool
      *
      */
    private function export($id) {
        if(!isset($id) || !is_numeric($id) || $id < 1) {
          return false;
        }

        if (
          ! $this->prgElementLogin->getLoggedInUser()->isAdmin() ||
          ! $this->prgElementLogin->getLoggedInUser()->isReporter()
        ) {
          return;
        }

        ob_end_flush();
        if(isset($this->smarty)) {
          unset($this->smarty);
        }
        header_remove();

        // get tournament data
        $tournament = $this->brdb->getTournamentData($id)->fetch_assoc();

        switch ($tournament['tournamentType']) {
          case 'NBV':
            $this->reportExcelNBV($tournament, $id);
            break;

          default:
            $this->exportDefault($tournament, $id);
            break;
        }

        // kill it
        exit(0);

      }

    private function reportExcelNBV($tournament, $id) {
        if(isset($tournament['name']) && $tournament['deadline']) {
            $fileName   = sprintf("%s_%d.xlsx", addslashes($tournament['name']), date("d.m.Y", strtotime($tournament['deadline'])));
        } else {
            $fileName = "random.xlsx";
        }

        $writer     = WriterFactory::create(Type::XLSX); // for XLSX files

        //$writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName); // stream data directly to the browser



        // create sheets
        $einzel = $writer->getCurrentSheet();
        $einzel->setName('Einzel');
        $singleDataRow = array(
            'Vorname',
            'Name',
            'm/w',
            'Verein',
            'Verb',
            'Einzel',
            'Doppel',
            'Mixed',
            'ERP',
            'DRP',
            'MRP',
            'SBNr',
            'GebDat',
            'VereinsNr',
            'Rangfolge',
            'Quote',
            'gemeldet von',
        );
        $writer->addRow($singleDataRow);

        // create double sheets
        $doppel = $writer->addNewSheetAndMakeItCurrent();
        $doppel->setName('Doppel');
        $writer->addRow($singleDataRow);
    // create mx sheets
        $mixed = $writer->addNewSheetAndMakeItCurrent();
        $mixed->setName('Mixed');
        $writer->addRow($singleDataRow);

        $einzelCount = 0;
        $mixedCount  = 0;
        $doppelCount = 0;


        $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
        while($row = $players->fetch_assoc()) {
            /* MIXED */
            if(strpos($row['classification'], 'GD') !== false) {
                $writer->setCurrentSheet($mixed);
                $add       = True;
                $rowEinzel = '';
                $rowDoppel = '';
                $rowMixed  = $row['classification'];
                $counter = ++$mixedCount;

            /* Doppel */
            } else if(strpos($row['classification'], 'DD') !== false || strpos($row['classification'], 'HD') !== false ||
                      strpos($row['classification'], 'JD') !== false || strpos($row['classification'], 'MD') !== false ) {
                $writer->setCurrentSheet($doppel);
                $add       = True;
                $rowEinzel = '';
                $rowDoppel = $row['classification'];
                $rowMixed  = '';
                $counter   = ++$doppelCount;

            /* Einzel */
            } else {
                $writer->setCurrentSheet($einzel);
                $add       = False;
                $rowEinzel = $row['classification'];
                $rowDoppel = '';
                $rowMixed  = '';
                $counter   = ++$einzelCount;
            }

            // add Player 1
            $bday   = date("d.m.Y", strtotime($row['p1Bday']));
            $bday   = $bday == "01.01.1970" ? "" : $bday;
            $gender = $this->getGenderShortTag($row['p1Gender']);
            $singleRow = array(
                $row['p1FirstName'],
                $row['p1LastName'],
                $gender,
                $row['p1ClubName'],
                $row['p1ClubAssociation'],
                $rowEinzel,
                $rowDoppel,
                $rowMixed,
                '',
                '',
                '',
                $row['p1PlayerNumber'],
                $bday,
                $row['p1ClubNr'],
                $counter,
                '',
                '',
            );
            $writer->addRow($singleRow); // add a row at a time


            if($add) {
                if($row['p2FirstName'] != NULL && $row['p2LastName'] != NULL) {
                    $firstName = $row['p2FirstName'];
                    $lastName  = $row['p2LastName'];
                    $gender    = $this->getGenderShortTag($row['p2Gender']);

                    $bday = $this->getBday($row['bday']);

                } else {
                    $firstName = "FREIMELDUNG";
                    $lastName  = "";
                    $gender    = "";
                    $bday      = "";
                }

                $verein      = isset($row['p2ClubName'])         ? $row['p2ClubName']     : '';
                $association = isset($row['p2ClubAssociation'])  ? $row['p2ClubAssociation']  : '';
                $sbnr        = isset($row['p2PlayerNumber'])     ? $row['p2PlayerNumber'] : '';

                $singleRow = array(
                    $firstName,
                    $lastName,
                    $gender,
                    $verein,
                    $association,
                    $rowEinzel,
                    $rowDoppel,
                    $rowMixed,
                    '',
                    '',
                    '',
                    $sbnr,
                    $bday,
                    $row['p2ClubNr'],
                    $counter,
                    '',
                    '',
                );
                $writer->addRow($singleRow); // add a row at a time
            }
        }
        //$writer->addRows($multipleRows); // add multiple rows at a time
        $writer->close();

    }

    private function getBday($bday) {
        if ($bday == null) {
            return null;
        }
        $due       = strtotime($bday);
        $bday      = date("d.m.Y", $due);
        return $bday == "01.01.1970" ? "" : $bday;
    }

    private function getGenderShortTag($gender) {
        return $gender == 'Male' ? 'm' : 'w';
    }


    private function exportDefault($tournament, $id) {
        if(isset($tournament['name']) && $tournament['deadline']) {
            $fileName   = sprintf("%s_%d.xlsx", addslashes($tournament['name']), date("d.m.Y", strtotime($tournament['deadline'])));
        } else {
            $fileName = "random.xlsx";
        }

        $writer     = WriterFactory::create(Type::XLSX); // for XLSX files


        //$writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName); // stream data directly to the browser

        // create sheets
        $einzel = $writer->getCurrentSheet();
        $einzel->setName('Spieler');

        $headerRow = array(
            'Vorname',
            'Name',
            'm/w',
            'Verein',
            'Verb',
            'Disziplin',
            'SBNr',
            'GebDat',
            'VereinsNr',
            'Rangfolge',
        );
        $writer->addRow($headerRow);


        $counter = 0;

        $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
        while($row  = $players->fetch_assoc()) {
            $gender = $this->getGenderShortTag($row['p1Gender']);
            $bday   = $this->getBday($row['p1Bday']);

            $singleRow = array(
                $row['p1FirstName'],
                $row['p1LastName'],
                $gender,
                $row['p1ClubName'],
                $row['p1ClubAssociation'],
                $row['classification'],
                $row['p1PlayerNumber'],
                $bday,
                $row['p1ClubNr'],
                ++$counter,
            );

            $writer->addRow($singleRow);

            if ($row['p2FirstName'] != null) {
                $singleRow = array(
                    $row['p2FirstName'],
                    $row['p2LastName'],
                    $this->getGenderShortTag($row['p2Gender']),
                    $row['p2ClubName'],
                    $row['p2ClubAssociation'],
                    $row['classification'],
                    $row['p2PlayerNumber'],
                    $this->getBday($row['p2Bday']),
                    $row['p2ClubNr'],
                    $counter,
                );
                $writer->addRow($singleRow);
            }
        }

        $writer->close();
    }

    /** Create a backup
      *
      */
    private function createBackup($id) {
        $res = $this->brdb->getPlayersByTournamentId($id);
        $backup = array();
        if($res) {
            while($row = $res->fetch_assoc()) {
                $backup[substr($row['classification'], 0, 2)][] = array(
                    'playerId'       => $row['playerId'],
                    'partnerId'      => $row['partnerId'],
                    'classification' => $row['classification'],
                );
            }
            if(isset($backup) && is_array($backup) && count($backup) > 0) {
                $backupS = serialize($backup);
                if(! $this->brdb->insertTournamentBackup($id, $backupS)) {
                  return false;
                }
                return true;
            }
        }
        return false;
    }
}
?>
