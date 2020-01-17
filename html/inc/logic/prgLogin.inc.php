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
include_once 'prgPattern.inc.php';

// DB
include_once BASE_DIR .'/inc/db/brdb.inc.php';

// Model
include_once BASE_DIR .'/inc/model/user.inc.php';

// Tools
include_once BASE_DIR .'/inc/logic/tools.inc.php';

// Smarty
include_once BASE_DIR .'/smarty/libs/Smarty.class.php';

class PrgPatternElementLogin extends APrgPatternElement {
    // database
    private $brdb;

    // tools
    private $tools;

    // user
    private $loggedInUser;

    const FORM_LOGIN_EMAIL         = "formLoginEmail";
    const FORM_LOGIN_PASSWORD      = "formLoginPass";
    const FORM_LOGIN_PASSWORD2     = "formLoginPass2";
    const FORM_LOGIN_TOKEN         = "formLoginToken";
    const FORM_LOGIN_ACTION        = "formLoginAction";
    const FORM_LOGIN_ACTION_LOGIN  = "Log In";
    const FORM_LOGIN_ACTION_LOGOUT = "Log Out";

    const FORM_ACTION_REQUEST_PASS  = "request_password";
    const FORM_ACTION_CHANGE_PASS   = "change_password";

    // Errors that can be set by methods of this class
    const ERROR_LOGIN_INVALID_EMAIL           = "Please use a valid email!";
    const ERROR_LOGIN_INVALID_ID               = "Could not find a registered user!";
    const ERROR_LOGIN_UNKNOWN_EMAIL_PASSWORD   = "Unknown email and/or incorrect password!";
    const ERROR_LOGIN_NO_SESSION               = "You are not logged in!";
    const SUCCESS_LOGIN                       = "You are successfully logged in!";
    const SUCCESS_LOGOUT                       = "You are successfully logged out!";

    // Wait time after incorrect login to prevent brute force attacks
    const PASSWORD_WAIT_FOR_WRONG   = 2;

    // Constants for the User table in the database
    const SESSION_LOGIN_USER_ID   = "sessionUserId";



    public function __construct($brdb) {
        parent::__construct("login");

        // load db
        $this->brdb = $brdb;

        $this->registerPostSessionVariable(self::FORM_LOGIN_ACTION);
        $this->registerPostSessionVariable(self::FORM_LOGIN_EMAIL);

        $server_scriptname = $_SERVER['PHP_SELF'];
        if(strstr($server_scriptname, 'pages/logout.php', true)) {
            $this->processPostLogout();
        }

        // set
        $this->tools = new Tools();
    }

    public function processPost() {
        if ($this->issetPostVariable(self::FORM_LOGIN_ACTION)) {
            $loginAction = strval(trim($this->getPostVariable(self::FORM_LOGIN_ACTION)));

            switch ($loginAction) {
                case self::FORM_ACTION_REQUEST_PASS:
                $this->processPostRequestPassword();
                break;

                case self::FORM_ACTION_CHANGE_PASS:
                $this->processPostChangePassword();
                break;

                case self::FORM_LOGIN_ACTION_LOGIN:
                $this->processPostLogin();
                break;

                case self::FORM_LOGIN_ACTION_LOGOUT:
                $this->processPostLogout();
                break;

                default:
                # code...
                break;
            }
        }
    }

    public function processGet() {}


    /** Request Password
      *
      *
      */
    private function processPostRequestPassword() {
        if ($this->isUserLoggedIn()) {
            $this->setFailedMessage("Du bist bereits angemeldet.");
            return;
        }

        if (!$this->issetPostVariable(self::FORM_LOGIN_EMAIL)) {
            $this->setFailedMessage("no valid mail address");
            return;
        }

        // check if mail is valid and exists
        $mail = strval(trim($this->getPostVariable(self::FORM_LOGIN_EMAIL)));
        if (! $this->tools->validEmail($mail)) {
            $this->setFailedMessage("no valid mail address1");
            return;
        }
        $res = $this->brdb->selectUserByEmail($mail);
        if($res->num_rows != 1) {
            $this->setFailedMessage("Bitte bei dem Support melden.");
            return;
        }
        $userData = $res->fetch_array();

        $ip = $_SERVER['REMOTE_ADDR'];

        // create token
        $token = hash("sha256", time() . rand(1, 9999));

        // insert token and userid
        $this->brdb->insertUserPassHash($userData['userId'], $token, $ip);
        if($this->brdb->hasError()) {
          $this->setFailedMessage("Bitte bei dem Support melden.");
          return;
        }

        $name      = $userData['firstName'];
        $subject   = "Dein Password wurde angefordert.";

        $link = $this->tools->linkTo(array(
          'page'   => 'index.php',
          'action' => 'change_password',
          'token'  => $token,
          'mail'   => base64_encode($mail)
        ));

        $assign    = array(
            'name'    => $name,
            'link'    => $link,
            'baseUrl' => $this->tools->getIniValue('baseUrl'),
        );

        $message   = '';
        if(!$this->tools->sendMail($mail, $name, $subject, 'Dein Passwort wurde angefordert', $message, $assign, 'htmlmail/request_password.tpl')) {
            $this->setFailedMessage("Fehler beim E-Mail-Versand.");
            return;
        }


        $this->setSuccessMessage("Ihr Password wurde angefordert.");
        $this->tools->customRedirect($this->tools->getBaseUrl());
        return;

    }

    /**
     * Change Password
     */
    private function processPostChangePassword() {
      // check if variables set
      if (!$this->issetPostVariable(self::FORM_LOGIN_EMAIL) ||
          !$this->issetPostVariable(self::FORM_LOGIN_TOKEN) ||
          !$this->issetPostVariable(self::FORM_LOGIN_PASSWORD)  ||
          !$this->issetPostVariable(self::FORM_LOGIN_PASSWORD2)) {
        $this->setFailedMessage("Formulardaten sind falsch.");
        return;
      }
      $mail  = base64_decode($this->getPostVariable(self::FORM_LOGIN_EMAIL));

      $token = strval(trim($this->getPostVariable(self::FORM_LOGIN_TOKEN)));
      $pass  = strval(trim($this->getPostVariable(self::FORM_LOGIN_PASSWORD)));
      $pass2 = strval(trim($this->getPostVariable(self::FORM_LOGIN_PASSWORD2)));

      // valid mail
      if (! $this->tools->validEmail($mail)) {
        $this->setFailedMessage("no valid mail address");
        return;
      }

      // check if Password = Password2
      if(strlen($pass) == 0) {
        $this->setFailedMessage("Bitte gebe ein Passwort ein.");
        return false;
      }
      if ($pass != $pass2) {
        $this->setFailedMessage("Passwörter stimmen nicht überein.");
        return false;
      }

      // check if token exists
      $res = $this->brdb->GetUserPassHash($mail, $token);
      if($this->brdb->hasError() || $res->num_rows != 1) {
          $this->setFailedMessage("Bitte bei dem Support melden.");
          return false;
      }

      $data     = $res->fetch_array();
      $passHash = $this->tools->createPasswordHash($pass);


      // delete token
      $this->brdb->DeleteUserPassHash($data['userId'], $token);
      if($this->brdb->hasError()){
          $this->setFailedMessage("Bitte bei dem Support melden.");
          return false;
      }

      // set new password
      $this->brdb->updateUserPassword($data['userId'], $passHash);
      if($this->brdb->hasError()){
          $this->setFailedMessage("Bitte bei dem Support melden.");
          return false;
      }

      $this->setSuccessMessage("Passwort wurde geändert. Bitte nun einloggen");
      $this->tools->customRedirect($this->tools->getBaseUrl());
      return true;
    }


    /**
     * Login via POST-Form
     */
    private function processPostLogin() {
        if ($this->tools->isProductiv() && isset($_SESSION['badlogin']) && $_SESSION['badlogin'] >= 5) {
            $this->setFailedMessage('Kein Login möglich. Bitte versuchen Sie es später noch einmal');
            return;
        }

        $_SESSION['badlogin'] = !isset($_SESSION['badlogin']) ? 0 : $_SESSION['badlogin']+1;

        // In case of a post check if the entered information
        // is valid and does not inject wired stuff. (Avoid SQL injection here)
        if (! $this->issetPostVariable(self::FORM_LOGIN_EMAIL) ||
            ! $this->issetPostVariable(self::FORM_LOGIN_PASSWORD)) {
            $this->setFailedMessage(self::ERROR_LOGIN_INVALID_EMAIL);
            return false;
        }

        $email     = $this->getPostVariable(self::FORM_LOGIN_EMAIL);
        $pass      = $this->getPostVariable(self::FORM_LOGIN_PASSWORD);

        // filter the email to avoid having other wired stuff being
        // injected to the php code or the database maybe
        if (! $this->tools->validEmail($email)) {
            $this->setFailedMessage(self::ERROR_LOGIN_INVALID_EMAIL);
            return;
        }

        // Now see if there is a user in the data base with correct
        // email and hashed password. Passwords are hashed with php hash functionality
        $dataSet = $this->brdb->selectUserByEmail($email);
        if ($this->brdb->hasError()) {
            $this->tools->log('User', 'Login try', $email, 'POST');
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        $this->brdb->selectUserById(99);

        if ($dataSet == true) {
            // fetch the dataset there is only one and try to verify the passowrd
            $loadedUser = new User($dataSet);
            if (password_verify($pass, $loadedUser->passHash)) {
              $this->setSessionVariable(self::SESSION_LOGIN_USER_ID, intval($dataSet->{User::USER_CLM_ID}));
              // unset post var
              $this->unsetPostVariable(self::FORM_LOGIN_PASSWORD);
              // set success message
              $this->setSuccessMessage(self::SUCCESS_LOGIN);

              // ref
              if(isset($_SESSION['ref']) && strpos($_SESSION['ref'],$this->tools->getBaseUrl()) === true) {
                  $link = $_SESSION['ref'];
                  unset($_SESSION['ref']);
              } else {
                  $link = $this->tools->getBaseUrl();
              }
              $this->tools->customRedirect($link);
              return;
            }
            // Make an potential attacker wait for us
            sleep(self::PASSWORD_WAIT_FOR_WRONG);
        }

        $this->tools->log('User', 'Login try', $email, 'POST');
        $this->setFailedMessage(self::ERROR_LOGIN_UNKNOWN_EMAIL_PASSWORD);

    }

    private function processPostLogout() {
      // For a logout we just dump the session and forget about all the
      // things we knew so far such as the correctly logged in user ID
      // and all the other infos
      session_destroy();
      unset($this->loggedInUser);
      $_SESSION = array();
      session_start();
      $this->setSuccessMessage(self::SUCCESS_LOGOUT);
    }

    /**
     * This method checks for correct login. It either checks if there was a post statement.
     * If there wasn't it tries to check if there is a user set by the session.
     * @return boolean true in case the current user has a valid login
     */
    public function isUserLoggedIn() {
        // first unsset the current user to basically clear it
        // and remove all pending informations in case of a logout
        if (isset($this->loggedInUser)) {
            unset($this->loggedInUser);
        }

        // If there is no post we directly get here and we try to set the class
        // information directly from the stored information in the session
        if ($this->issetSessionVariable(self::SESSION_LOGIN_USER_ID)) {
            // Try to get the user by the ID stored in the session
            $userId = intval($this->getSessionVariable(self::SESSION_LOGIN_USER_ID));
            $dataSet = $this->brdb->selectUserById($userId);

            // if the query was succesful try to use the data to init the User object
            $this->loggedInUser = new User($dataSet);
            return true;
        }

        return false;
    }

    public function getLoggedInUser() {
        return $this->loggedInUser;
    }

    public function redirectUserIfNonAdmin() {
        if ( ! $this->loggedInUser->isAdmin()) {
            $this->setFailedMessage("User does not have the expected permissions.");
            $this->tools->customRedirect(array(
                'page' => 'index.php',
            ));
        }
    }

    public function redirectUserIfNonReporter() {
        if ( ! $this->loggedInUser->isReporter()) {
            $this->setFailedMessage("User does not have the expected permissions.");
            $this->tools->customRedirect(array(
                'page' => 'index.php',
            ));
        }
    }

    public function redirectUserIfnoRights($rights, $relationship = false) {
        if ( !is_array($rights) && is_string($rights)) {
            try {
                $tmp = array();
                $tmp[]= $rights;
                $rights = $tmp;
                unset($tmp);
            } catch (Exception $e) {
                $rights = array();
            }
        }

        $status = false;
        if (is_array($rights)) {
            foreach($rights as $right) {
                switch ($right) {
                    case 'admin':
                        $status = $this->loggedInUser->isAdmin();
                        break;

                    case 'reporter':
                        $status = $this->loggedInUser->isReporter();
                        break;
                    default:
                        continue;
                        break;
                }

                if ( $status && $relationship == 'or') {
                    return true;
                } else {
                    break;
                }
            }
        }

        if (! $status) {
            $this->setFailedMessage("User does not have the expected permissions.");
            $this->tools->customRedirect(array(
                'page' => 'index.php',
            ));
        }
        return $status;
    }

    public function redirectUserIfNotLoggindIn() {
        if ( ! $this->isUserLoggedIn()) {
            $this->setFailedMessage("No login. No access.");
            $this->tools->customRedirect(array(
                'page' => 'index.php',
            ));
        }
    }
}
?>
