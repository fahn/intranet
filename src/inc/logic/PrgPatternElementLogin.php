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

class PrgPatternElementLogin extends APrgPatternElement
{
   
    const __TABLE__ = "Login";
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



    public function __construct() {
        parent::__construct("login");

        $this->registerPostSessionVariable(self::FORM_LOGIN_ACTION);
        $this->registerPostSessionVariable(self::FORM_LOGIN_EMAIL);

        $server_scriptname = $_SERVER["PHP_SELF"];
        if (strstr($server_scriptname, "pages/logout.php", true))
        {
            $this->processPostLogout();
        }

    }

    public function processPost(): void
    {
        if ($this->issetPostVariable(self::FORM_LOGIN_ACTION))
        {
            $loginAction = $this->getPostVariableString(self::FORM_LOGIN_ACTION);

            switch ($loginAction)
            {
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


    /**
     * Request new Password
     *
     * @return boolean
     */
    private function processPostRequestPassword(): bool
    {
        try {
            if ($this->isUserLoggedIn())
            {
                throw new \Exception("Dieser Bereich steht nur nicht angemeldeltet Personen zur Verfügung");
            }

            if (!$this->issetPostVariable(self::FORM_LOGIN_EMAIL))
            {
                throw new \Exception("no valid mail address");
                return false;
            }

            $mail = $this->getPostVariableString(self::FORM_LOGIN_EMAIL);
            if (! $this->validEmail($mail)) {
                throw new \Exception("no valid mail address");
                return false;
            }

            $userData = $this->brdb->selectUserByEmail($mail);
            if (!isset($userData) || !is_array($userData))
            {
                throw new \Exception("User exisitiert nicht.");
            }

            $ip = $this->getUserIPAdress();

            // create token
            $token = hash("sha256", time() . rand(1, 9999));

            // insert token and userid
            if (!$this->brdb->insertUserPassHash($userData["userId"], $token, $ip))
            {
                throw new \Exception("Cannot prepaire Password change");
            }

            $name      = $userData["firstName"];
            $subject   = "Dein Password wurde angefordert.";

            $link = $this->linkTo(array(
                "page"   => "index.php",
                "action" => "change_password",
                "token"  => $token,
                "mail"   => base64_encode($mail)
            ));

            $assign    = array(
                "name"    => $name,
                "link"    => $link,
                "baseUrl" => $this->settings->getSettingString("SITE_URL"),
            );

            $message   = "";
            if (!$this->sendMail($mail, $name, $subject, "Dein Passwort wurde angefordert", $message, $assign, "htmlmail/request_password.tpl")) {
                throw new \Exception("Fehler beim E-Mail-Versand.");
            }

            $this->setSuccessMessage("Ihr Password wurde angefordert.");
            $this->customRedirectString($this->getBaseUrl());
            return true;
        }
        catch (\Exception $e)
        {
            $this->log("User", "Cannot request new Password 1/2", sprintf("User: %s; Details %s", $mail, $e->getMessage()), "POST");
            return false;
        }
    }

    /**
     * Change Password after request new Password
     *
     * @return boolean
     */
    private function processPostChangePassword(): bool
    {
        try {
            $requireFields = array(self::FORM_LOGIN_EMAIL, self::FORM_LOGIN_TOKEN, self::FORM_LOGIN_PASSWORD, self::FORM_LOGIN_PASSWORD2);
            if (! $this->prgElementLogin->checkRequiredFields($requireFields))
            {
                throw new \Exception("Formulardaten sind falsch.");
            }

            $mail  = base64_decode($this->getPostVariable(self::FORM_LOGIN_EMAIL));

            $token = $this->getPostVariableString(self::FORM_LOGIN_TOKEN);
            $pass  = $this->getPostVariableString(self::FORM_LOGIN_PASSWORD);
            $pass2 = $this->getPostVariableString(self::FORM_LOGIN_PASSWORD2);

            // valid mail
            if (! $this->validEmail($mail))
            {
                throw new \Exception("no valid mail address");
            }

            // check if Password = Password2
            if (strlen($pass) == 0)
            {
                throw new \Exception("Bitte gebe ein Passwort ein.");
            }
            if ($pass != $pass2)
            {
                throw new \Exception("Passwörter stimmen nicht überein.");
            }

            // check if token exists
            $data = $this->brdb->GetUserPassHash($mail, $token);
            if (!isset($data) || !is_array($data))
            {
                throw new \Exception("Bitte bei dem Support melden.");
            }
            $passHash = $this->createPasswordHash($pass);

            // delete token
            if ($this->brdb->DeleteUserPassHash($data["userId"], $token))
            {
                throw new \Exception("Bitte bei dem Support melden.");
            }

            // set new password
           
            if (!$this->brdb->updateUserPassword($data["userId"], $passHash))
            {
                throw new \Exception("Bitte bei dem Support melden.");
            }

            $this->setSuccessMessage("Passwort wurde geändert. Bitte nun einloggen");
            $this->customRedirectString($this->getBaseUrl());
            return true;
        }
        catch (\Exception $e)
        {
            $this->log("User", "Cannot request new Password 2/2", sprintf("User: %s; Details %s", $mail, $e->getMessage()), "POST");
            return false;
        }
    }


    /**
     * Login process
     *
     * @return boolean
     */
    private function processPostLogin(): bool
    {
        try {
            if ($this->isBadLogin()) {
                throw new \Exception("Kein Login möglich. Bitte versuchen Sie es später noch einmal");
            }

            $requireFields = array(self::FORM_LOGIN_EMAIL, self::FORM_LOGIN_PASSWORD);
            if (! $this->checkRequiredFields($requireFields))
            {
                throw new \Exception(self::ERROR_LOGIN_INVALID_EMAIL ."1");
            }

            $email     = $this->getPostVariableString(self::FORM_LOGIN_EMAIL);
            $pass      = $this->getPostVariableString(self::FORM_LOGIN_PASSWORD);

            // filter the email to avoid having other wired stuff being
            // injected to the php code or the database maybe
           
            // Now see if there is a user in the data base with correct
            // email and hashed password. Passwords are hashed with php hash functionality

            if (! $this->validEmail($email)) {
                throw new \Exception(self::ERROR_LOGIN_INVALID_EMAIL);
            }

            $dataSet = $this->brdb->selectUserByEmail($email);
            if (!isset($dataSet) || !is_array($dataSet)) {
                throw new \Exception("User not exists");
            }

            $loadedUser = new \Badtra\Intranet\Model\User($dataSet);
            if (!password_verify($pass, $loadedUser->passHash)) {
                throw new \Exception("Password not matches");
            }

            $userId = intval($loadedUser->getUserId());
            // set lastLogin
           
            $this->brdb->setUserLastLogin($userId);

            // SET SESSION
            $this->setSessionVariable(self::SESSION_LOGIN_USER_ID, $userId);
            // unset post var
            $this->unsetPostVariable(self::FORM_LOGIN_PASSWORD);
            // set success message
            $this->setSuccessMessage(self::SUCCESS_LOGIN);

            // ref
            if (isset($_SESSION["ref"]) && strpos($_SESSION["ref"], $this->getBaseUrl()) === true)
            {
                $link = $_SESSION["ref"];
                unset($_SESSION["ref"]);
            } else {
                $link = $this->getBaseUrl();
            }

            $this->customRedirectString($link);
            return true;

        } catch (\Exception $e)
        {
            $this->log("User", "Login try", sprintf("User: %s; Details %s", $email, $e->getMessage()), "POST");
            $this->setFailedMessage($e->getMessage());
            return false;
        }

    }

    private function returnRefUrl() {
  
    }

    /**
     * check if user tried to log in often
     *
     * @return boolean
     */
    private function isBadLogin(): bool
    {
        if (! $this->isProductiv())
        {
            return false;
        }
        // set
        $_SESSION["badlogin"] = !isset($_SESSION["badlogin"]) ? 0 : $_SESSION["badlogin"]+1;

        // return
        return isset($_SESSION["badlogin"]) && $_SESSION["badlogin"] >= 5 ? true : false;

       
    }

    /**
     * Logout
     *
     * @return void
     */
    private function processPostLogout(): void
    {
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
     * If there wasn"t it tries to check if there is a user set by the session.
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
            $this->loggedInUser = new \Badtra\Intranet\Model\User($dataSet);
            return true;
        }

        return false;
    }

    public function getLoggedInUser() {
        return $this->loggedInUser;
    }

    public function redirectUserIfNonAdmin() {
        if (! $this->loggedInUser->isAdmin()) {
            $this->setFailedMessage("User does not have the expected permissions.");
            $this->customRedirectArray(array(
                "page" => "index.php",
            ));
        }
    }

    public function redirectUserIfNonReporter() {
        if (! $this->loggedInUser->isReporter()) {
            $this->setFailedMessage("User does not have the expected permissions.");
            $this->customRedirectArray(array(
                "page" => "index.php",
            ));
        }
    }

    public function redirectUserIfnoRights($rights, $relationship = false) {
        if (!is_array($rights) && is_string($rights)) {
            try {
                $tmp = array();
                $tmp[]= $rights;
                $rights = $tmp;
                unset($tmp);
            } catch (\Exception $e) {
                $rights = array();
            }
        }

        $status = false;
        if (is_array($rights)) {
            foreach($rights as $right) {
                switch ($right) {
                    case "admin":
                        $status = $this->loggedInUser->isAdmin();
                        break;

                    case "reporter":
                        $status = $this->loggedInUser->isReporter();
                        break;
                    default:
                        return;
                        break;
                }

                if ( $status && $relationship == "or") {
                    return true;
                } else {
                    break;
                }
            }
        }

        if (! $status) {
            $this->setFailedMessage("User does not have the expected permissions.");
            $this->customRedirectArray(array(
                "page" => "index.php",
            ));
        }
        return $status;
    }

    public function redirectUserIfNotLoggindIn() {
        if (! $this->isUserLoggedIn()) {
            $this->setFailedMessage("No login. No access.");
            $this->customRedirectArray(array(
                "page" => "index.php",
            ));
        }
    }
}

