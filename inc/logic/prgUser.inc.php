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

class PrgPatternElementUser extends APrgPatternElement {
    const FORM_USER_ADMIN_USER_ID = "accountAdminUserId";
    const FORM_USER_EMAIL         = "accountEmail";
    const FORM_USER_FNAME         = "accountFirstName";
    const FORM_USER_LNAME         = "accountLastName";
    const FORM_USER_GENDER        = "accountGender";
    const FORM_USER_IS_PLAYER     = "accountIsPlayer";
    const FORM_USER_IS_ADMIN      = "accountIsAdmin";
    const FORM_USER_IS_REPORTER   = "accountIsReporter";
    const FORM_USER_PASSWORD      = "accountPassword";
    const FORM_USER_PASSWORD2     = "accountPassword2";
    const FORM_USER_PHONE         = "accountPhone";
    const FORM_USER_BDAY          = "accountBday";
    const FORM_USER_PLAYERID      = "accountPlayerId";
    const FORM_USER_CLUBID        = "accountClubId";

    const FORM_USER_GENDER_MALE   = "Male";
    const FORM_USER_GENDER_FEMALE = "Female";

    const FORM_USER_IS_YES = "1";
    const FORM_USER_IS_NO  = "";

    const FORM_USER_ACTION                   = "formAction";
    const FORM_USER_ACTION_REGISTER          = "Register";
    const FORM_USER_ACTION_SELECT_ACCOUNT    = "Edit User";
    const FORM_USER_ACTION_INSERT_ACCOUNT    = "Insert User";
    const FORM_USER_ACTION_UPDATE_ACCOUNT    = "Update User";
    const FORM_USER_ACTION_DELETE_ACCOUNT    = "Delete User";
    const FORM_USER_ACTION_UPDATE_MY_ACCOUNT = "Update My Account";

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

    const PASSWORD_LENGTH = 8;

    protected $prgElementLogin;

    public function __construct() {

    }


    public function __load(PrgPatternElementLogin $prgElementLogin) {

        parent::__construct("userRegister");
        $this->prgElementLogin = $prgElementLogin;
        $this->registerPostSessionVariable(self::FORM_USER_EMAIL);
        $this->registerPostSessionVariable(self::FORM_USER_FNAME);
        $this->registerPostSessionVariable(self::FORM_USER_LNAME);
        $this->registerPostSessionVariable(self::FORM_USER_GENDER);
        $this->registerPostSessionVariable(self::FORM_USER_IS_PLAYER);
        $this->registerPostSessionVariable(self::FORM_USER_IS_ADMIN);
        $this->registerPostSessionVariable(self::FORM_USER_IS_REPORTER);
        $this->registerPostSessionVariable(self::FORM_USER_ADMIN_USER_ID);

    }

    public function processPost() {
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin     = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if (!$this->prgElementLogin->isUserLoggedIn() || !$isUserAdmin) {
            return;
        }


        if ($this->issetPostVariable(self::FORM_USER_ACTION)) {

            $loginAction = strval(trim($this->getPostVariable(self::FORM_USER_ACTION)));
            switch ($loginAction) {
              /*case self::FORM_USER_ACTION_REGISTER:
                $this->processPostDeleteUserAccount();
                break;
*/
              case self::FORM_USER_ACTION_INSERT_ACCOUNT:
                $this->processPostInsertUserAccount();
                break;

              case self::FORM_USER_ACTION_UPDATE_ACCOUNT:
                $id = Tools::get("id");
                $this->processPostUpdateUserAccount($id);
                break;

              case  self::FORM_USER_ACTION_UPDATE_MY_ACCOUNT:
                $this->processPostUpdateUserMyAccount();
                break;

              default:
                # code...
                break;
            }
        }
    }

    public function processGet() {
      $isUserLoggedIn  = PrgPatternElementLogin::isUserLoggedIn();
      $isUserAdmin     = PrgPatternElementLogin::getLoggedInUser()->isAdmin();
      // Don't process the posts if no user is logged in!
      // otherwise well formed post commands could trigger database actions
      // without theoretically having access to it.
      if (!$this->prgElementLogin->isUserLoggedIn() || !$isUserAdmin) {
          return;
      }

        $action = Tools::get("action");

        switch ($action) {
          case 'delete':
            $id = Tools::get("id");
            $this->processGetDeleteUserAccount($id);
            break;

          default:
            # code...
            break;
        }
    }

    private function processPostInsertUserAccount() {
      // Check that all information has been posted
      if (
        ! $this->issetPostVariable(self::FORM_USER_FNAME) ||
        ! $this->issetPostVariable(self::FORM_USER_LNAME) ||
        ! $this->issetPostVariable(self::FORM_USER_GENDER)
      ) {
          $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
          return;
      }

      $res = insertUserEasyProcess();
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

    public function processPostUpdateUserAccount($userId) {
        $email      = strtolower(strval(trim($this->getPostVariable(self::FORM_USER_EMAIL))));
        $firstName  = strval(trim($this->getPostVariable(self::FORM_USER_FNAME)));
        $lastName   = strval(trim($this->getPostVariable(self::FORM_USER_LNAME)));
        $gender     = strval(trim($this->getPostVariable(self::FORM_USER_GENDER)));

        // is Admin
        if(!$this->issetPostVariable(self::FORM_USER_IS_ADMIN) && $userId != 2) {
            $isAdmin = 0;
        } else {
            $isAdmin = strval(trim($this->getPostVariable(self::FORM_USER_IS_ADMIN)));
        }

        // isReporter
        if(!$this->issetPostVariable(self::FORM_USER_IS_REPORTER)) {
            $isReporter = 0;
        } else {
            $isReporter = strval(trim($this->getPostVariable(self::FORM_USER_IS_REPORTER)));
        }

        // isPlayer
        if(!$this->issetPostVariable(self::FORM_USER_IS_PLAYER)) {
            $isPlayer = 0;
        } else {
            $isPlayer = strval(trim($this->getPostVariable(self::FORM_USER_IS_PLAYER)));
        }

        // additional
        $playerId = strval(trim($this->getPostVariable(self::FORM_USER_PLAYERID)));;
        $clubId   = strval(trim($this->getPostVariable(self::FORM_USER_CLUBID)));;
        $phone    = strval(trim($this->getPostVariable(self::FORM_USER_PHONE)));;
        if ($this->issetPostVariable(self::FORM_USER_BDAY)) {
            $bday     = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_USER_BDAY)));
        } else {
            $bday = "";
        }


        // Check if oyu try to deadmin yourself this is not allowed, otherwise
        // it would be possible to lockout oneself from the data base
        if ($userId == $this->prgElementLogin->getLoggedInUser()->userId &&
            $this->prgElementLogin->getLoggedInUser()->isAdmin() &&
            $isAdmin == 0) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_CANNOT_DEADMIN);
            return;
        }

        // First filter the email before continue
        if(isset($email) && count($email) > 3) {
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_INVALID_EMAIL);
          }
          // Check if the user is already registered
          // In case it is we have to throw an error
          $res = $this->brdb->selectUserbyEmail($email);
          if ($this->brdb->hasError()) {
              $this->setFailedMessage($this->brdb->getError());
              return;
          }
          if ($res->num_rows > 0) {
              // Check that it is not my email, which is allowed to be set!
              $dataSetUser = new User($res->fetch_assoc());
              if ($dataSetUser->userId != $userId) {
                  $this->setFailedMessage(self::ERROR_USER_EMAIL_EXISTS);
                  return;
              }
          }
        }

        // now everything is checked and the command for adding
        // the user can be called
        $res = $this->brdb->updateAdminUser($userId, $email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $clubId, $isPlayer, $isReporter, $isAdmin);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }
        $this->setSuccessMessage(self::SUCCESS_USER_UPDATE);
        return true;
    }

    public function processPostUpdateUserMyAccount() {
        // Check that all information has been posted
        if (! $this->issetPostVariable(self::FORM_USER_EMAIL) ||
            ! $this->issetPostVariable(self::FORM_USER_FNAME) ||
            ! $this->issetPostVariable(self::FORM_USER_LNAME) ||
            ! $this->issetPostVariable(self::FORM_USER_PASSWORD) ||
            ! $this->issetPostVariable(self::FORM_USER_PASSWORD2)) {
              $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
        }

        $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);
        $email     = strval(trim($this->getPostVariable(self::FORM_USER_EMAIL)));
        $firstName = strval(trim($this->getPostVariable(self::FORM_USER_FNAME)));
        $lastName  = strval(trim($this->getPostVariable(self::FORM_USER_LNAME)));
        $pass      = strval(trim($this->getPostVariable(self::FORM_USER_PASSWORD)));
        $pass2     = strval(trim($this->getPostVariable(self::FORM_USER_PASSWORD2)));
        $passHash  = password_hash($pass, PASSWORD_DEFAULT);
        $email     = strtolower($email);
        $phone     = strval(trim($this->getPostVariable(self::FORM_USER_PHONE)));
        $gender    = strval(trim($this->getPostVariable(self::FORM_USER_GENDER)));
        $bday      = strval(trim($this->getPostVariable(self::FORM_USER_BDAY)));

        if(!empty($pass) && !empty($pass2)) {
          // Check if passwords are the same
          if ($pass != $pass2) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_PASSWORD_MISSMATCH);
            return;
          }
          $this->brdb->updateUserPassword($userId, $passHash);
          if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
          }
        }

        // First filter the email before continue
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
          // Check that it is not my email, which is allowed to be set!
          $dataSetUser = new User($res->fetch_assoc());
          if ($dataSetUser->userId != $userId) {
            $this->setFailedMessage(self::ERROR_USER_EMAIL_EXISTS);
            return;
          }
        }

        // now everything is checked and the command for adding
        // the user can be called
        $bday = strval(trim($this->getPostVariable(self::FORM_USER_BDAY)));
        $bday = date("Y-m-d", strtotime($bday));

        $phone = strval(trim($this->getPostVariable(self::FORM_USER_PHONE)));

        $res = $this->brdb->updateUser($userId, $email, $firstName, $lastName, $gender, $phone, $bday);
        if ($this->brdb->hasError()) {
          $this->setFailedMessage($this->brdb->getError());
          return;
        }

        $this->setSuccessMessage(self::SUCCESS_USER_UPDATE);
        return;
    }

    public function getAdminUser() {
        // If there is no post we directly get here and we try to set the class
        // information directly from the stored information in the session
        if ($this->issetSessionVariable(self::FORM_USER_ADMIN_USER_ID)) {
            // Try to get the user by the ID stored in the session
            $userId = intval($this->getSessionVariable(self::FORM_USER_ADMIN_USER_ID));
            $res = $this->brdb->selectUserById($userId);
            if ($this->brdb->hasError()) {
                $this->setFailedMessage($this->brdb->getError());
                return new User();
            }
            // if the query was succesful try to use the data to init the User object
            if ($res->num_rows == 1) {
                $dataSet = $res->fetch_assoc();
                return new User($dataSet);
            } else {
                $this->setFailedMessage(self::ERROR_USER_NO_USER_FOR_ADMIN_FOUND);
            }
            $this->setFailedMessage(self::ERROR_USER_NO_USER_FOR_ADMIN_SELECTED);
        }

        return new User();
    }


    public function getAdminsAndReporter() {
        $data = array();
        $res = $this->brdb->GetActiveAndReporterOrAdminPlayer();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[]         = array(
                    'userId'   => $dataSet['userId'],
                    'fullName' => $dataSet['firstName'] .' '. $dataSet['lastName'],
                );
            }
        }
        return $data;
    }
}

?>
