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
include_once $_SERVER['BASE_DIR'] .'/inc/model/player.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

class PrgPatternElementPlayer extends APrgPatternElement {
    const FORM_PLAYER_PLAYERID    = "playerId";
    const FORM_PLAYER_PLAYERNR    = "playerNr";
    const FORM_PLAYER_FIRSTNAME   = "firstName";
    const FORM_PLAYER_LASTNAME    = "lastName";
    const FORM_PLAYER_BDAY        = "bday";
    const FORM_PLAYER_CLUBID      = "clubId";
    const FORM_PLAYER_GENDER      = "gender";

    const FORM_PLAYER_ACTION        = "formAction";
    const FORM_PLAYER_ACTION_SELECT = "Edit Player";
    const FORM_PLAYER_ACTION_INSERT = "Insert Player";
    const FORM_PLAYER_ACTION_UPDATE = "Update Player";
    const FORM_PLAYER_ACTION_DELETE = "Delete Player";

    // Errors that can be set by methods of this class
    const ERROR_USER_DELETE_NO_USERID            = "Please select a user for deleting it!";
    const ERROR_USER_UPDATE_INVALID_EMAIL        = "Please use a valid email format!";
    const ERROR_USER_UPDATE_MISSING_INFORMATION  = "Please provide all required information!";
    const ERROR_USER_UPDATE_PASSWORD_MISSMATCH   = "Your passwords do not match!";
    const ERROR_USER_UPDATE_PASSWORD_INVALID     = "Please set a valid password!";
    const ERROR_USER_EMAIL_EXISTS                = "Email is already registered!";
    const ERROR_USER_NO_USER_FOR_ADMIN_SELECTED  = "Please select a User that should be edited!";
    const ERROR_USER_NO_USER_FOR_ADMIN_FOUND     = "Please select a valid User that should be edited!";
    const ERROR_USER_UPDATE_CANNOT_DEADMIN       = "You are not allowed to remove admin rights from your own account!";
    const SUCCESS_USER_REGISTER                  = "Succesfully registered account!";
    const SUCCESS_USER_UPDATE                    = "Succesfully updated account!";
    const SUCCESS_USER_DELETE                    = "Succesfully deleted User!";


    protected $prgElementLogin;

    protected $brdb;

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("player");

        $this->registerPostSessionVariable(self::FORM_PLAYER_PLAYERID);
        $this->registerPostSessionVariable(self::FORM_PLAYER_PLAYERNR);
        $this->registerPostSessionVariable(self::FORM_PLAYER_FIRSTNAME);
        $this->registerPostSessionVariable(self::FORM_PLAYER_LASTNAME);
        $this->registerPostSessionVariable(self::FORM_PLAYER_BDAY);
        $this->registerPostSessionVariable(self::FORM_PLAYER_CLUBID);
        $this->registerPostSessionVariable(self::FORM_PLAYER_GENDER);

        // load DB
        $this->brdb = $brdb;

        // load Login
        $this->prgElementLogin = $prgElementLogin;

    }

    public function processPost() {
        // ADMIN AREA
        #$this->prgElementLogin->redirectUserIfNotLoggindIn();
        #$this->prgElementLogin->redirectUserIfnoRights(array('reporter', 'admin'), 'or');





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
                $id = Tools::get("id");
                $this->processPostUpdateUserAccount($id);
                break;

            case  self::FORM_PLAYER_ACTION_DELETE:

                break;

            default:
                # code...
                break;
        }
    }

    public function processGet() {}

    private function processPostInsertPlayer() {
      // Check that all information has been posted

      if (
        ! $this->issetPostVariable(self::FORM_PLAYER_FIRSTNAME) ||
        ! $this->issetPostVariable(self::FORM_PLAYER_LASTNAME) ||
        ! $this->issetPostVariable(self::FORM_PLAYER_BDAY) ||
        ! $this->issetPostVariable(FORM_PLAYER_GENDER) ||
        ! $this->issetPostVariable(FORM_PLAYER_PLAYERID) ||
        ! $this->issetPostVariable(self::FORM_PLAYER_CLUBID)
      ) {
          $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
          return;
      }



      $data = array(
          'clubId'    => $this->getPostVariable(self::FORM_PLAYER_CLUBID),
          'firstName' => $this->getPostVariable(self::FORM_PLAYER_FIRSTNAME),
          'lastName'  => $this->getPostVariable(self::FORM_PLAYER_LASTNAME),
          'gender'    => $this->getPostVariable(self::FORM_PLAYER_GENDER),
          'playerNr'  => $this->getPostVariable(self::FORM_PLAYER_PLAYERNR),
          'bday'      => $this->getPostVariable(self::FORM_PLAYER_BDAY),
      );

      $res = $this->brdb->insertPlayer($data);
      if ($this->brdb->hasError()) {
          $this->setFailedMessage($this->brdb->getError());
          return;
      }
      $id = $this->brdb->insert_id();

      if(!$this->processPostUpdateUserAccount($id)) {
          $this->setFailedMessage("Probleme beim anlegen. Bitte editieren Sie den Nutzer!");
          Tools::customRedirect(array(
            'page'   => "adminAllUser.php",
            'action' => 'edit',
            'id'     => $id,
          ));
          return;
      }

      $this->setSuccessMessage("Nutzer wurde angelegt");
      Tools::customRedirect(array(
        'page'   => "adminAllUser.php",
        'action' => 'edit',
        'id'     => $id,
      ));
      return;

    }

    public function processPostRegisterUser() {
        // Check that all information has been posted
        if (
            !$this->issetPostVariable(self::FORM_USER_EMAIL) ||
            !$this->issetPostVariable(self::FORM_USER_FNAME) ||
            !$this->issetPostVariable(self::FORM_USER_LNAME) ||
            !$this->issetPostVariable(self::FORM_USER_GENDER) ||
            !$this->issetPostVariable(self::FORM_USER_PASSWORD) ||
            !$this->issetPostVariable(self::FORM_USER_PASSWORD2)) {

            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return;
        }

        $email      = strval(trim($this->getPostVariable(self::FORM_USER_EMAIL)));
        $firstName  = strval(trim($this->getPostVariable(self::FORM_USER_FNAME)));
        $lastName   = strval(trim($this->getPostVariable(self::FORM_USER_LNAME)));
        $gender     = strval(trim($this->getPostVariable(self::FORM_USER_GENDER)));
        $email      = strtolower($email);

        // First filter the email before continue
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $this->setFailedMessage(self::ERROR_USER_UPDATE_INVALID_EMAIL);
          return;
        }
        // Check if the user is already registered
        // In case it is we have to throw an error
        $res = $this->brdb->selectUserbyEmail($email);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        if ($res->num_rows > 0) {
            $this->setFailedMessage(self::ERROR_USER_EMAIL_EXISTS);
            return;
        }

        // now everything is checked and the command for adding
        // the user can be called
        $res = $this->brdb->registerUser($email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $clubId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        $this->setSuccessMessage(self::SUCCESS_USER_REGISTER);
        return;
    }

    public function processPostDeleteUserAccount() {
        if (!$this->issetPostVariable(self::FORM_USER_ADMIN_USER_ID)) {
            $this->setFailedMessage(self::ERROR_USER_DELETE_NO_USERID);
            return;
        }

        $userId = intval($this->getPostVariable(self::FORM_USER_ADMIN_USER_ID));
        if ($userId == 2) {
            $this->setFailedMessage("GodMode ON");
            return;
        }
        $res = $this->brdb->deleteUserById($userId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
        }

        $this->setSuccessMessage(self::SUCCESS_USER_DELETE);
        return;
    }

    /** Delete Player: Delete E-Mail, Password and rights
      *
      */
    private function processGetDeleteUserAccount($userId) {
        if ($userId == 2) {
            $this->setFailedMessage("GodMode ON");
            return;
        }
        $res = $this->brdb->deleteUserById($userId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
        }

        $this->setSuccessMessage(self::SUCCESS_USER_DELETE);
        Tools::customRedirect(array(
          'page' => "adminAllUser.php",
        ));
        return true;
    }


    public function find($item) {
        if ($item instanceof Player) {
            $playerNr = $item->getPlayerNr();
            $res      = $this->brdb->selectPlayerByPlayerNr($playerNr);
            if ($this->brdb->hasError()) {
                return false;
            }

            return $res->num_rows == 1 ? true : false;
        }
        return false;

    }



    public function insert($player) {
        if ($player instanceof Player) {
            $arr = $player->getSqlData();
            $res = $this->brdb->insertPlayer($arr);
            if ($this->brdb->hasError()) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function update($item) {
        if ($item instanceof Player) {
            $res = $this->brdb->updatePlayer($item->getSqlData());
            if ($this->brdb->hasError()) {
                return false;
            }
            return true;
        }
        return false;
    }
}

?>
