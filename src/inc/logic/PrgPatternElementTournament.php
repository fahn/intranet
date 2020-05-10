<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
namespace Badtra\Intranet\Logic;
require_once "PrgPattern.php";

// export
require_once "./vendor/autoload.php";

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class PrgPatternElementTournament extends APrgPatternElement
{

    const __TABLE__ = "Tournament";

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

    protected PrgPatternElementLogin $prgElementLogin;

    private $page = "tournament.php";


    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("tournament");

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

    }



    public function processPost(): void
    {
        // check if user is login
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        if (! $this->issetPostVariable(self::FORM_FORM_ACTION)) {
            $this->setFailedMessage("no form");
            return;
        }
        $loginAction = strval(trim($this->getPostVariable(self::FORM_FORM_ACTION)));

        // INSERT PLAYER TO Tournament
        if ($loginAction == self::FORM_GAME_ACTION_INSERT_PLAYERS) {
                $this->processPostInsertPlayerToTournament();
                return;
        }

        /* ADMIN AREA */
        $this->prgElementLogin->redirectUserIfnoRights(array("reporter", "admin"), "or");

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

    private function getSerializeArray($var): string
    {
        return serialize($var);
    }

    /**
     * Insert Tournament
     *
     * @return boolean
     */
    private function processPostInsertTournament(): bool
    {

        try {

            $requireFields = array(self::FORM_INPUT_TOURNAMENT_NAME, self::FORM_INPUT_TOURNAMENT_PLACE, self::FORM_INPUT_TOURNAMENT_STARTDATE, self::FORM_INPUT_TOURNAMENT_ENDDATE, self::FORM_INPUT_TOURNAMENT_ENDDATE, self::FORM_INPUT_TOURNAMENT_DEADLINE, self::FORM_INPUT_TOURNAMENT_LINK, self::FORM_INPUT_TOURNAMENT_CLASSIFICATION, self::FORM_INPUT_TOURNAMENT_DISCIPLINE);
            if (! $this->prgElementLogin->checkRequiredFields($requireFields))
            {
                throw new \Exception("Informationen fehlen.");
            }
            print_r($_POST);
           
            $name           = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_NAME);
            $place          = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_PLACE);
            $startdate      = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE)));
            $enddate        = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE)));
            $deadline       = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE)));
            $link           = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_LINK);

            $classification = $this->getSerializeArray($this->getAllPostVariable(self::FORM_INPUT_TOURNAMENT_CLASSIFICATION));
            $additionalClassification = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ADDITION_CLASSIFICATION);
            if (isset($additionalClassification) && strpos($additionalClassification, ",")) {
                $additionalClassification = explode(",", $additionalClassification);
            } else {
                $additionalClassification = array($additionalClassification);
            }
            $additionalClassification = $this->getSerializeArray($additionalClassification);

            $discipline     = $this->getSerializeArray($this->getAllPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE));
            $reporterId     = $this->getPostVariableInt(self::FORM_INPUT_TOURNAMENT_REPORTER_ID);
            $tournamentType = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_TYPE);

            // lat  & long
            $address   = $place .", Deutschland"; // Google HQ
            $latlng    = $this->getGoogleLatAndLng($address);
            $latitude  = $latlng["lat"];
            $longitude = $latlng["lng"];

            // Description
            $description = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_DESCRIPTION);

            $tournament = new \Badtra\Intranet\Model\Tournament();
            $tournament->setName($name);
            $tournament->setPlace($place);
            $tournament->setStartdate($startdate);
            $tournament->setEndDate($enddate);
            $tournament->setDeadline($deadline);
            $tournament->setLink($link);
            $tournament->setClassification($classification);
            $tournament->setAdditionalClassification($additionalClassification);
            $tournament->setDiscipline($discipline);
            $tournament->setReporterId($reporterId);
            $tournament->setTournamentType($tournamentType);
            $tournament->setLatitude($latitude);
            $tournament->setLongitude($longitude);
            $tournament->setDescription($description);

            if (!$this->brdb->insertTournament($tournament))
            {
                throw new \Exception("Das Turnier konnte nicht angelegt werden");
            }

            // insert class
            $this->setSuccessMessage(sprintf("Turnier %s wurde hinzugefügt.", $name));
            $this->customRedirectArray(array("page" => $this->page));
            return true;

        } catch (\Exception $e) {
            $this->setFailedMessage("Es kam ein Fehler auf. Fehlermeldung liegt dem Admin vor.");
            $this->log($this->__TABLE__, "Cannot insert Tournament", $tournament, "");
            return false;
        }
    }

    /** Insert player to Tournament
      *
      */
    private function processPostInsertPlayerToTournament() {

        try {
            $requireFields = array(self::FORM_INPUT_PLAYER, self::FORM_INPUT_DISCIPLIN);
            if (! $this->prgElementLogin->checkRequiredFields($requireFields))
            {
                throw new \Exception("Daten sind fehlerhaft");
            }

            $id            = $this->getGetVariable("id");

            $player        = $this->getPostVariableInt(self::FORM_INPUT_PLAYER);
            $partner       = $this->getPostVariableInt(self::FORM_INPUT_PARTNER);
            $disziplin     = $this->getPostVariableInt(self::FORM_INPUT_DISCIPLIN);

            $reporterId = $this->prgElementLogin->getLoggedInUser()->getID();

            $tmp_disziplin = isset($disziplin) ? $disziplin : "";

            $tmp_partner   = isset($partner) && is_numeric($partner) ? $partner : 0;

            // check if id and partner exists
            $resData = $this->brdb->getTournamentPlayerByData($id, $player, $tmp_partner, $tmp_disziplin);
            if (isset($resData) && is_array($resData) && count($resData) > 0)
            {
                throw new \Exception("Spieler schon vorhanden");
            }


            // check player p1
            $p1 = $this->brdb->selectPlayerById($player);
            if (isset($tmp_partner) &&  $tmp_partner > 0 && ! $this->checkPlayerAndDisciplin($p1, $tmp_disziplin, 1))
            {
                throw new \Exception(sprintf("Falsche Diziplin für Spieler %s %s", $p1["firstName"], $p1["lastName"]));
            }

            // check player p2
            if (isset($tmp_partner) && $tmp_partner > 0)
            {
                $p2 = $this->brdb->selectPlayerById($tmp_partner);
                if (! $this->checkPlayerAndDisciplin($p2, $tmp_disziplin, 2))
                {
                    throw new \Exception(sprintf("Falsche Diziplin für Spieler %s %s", $p2["firstName"], $p2["lastName"]));
                }
            }


            if (!$this->brdb->insertPlayerToTournament($id, $player, $tmp_partner, $tmp_disziplin, $reporterId)) {
                $firstPlayer = sprintf("%s %s", $p1["firstName"], $p1["lastName"]);
                $secondPlayer = isset($tmp_partner) && $tmp_partner > 0 ? sprintf("%s %s", $p2["firstName"], $p2["lastName"]) : "";

                throw new \Exception(sprintf("Die Paarung konnte nicht eingetragen werden: %s %s", $firstPlayer, $secondPlayer));
            }


            $this->setSuccessMessage("Der Spieler/Die Paarung wurde eingetragen");
            $this->customRedirectArray(array(
                "page"   => $this->page,
                "action" => "details",
                "id"     => $id,
            ));
            return true;

        }
        catch (\Exception $e)
        {
            $this->setFailedMessage("Es kam ein Fehler auf. Fehlermeldung liegt dem Admin vor.");
            $this->log($this->__TABLE__, "Cannot insert Tournament", "", "DB-QUERY");
            return false;
        }
       
    }

    /**
      *
      */
    private function checkPlayerAndDisciplin(array $player, String $discipline, int $first = 1) {
        if ((!isset($player) && !is_array($player)) || ($first != 1 && $first != 2)) {
            return 0;
        }

        try {
            $discipline = explode(" ", $discipline);
        } catch (\Exception $e) {
            return 0;
        }

        switch ($discipline[0]) {
            case "HE":
            case "JE":
            case "HD":
            case "JD":
                return $player["gender"] == "Male" ? true : false;
            break;

            case "DE":
            case "ME":
            case "DD":
            case "MD":
            return $player["gender"] == "Female" ? true : false;
            break;

            case "GD":
            return ($first == 1 && $player["gender"] == "Male") ? 1 : ($first == 2 && $player["gender"] == "Female" ? true : false);

            default:
            return 0;
            break;
        }
        }

    /**
     * Delete player from tournament
     *
     * @param integer $tournamentId
     * @param integer $playerId
     * @return boolean
     */
    // @TODO
    private function _deletePlayersFromTournamentId(int $tournamentId,int $playerId): void
    {
        // player data
        $tmp = $this->brdb->getPlayerFromTournamentById($playerId);

        $actUser = $this->prgElementLogin->getLoggedInUser();
        if (! $actUser->isAdmin() &&
            ! $actUser->isReporter() &&
            $tmp["reporterId"] != $actUser->userId &&
            $actUser->getPlayerId()  != $tmp["playerNr"] &&
            $actUser->getPlayerId()  != $tmp["partnerNr"])
        {
            $this->setFailedMessage("Nicht genug Rechte: Der Spieler konnte nicht gelöscht werden");
           
            $this->customRedirectArray(array(
              "page"   => $this->page,
              "action" => "details",
              "id"     => $tournamentId,
            ));
        }


        // inform reporter
        $row = $this->brdb->getTournamentData($tournamentId);

        // inform reporterId
        if ($this->_isPast($row["deadline"]))
        {
            $playerArr = array();
            array_push($playerArr, $tmp["playerId"]);

            if (isset($tmp["partnerId"]) && $tmp["partnerId"] > 0)
            {
                array_push($playerArr, $tmp["partnerId"]);

                $this->informUser("P", $tmp["partnerId"], $playerArr, $row);
            }

            // inform player
            $this->informUser("P", $tmp["playerId"], $playerArr, $row);

            // inform reporter
            if (isset($row["reporterId"]) && $row["reporterId"] > 0) {
                $this->informUser("U", $row["reporterId"], $playerArr, $row);
            }
        }


        if ($this->brdb->deletePlayersFromTournamentId($tournamentId, $playerId))
        {
            $this->setSuccessMessage("Der/Die Spieler/Paarung wurde/n aus dem Turnier gelöscht");
        } else {
            $this->setFailedMessage("Der Spieler konnte nicht gelöscht werden");
        }


        $this->customRedirectArray(array(
          "page"   => $this->page,
          "action" => "details",
          "id"     => $tournamentId,
        ));
    }

   
    private function informUser(String $type, int $id, array $players, array $tournamentInfo) {
        if ($id < 1) {
            return false;
        }

        if ($type == "U") {
            // get Player Information
            if (isset($players) && is_array($players)) {
                $tmp = array();
                foreach ($players as $player) {
                    $playerData   = $this->brdb->selectPlayerById($player);
                    array_push($tmp, sprintf("%s %s", $playerData["firstName"], $playerData["lastName"]));
                }
                $playerString = implode(", ", $tmp);
                unset($tmp, $players, $player);
            }
           
            // get User Information
            $user   = $this->brdb->selectUserById($id);
            if (!isset($user["email"]) || !filter_var($user["email"], FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $actUser = $this->prgElementLogin->getLoggedInUser();
            $subject   = sprintf("Abmeldung %s (%s) nach Deadline", $tournamentInfo["name"], $tournamentInfo["place"]);
            $content   = sprintf("Der Spieler %s wurde nach Meldeschluss durch %s abgemeldet", $playerString, $actUser->getFullName());
            $preheader = $content;

            // send
            return $this->sendMail($user["email"], $user["name"], $subject, $preheader, $content);
        } else if ($type == "P") {
            $user   = $this->brdb->selectUserByPlayerId($id);
            return $user["userId"] > 0 ? $this->informUser("U", $user["userId"], $player, $tournamentInfo) : false;
        }

        return false;
        unset($userId, $name, $subject, $prehader, $content, $user);
    }
   


    private function processPostUpdateTournament()
    {
        $id = $this->getGetVariable("id");
        $delete = intval(trim($this->getPostVariable(self::FROM_TOURNAMENT_DELETE_STATUS)));

        if (isset($id) && isset($delete) && $delete == 1) {
            // delete Players
            if (!$this->brdb->deleteAllPlayersFromTournamentById($id)) {
                $this->setFailedMessage("Turnier konnte nicht gelöscht werden (1/3).");
                return;
            }

            // delete Tournament
            if (!$this->brdb->deleteTournamentById($id)) {
                $this->setFailedMessage("Turnier konnte nicht gelöscht werden (3/3).");
                return;
            }

            $this->setSuccessMessage("Turnier wurde gelöscht");
            $this->customRedirectArray(array("page" => $this->page));

            return;
        }


        $name      = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_NAME);
        $place     = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_PLACE);
        $startdate = $this->getDate($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE));
        $enddate   = $this->getDate($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE));
        $deadline  = $this->getDate($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE));
        $link      = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_LINK);
        $description = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_DESCRIPTION);
        $discipline     = $this->getSerializeArray($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DISCIPLINE));
        $reporterId     = $this->getPostVariableInt(self::FORM_INPUT_TOURNAMENT_REPORTER_ID);
        $tournamentType = $this->getPostVariableString(self::FORM_INPUT_TOURNAMENT_TYPE);

        $classification = $this->getSerializeArray($this->getAllPostVariable(self::FORM_INPUT_TOURNAMENT_CLASSIFICATION));
        $additionalClassification = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ADDITION_CLASSIFICATION);

        if (isset($additionalClassification) && strpos($additionalClassification, ",")) {
            $additionalClassification = explode(",", $additionalClassification);
        } else {
            $additionalClassification = array($additionalClassification);
        }
        $additionalClassification = $this->getSerializeArray($additionalClassification);



        // generate lang and long
        try {
            $address   = $place .", Deutschland"; // Google HQ
            $latlng    = $this->getGoogleLatAndLng($address);
            $latitude  = $latlng["lat"];
            $longitude = $latlng["lng"];
        } catch (\Exception $e1) {
            $latitude = 0;
            $longitude = 0;
        }

        try {
            $tournament = new \Badtra\Intranet\Model\Tournament();
            $tournament->setName($name);
            $tournament->setPlace($place);
            $tournament->setStartdate($startdate);
            $tournament->setEndDate($enddate);
            $tournament->setDeadline($deadline);
            $tournament->setLink($link);
            $tournament->setClassification($classification);
            $tournament->setAdditionalClassification($additionalClassification);
            $tournament->setDiscipline($discipline);
            $tournament->setReporterId($reporterId);
            $tournament->setTournamentType($tournamentType);
            $tournament->setLatitude($latitude);
            $tournament->setLongitude($longitude);
            $tournament->setDescription($description);

            if (! $this->brdb->updateTournamentById($tournament)) {
                $this->setFailedMessage("Turnier konnte nicht aktualisiert werden.");
                return;
            }
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, "Cannot update Tournament", $tournamentType->__toString(), "");
            $this->setFailedMessage("Turnier konnte nicht aktualisiert werden.");
            return;
        }

        $this->setSuccessMessage("Turnier wurde geändert.");
        $this->customRedirectArray(array("page" => $this->page, "action" => "details", "id" => $id));
    }

    private function processPostDeleteTournament(): bool
    {
        return false;
    }

    public function processGet() {
        if ($this->issetGetVariable("action")) {
            $action = $this->getGetVariable("action");
            $id = $this->getGetVariable("id");

            switch ($action) {
                case "deletePlayer":
                    if ($this->issetGetVariable("id") && $this->issetGetVariable("tournamentPlayerId")) {
                      $this->_deletePlayersFromTournamentId($this->getGetVariableInteger("id"), $this->getGetVariableInteger("tournamentPlayerId"));
                    }
                    break;

                case "create_backup":
                    $this->createBackup($id);
                    break;

                case "export":
                    // create backup
                    $this->createBackup($this->getGetVariable("id"));
                    // create xls
                    $this->export($this->getGetVariable("id"));
                    break;

                case "unlock":
                    $this->unlockPlayerFromTournament();
                    break;

                case "lock":
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
        if (!isset($playerId) || !is_numeric($playerId))
        {
            $result = $this->brdb->unlockAllPlayerFromTournament($id);
        }
        else
        {
            $result = $this->brdb->unlockPlayerFromTournament($id, $playerId);
        }

        if (!$result) {
          $this->setFailedMessage("Spieler/Spieler konnte/konnten nicht unlocked werden");
          return;
        }

        $this->setSuccessMessage("Spieler wurde geunlocked");
        $this->customRedirectArray(array("page" => $this->page, "action" => "details", "id" => $id));
    }

    /**
     * Lock player for tournament by id
     *
     * @return bool
     */
    private function lockPlayerFromTournament(): bool
    {
        $id = $this->getGetVariable("id");

        try {
            $playerId = $this->getGetVariable("tournamentPlayerId");
            if (!isset($id) || !is_numeric($id) || !isset($playerId) || !is_numeric($playerId))
            {
                throw new \Exception("Daten für die Paarung stimmen nicht!");
            }
           
            if (!$this->brdb->lockPlayerFromTournament($id, $playerId))
            {
                throw new \Exception("Spieler/Spieler konnte/konnten nicht locked werden");
            }
   
            $this->setSuccessMessage("Spieler wurde als gemeldet gesetzt.");
            $this->customRedirectArray(array("page" => $this->page, "action" => "details", "id" => $id));
            return true;
        }
        catch (\Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Parrung konnte nicht eingetragen werden: %d %d Details %s", $id, $playerId, $e->getMessage()), "", "POST");
            $this->setFailedMessage($e->getMessage());
            return false;
        }
    }

    /**
     * export Function
     */
    private function export(int $id): bool
    {
        if (!isset($id) || !is_numeric($id) || $id < 1) {
            return false;
        }

        if (
          ! $this->prgElementLogin->getLoggedInUser()->isAdmin() ||
          ! $this->prgElementLogin->getLoggedInUser()->isReporter()
        ) {
          return false;
        }

        ob_end_flush();
        if (isset($this->smarty))
        {
            unset($this->smarty);
        }
        header_remove();

        // get tournament data
        $tournament = $this->brdb->getTournamentData($id);

        switch ($tournament["tournamentType"])
        {
            case "NBV":
            $this->reportExcelNBV($tournament, $id);
            break;

            default:
            $this->exportDefault($tournament, $id);
            break;
        }

        // kill it
        return true;

      }

    private function reportExcelNBV(array $tournament, int $id): void
    {
        if (isset($tournament["name"]) && $tournament["deadline"]) {
            $fileName   = sprintf("%s_%d.xlsx", addslashes($tournament["name"]), date("d.m.Y", strtotime($tournament["deadline"])));
        } else {
            $fileName = "random.xlsx";
        }

        $writer     = WriterFactory::create(Type::XLSX); // for XLSX files

        //$writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName); // stream data directly to the browser



        // create sheets
        $einzel = $writer->getCurrentSheet();
        $einzel->setName("Einzel");
        $singleDataRow = array(
            "Vorname",
            "Name",
            "m/w",
            "Verein",
            "Verb",
            "Einzel",
            "Doppel",
            "Mixed",
            "ERP",
            "DRP",
            "MRP",
            "SBNr",
            "GebDat",
            "VereinsNr",
            "Rangfolge",
            "Quote",
            "gemeldet von",
        );
        $writer->addRow($singleDataRow);

        // create double sheets
        $doppel = $writer->addNewSheetAndMakeItCurrent();
        $doppel->setName("Doppel");
        $writer->addRow($singleDataRow);
    // create mx sheets
        $mixed = $writer->addNewSheetAndMakeItCurrent();
        $mixed->setName("Mixed");
        $writer->addRow($singleDataRow);

        $einzelCount = 0;
        $mixedCount  = 0;
        $doppelCount = 0;


        $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
        if (isset($players) && !empty($players)) {
            foreach ($players as $row) {
                /* MIXED */
                if (strpos($row["classification"], "GD") !== false) {
                    $writer->setCurrentSheet($mixed);
                    $add       = True;
                    $rowEinzel = "";
                    $rowDoppel = "";
                    $rowMixed  = $row["classification"];
                    $counter = ++$mixedCount;

                /* Doppel */
                } else if (strpos($row["classification"], "DD") !== false || strpos($row["classification"], "HD") !== false ||
                        strpos($row["classification"], "JD") !== false || strpos($row["classification"], "MD") !== false ) {
                    $writer->setCurrentSheet($doppel);
                    $add       = True;
                    $rowEinzel = "";
                    $rowDoppel = $row["classification"];
                    $rowMixed  = "";
                    $counter   = ++$doppelCount;

                /* Einzel */
                } else {
                    $writer->setCurrentSheet($einzel);
                    $add       = False;
                    $rowEinzel = $row["classification"];
                    $rowDoppel = "";
                    $rowMixed  = "";
                    $counter   = ++$einzelCount;
                }

                // add Player 1
                $bday   = date("d.m.Y", strtotime($row["p1Bday"]));
                $bday   = $bday == "01.01.1970" ? "" : $bday;
                $gender = $this->_transformGenderToNBVExport($row["p1Gender"]);
                $singleRow = array(
                    $row["p1FirstName"],
                    $row["p1LastName"],
                    $gender,
                    $row["p1ClubName"],
                    $row["p1ClubAssociation"],
                    $rowEinzel,
                    $rowDoppel,
                    $rowMixed,
                    "",
                    "",
                    "",
                    $row["p1PlayerNumber"],
                    $bday,
                    $row["p1ClubNr"],
                    $counter,
                    "",
                    "",
                );
                $writer->addRow($singleRow); // add a row at a time


                if ($add) {
                    if ($row["p2FirstName"] != NULL && $row["p2LastName"] != NULL) {
                        $firstName = $row["p2FirstName"];
                        $lastName  = $row["p2LastName"];
                        $gender    = $this->_transformGenderToNBVExport($row["p2Gender"]);

                        $bday = $this->tranformBday($row["bday"]);

                    } else {
                        $firstName = "FREIMELDUNG";
                        $lastName  = "";
                        $gender    = "";
                        $bday      = "";
                    }

                    $verein      = isset($row["p2ClubName"])         ? $row["p2ClubName"]     : "";
                    $association = isset($row["p2ClubAssociation"])  ? $row["p2ClubAssociation"]  : "";
                    $sbnr        = isset($row["p2PlayerNumber"])     ? $row["p2PlayerNumber"] : "";

                    $singleRow = array(
                        $firstName,
                        $lastName,
                        $gender,
                        $verein,
                        $association,
                        $rowEinzel,
                        $rowDoppel,
                        $rowMixed,
                        "",
                        "",
                        "",
                        $sbnr,
                        $bday,
                        $row["p2ClubNr"],
                        $counter,
                        "",
                        "",
                    );
                    $writer->addRow($singleRow); // add a row at a time
                }
            }
        }
        //$writer->addRows($multipleRows); // add multiple rows at a time
        $writer->close();

    }

    private function tranformBday(string $bday): string
    {
        if ($bday == null || strlen($bday)) {
            throw new \Exception("Bday is empty");
        }
        try {
            $due       = strtotime($bday);
            $bday      = date("d.m.Y", $due);
            return $bday == "01.01.1970" ? "" : $bday;
        } catch (\Exception $e) {
            throw new \Exception(sprintf("Cannot transform bday \"%s\"", $bday));
            return "";
        }
    }

    /**
     * Transform Gender to m/w for NBV.
     *
     * @param string $gender
     * @return string
     */
    private function _transformGenderToNBVExport(string $gender): string
    {
        return $gender == "Male" ? "m" : "w";
    }


    private function exportDefault(array $tournament,int $id): void
    {
        if (isset($tournament["name"]) && $tournament["deadline"]) {
            $fileName   = sprintf("%s_%d.xlsx", addslashes($tournament["name"]), date("d.m.Y", strtotime($tournament["deadline"])));
        } else {
            $fileName = "random.xlsx";
        }

        $writer     = WriterFactory::create(Type::XLSX); // for XLSX files


        //$writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName); // stream data directly to the browser

        // create sheets
        $einzel = $writer->getCurrentSheet();
        $einzel->setName("Spieler");

        $headerRow = array(
            "Vorname",
            "Name",
            "m/w",
            "Verein",
            "Verb",
            "Disziplin",
            "SBNr",
            "GebDat",
            "VereinsNr",
            "Rangfolge",
        );
        $writer->addRow($headerRow);


        $counter = 0;

        $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
        if (isset($players) && !empty($players))
        {
            foreach ($players as $row)
            {
                $gender = $this->_transformGenderToNBVExport($row["p1Gender"]);
                $bday   = $this->tranformBday($row["p1Bday"]);

                $singleRow = array(
                    $row["p1FirstName"],
                    $row["p1LastName"],
                    $gender,
                    $row["p1ClubName"],
                    $row["p1ClubAssociation"],
                    $row["classification"],
                    $row["p1PlayerNumber"],
                    $bday,
                    $row["p1ClubNr"],
                    ++$counter,
                );

                $writer->addRow($singleRow);

                if ($row["p2FirstName"] != null)
                {
                    $singleRow = array(
                        $row["p2FirstName"],
                        $row["p2LastName"],
                        $this->_transformGenderToNBVExport($row["p2Gender"]),
                        $row["p2ClubName"],
                        $row["p2ClubAssociation"],
                        $row["classification"],
                        $row["p2PlayerNumber"],
                        $this->tranformBday($row["p2Bday"]),
                        $row["p2ClubNr"],
                        $counter,
                    );
                    $writer->addRow($singleRow);
                }
            }
        }

        $writer->close();
        return;
    }


    /**
     * Create backup from Tournamentdata
     *
     * @param integer $id
     * @return boolean
     */
    private function createBackup(int $id): bool
    {
        try {
            $playerList = $this->brdb->getPlayersByTournamentId($id);
            $backup = array();
            if (!isset($playerList) || empty($playerList))
            {
                throw new \Exception("Kein Bacup nötig, da keine Spieler vorhanden sind.");
            }
            foreach ($playerList as $dataSet)
            {
                $backup[substr($dataSet["classification"], 0, 2)][] = array
                (
                    "playerId"       => $dataSet["playerId"],
                    "partnerId"      => $dataSet["partnerId"],
                    "classification" => $dataSet["classification"],
                );

                $backupS = serialize($backup);
                if (!$this->brdb->insertTournamentBackup($id, $backupS))
                {
                    throw new \Exception("Backup konnte nicht erstellt werden");
                }

                $this->setSuccessMessage("Backup wurde erstellt");
                $this->customRedirectArray(
                    array(
                        "page"   => $this->page,
                        "action" => "backup",
                        "id"     => $id,
                    )
                );
                  return true;
            }

        } catch (\Exception $e)
        {
            $this->setFailedMessage("Backup konnte nicht erstellt werden");
            return false;
        }
    }

    /**
     * If date is today return true, else false
     *
     * @param string $time
     * @return boolean
     */
    private function _isToday(string $time): bool
    {
        return (strtotime($time) === strtotime("today"));
    }

    /**
     * If date in past return true, else false
     *
     * @param string $time
     * @return bool
     */
    private function _isPast(string $time): bool
    {
        return (strtotime($time) < time());
    }

    /**
     * Get date in format Y-m-d
     *
     * @param string $date
     * @return string
     */
    private function getdate(string $date): string
    {
        return date("Y-m-d", strtotime($date));
    }
}

