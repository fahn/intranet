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
require_once "prgPattern.inc.php";
require_once BASE_DIR."/inc/db/brdb.inc.php";
require_once BASE_DIR."/inc/model/player.inc.php";


class PrgPatternElementPlayer extends APrgPatternElement
{
    const FORM_PLAYER_PLAYERID  = "playerId";
    const FORM_PLAYER_PLAYERNR  = "playerNr";
    const FORM_PLAYER_FIRSTNAME = "firstName";
    const FORM_PLAYER_LASTNAME  = "lastName";
    const FORM_PLAYER_BDAY      = "bday";
    const FORM_PLAYER_CLUBID    = "clubId";
    const FORM_PLAYER_GENDER    = "gender";

    const FORM_PLAYER_ACTION        = "formAction";
    const FORM_PLAYER_ACTION_SELECT = "Edit Player";
    const FORM_PLAYER_ACTION_INSERT = "Insert Player";
    const FORM_PLAYER_ACTION_UPDATE = "Update Player";
    const FORM_PLAYER_ACTION_DELETE = "Delete Player";

    // Errors that can be set by methods of this class
    const ERROR_USER_DELETE_NO_USERID           = "Please select a user for deleting it!";
    const ERROR_USER_UPDATE_INVALID_EMAIL       = "Please use a valid email format!";
    const ERROR_USER_UPDATE_MISSING_INFORMATION = "Please provide all required information!";
    const ERROR_USER_UPDATE_PASSWORD_MISSMATCH  = "Your passwords do not match!";
    const ERROR_USER_UPDATE_PASSWORD_INVALID    = "Please set a valid password!";
    const ERROR_USER_EMAIL_EXISTS = "Email is already registered!";
    const ERROR_USER_NO_USER_FOR_ADMIN_SELECTED = "Please select a User that should be edited!";
    const ERROR_USER_NO_USER_FOR_ADMIN_FOUND    = "Please select a valid User that should be edited!";
    const ERROR_USER_UPDATE_CANNOT_DEADMIN      = "You are not allowed to remove admin rights from your own account!";
    const SUCCESS_USER_REGISTER = "Succesfully registered account!";
    const SUCCESS_USER_UPDATE   = "Succesfully updated account!";
    const SUCCESS_USER_DELETE   = "Succesfully deleted User!";

    protected PrgPatternElementLogin $prgElementLogin;


    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("player");

        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_PLAYER_PLAYERID);
        $this->registerPostSessionVariable(self::FORM_PLAYER_PLAYERNR);
        $this->registerPostSessionVariable(self::FORM_PLAYER_FIRSTNAME);
        $this->registerPostSessionVariable(self::FORM_PLAYER_LASTNAME);
        $this->registerPostSessionVariable(self::FORM_PLAYER_BDAY);
        $this->registerPostSessionVariable(self::FORM_PLAYER_CLUBID);
        $this->registerPostSessionVariable(self::FORM_PLAYER_GENDER);

    }//end __construct()


    public function processPost()
    {
        // ADMIN AREA
        // $this->prgElementLogin->redirectUserIfNotLoggindIn();
        // $this->prgElementLogin->redirectUserIfnoRights(array("reporter", "admin"), "or");
        if (! $this->issetPostVariable(self::FORM_PLAYER_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }


        $loginAction = $this->getPostVariable(self::FORM_PLAYER_ACTION);
        switch ($loginAction) {
        case self::FORM_PLAYER_ACTION_INSERT:
            $this->processPostInsertPlayer();
                break;

        case self::FORM_PLAYER_ACTION_UPDATE:
            $this->processPostUpdatePlayer();
                break;

        case self::FORM_PLAYER_ACTION_DELETE:
            $this->processPostDeletePlayer();
                break;

        default:
            // code...
                break;
        }

    }//end processPost()


    public function processGet()
    {
    }//end processGet()


    /**
     * Insert Player
     *
     * @return boolean
     */
    private function processPostInsertPlayer(): bool
    {
        // Check that all information has been posted
        $requireFields = [
            self::FORM_PLAYER_FIRSTNAME,
            self::FORM_PLAYER_LASTNAME,
            self::FORM_PLAYER_BDAY,
            self::FORM_PLAYER_GENDER,
            self::FORM_PLAYER_PLAYERID,
            self::FORM_PLAYER_CLUBID,
        ];
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return false;
        }

        try {
            $player = new Player();
            $player->setFirstname(strval(trim($this->getPostVariable(self::FORM_PLAYER_FIRSTNAME))));
            $player->setLastname(strval(trim($this->getPostVariable(self::FORM_PLAYER_LASTNAME))));
            $player->setGender(strval(trim($this->getPostVariable(self::FORM_PLAYER_GENDER))));
            $player->setBday(strval(trim($this->getPostVariable(self::FORM_PLAYER_BDAY))));
            $player->setPlayerNr(strval(trim($this->getPostVariable(self::FORM_PLAYER_PLAYERNR))));
            $player->setClubId(intval(trim($this->getPostVariable(self::FORM_PLAYER_CLUBID))));

            $this->brdb->insertPlayer($player);

            $this->setSuccessMessage("Spieler wurde angelegt");
            $this->customRedirectArray(
                ["page" => "adminAllUser.php"]
            );
            return true;
        } catch (Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot insert Player. %s Details %s", $player, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Spieler konnte nicht eingetragen werden");
            return false;
        }//end try

    }//end processPostInsertPlayer()


    /**
     * Update Player
     *
     * @return boolean
     */
    private function processPostUpdatePlayer(): bool
    {
        // Check that all information has been posted
        $requireFields = [
            self::FORM_PLAYER_PLAYERID,
            self::FORM_PLAYER_FIRSTNAME,
            self::FORM_PLAYER_LASTNAME,
            self::FORM_PLAYER_BDAY,
            self::FORM_PLAYER_GENDER,
            self::FORM_PLAYER_PLAYERID,
            self::FORM_PLAYER_CLUBID,
        ];
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return false;
        }

        try {
            $id = intval(trim($this->getPostVariable(self::FORM_PLAYER_PLAYERID)));

            $player = new Player();
            $player->setPlayerId($id);
            $player->setFirstname(strval(trim($this->getPostVariable(self::FORM_PLAYER_FIRSTNAME))));
            $player->setLastname(strval(trim($this->getPostVariable(self::FORM_PLAYER_LASTNAME))));
            $player->setGender(strval(trim($this->getPostVariable(self::FORM_PLAYER_GENDER))));
            $player->setBday(strval(trim($this->getPostVariable(self::FORM_PLAYER_BDAY))));
            $player->setPlayerNr(strval(trim($this->getPostVariable(self::FORM_PLAYER_PLAYERNR))));
            $player->setClubId(intval(trim($this->getPostVariable(self::FORM_PLAYER_CLUBID))));

            $this->brdb->updatePlayer($player);

            $this->setSuccessMessage("Spieler wurde editiert");
            $this->customRedirectArray(
                ["page" => "adminAllUser.php"]
            );
            return true;
        } catch (Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot update Player. %s Details %s", $player, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Spieler konnte nicht eingetragen werden");
            return false;
        }//end try

    }//end processPostUpdatePlayer()


    private function processPostDeletePlayer()
    {
        // Check that all information has been posted
        $requireFields = [self::FORM_PLAYER_PLAYERID];
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return false;
        }

        try {
            $id = intval(trim($this->getPostVariable(self::FORM_PLAYER_PLAYERID)));

            $this->brdb->deletePlayer($id);
        } catch (Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot delete Player. %d Details %s", $id, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Spieler konnte nicht eingetragen werden");
            return false;
        }
    }//end processPostDeletePlayer()


    /**
     * find a player
     *
     * @param  Player $player
     * @return void
     */
    public function find(Player $player): bool
    {
        try {
            return count($this->brdb->selectPlayerByPlayerNr($player->getPlayerNr())) > 0 ? true : false;
        } catch (Exception $e) {
            return false;
        }

    }//end find()


    /**
     * insert
     *
     * @param  Player $player
     * @return boolean
     */
    public function insert(Player $player): bool
    {
        try {
            return $this->brdb->insertPlayer($player);
        } catch (Exception $e) {
            return false;
        }

    }//end insert()


    public function update(Player $player): bool
    {
        try {
            return $this->brdb->updatePlayer($player);
        } catch (Exception $e) {
            return false;
        }

    }//end update()
}//end class
