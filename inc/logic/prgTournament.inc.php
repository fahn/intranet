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
include_once $_SERVER['BASE_DIR'] .'/inc/db/user.inc.php';
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
        $this->setSuccessMessage("Turnier \"". $name ."\" wurde hinzugefügt.");
        $this->tools->customRedirect(array('page' => 'rankingTournament.php'));
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
            ));
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
            $name      = $partner['firstName'] ." ". $partner['lastName'];
            $content   = sprintf("Der Spieler %s wurde nach Meldeschluss durch %s abgemeldet", $name, $actUser->getFullName());
            $preehader = $content;

            // send
            $this->tools->sendMail($mail, $name, $subject, $preheader, $content);
            unset($res, $partner, $mail, $name, $subject, $preheader, $content);
          }

          $res    = $this->brdb->selectUserById($tmp['playerID']);
          $player = $res->fetch_assoc();

          // def. receiver, subject and content
          $mail      = $user['email'];
          $subject   = sprintf("Abmeldung %s nach Deadline", $row['name']);
          $content   = sprintf("Der Spieler %s %s wurde nach Meldeschluss durch %s abgemeldet abgemeldet", $player['firstName'], $player['lastName'], $actUser->getFullName());
          $preheader = $content;

          // send
          $this->tools->sendMail($mail, $row['name'], $subject, $preheader, $content);
        }

        $res = $this->brdb->deletePlayersFromTournamentId($tournamentId, $playerId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage("Der Spieler konnte nicht gelöscht werden");
        }



        $this->setSuccessMessage("Der Spieler wurde aus dem Turnier gelöscht");

        $this->tools->customRedirect(array(
          'page'   => 'rankingTournament.php',
          'action' => 'details',
          'id'     => $tournamentId,
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
          $this->tools->customRedirect(array('page' => 'rankingTournament.php'));
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
        $this->tools->customRedirect(array('page' => 'rankingTournament.php', 'action' => 'details', 'id' => $id));
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
            switch ($action) {
              case 'deletePlayer':
                if($this->issetGetVariable("id") && $this->issetGetVariable("tournamentPlayerId")) {
                  $this->deletePlayersFromTournamentId($this->getGetVariable("id"), $this->getGetVariable("tournamentPlayerId"));
                }
                break;
              case 'create_backup':

                if ($this->createBackup($this->getGetVariable("id"))) {
                  $this->setFailedMessage("Backup konnte nicht erstellt werden");
                } else {
                    $this->setSuccessMessage("Backup wurde erstellt");
                }
                $this->tools->customRedirect(array(
                  'page'   => 'rankingTournament.php',
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




              default:
                # code...
                break;
            }
        }

    }

    /** Export tool
      *
      */
    private function export($id) {
        if(!isset($id) || !is_numeric($id) || $id < 1) {
          return false;
        }

        if (
          $this->prgElementLogin->getLoggedInUser()->isAdmin() ||
          $this->prgElementLogin->getLoggedInUser()->isReporter()
        ) {
            #session_write_close();
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
                $this->reportDefault($tournament, $id);
                break;
            }

            // kill it
            exit(0);


        }
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
            $gender = $row['p1Gender'] == 'Male' ? 'm' : 'w';
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
                $row['p1ClubNumber'],
                $counter,
                '',
                '',
            );
            $writer->addRow($singleRow); // add a row at a time


            if($add == True) {
                if($row['p2FirstName'] != NULL AND $row['p2LastName'] != NULL) {
                    $firstName = $row['p2FirstName'];
                    $lastName  = $row['p2LastName'];
                    $gender    = $row['p2Gender'] == 'Male' ? 'm' : 'w';

                    if(!(int) $row['p2Bday'] != 0) {
                        $bday = '';
                    } else {
                        $due       = strtotime($row['p2Bday']);
                        $bday      = date("d.m.Y", $due);
                        $bday      = $bday == "01.01.1970" ? "" : $bday;
                    }
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
                    $row['p2ClubNumber'],
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


    private function reportDefault($tournament, $id) {
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

        $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
        while($row = $players->fetch_assoc()) {
          $singleRow = array(
            $row['p1FirstName'],
            $row['p1LastName'],
            $gender,
            $row['p1ClubName'],
            $row['p1ClubAssociation'],
            $row['p1PlayerNumber'],
            $bday,
            $row['p1ClubNumber'],
            $counter,
          );
          $writer->addRow($singleRow);
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
                    'playerID'       => $row['playerID'],
                    'partnerID'      => $row['partnerID'],
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
