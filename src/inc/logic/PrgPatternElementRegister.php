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


use \Badtra\Intranet\Logic\APrgPatternElement;
use \Badtra\Intranet\Model\User;

// vendor
require_once BASE_DIR ."/vendor/autoload.php";
 
class PrgPatternElementRegister extends APrgPatternElement
{
    const FORM_USER_EMAIL         = "accountEmail";
    const FORM_USER_FNAME         = "accountFirstName";
    const FORM_USER_LNAME         = "accountLastName";
    const FORM_USER_GENDER        = "accountGender";
    const FORM_USER_PASSWORD      = "accountPassword";
    const FORM_USER_NEW_PASSWORD  = "accountNewPassword";
    const FORM_USER_REPEAT_NEW_PASSWORD     = "accountRepeatNewPassword";
    const FORM_USER_PHONE         = "accountPhone";
    const FORM_USER_BDAY          = "accountBday";
    const FORM_USER_PLAYERID      = "accountPlayerId";

    const FORM_USER_GENDER_MALE   = "Male";
    const FORM_USER_GENDER_FEMALE = "Female";

    const FORM_USER_IS_YES = "1";
    const FORM_USER_IS_NO  = "";

    const FORM_USER_ACTION                   = "formAction";
    const FORM_USER_ACTION_REGISTER          = "Register";
    const FORM_USER_ACTION_INSERT_ACCOUNT    = "Insert User";

    // Errors that can be set by methods of this class
    const ERROR_USER_DELETE_NO_USERID            = "Please select a user for deleting it!";
    const ERROR_USER_UPDATE_INVALID_EMAIL        = "Please use a valid email format!";
    const ERROR_USER_UPDATE_MISSING_INFORMATION  = "Please provide all required information!";
    const ERROR_USER_UPDATE_PASSWORD_MISSMATCH   = "Your passwords do not match!";
    const ERROR_USER_UPDATE_PASSWORD_INVALID     = "Please set a valid password!";
    const ERROR_USER_EMAIL_EXISTS                = "Email is already registered!";
    const SUCCESS_USER_REGISTER                  = "Succesfully registered account!";


    public function __construct(PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("userRegister");

        $this->prgElementLogin = $prgElementLogin;
    }

    public function processGet():void {}

    public function processPost(): void
    {

        if (! $this->issetPostVariable(self::FORM_USER_ACTION)) {
            $this->setFailedMessage("Kein Formular gewählt");
            return;
        }

        $loginAction = $this->getPostVariableString(self::FORM_USER_ACTION);
        $this->prgElementLogin->redirectUserIfNonAdmin();
        switch ($loginAction) {
            case self::FORM_USER_ACTION_INSERT_ACCOUNT:
                $this->processPostRegisterUser();
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Register User by himself
     *
     * @return boolean
     */
    public function processPostRegisterUser():bool
    {
        try {
            $requireFields = array(self::FORM_USER_FNAME, self::FORM_USER_LNAME, self::FORM_USER_GENDER, self::FORM_USER_EMAIL);
            if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
                throw new \Exception(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            }

            $user = new \Badtra\Intranet\Model\User();

            $email      = strtolower($this->getPostVariableString(self::FORM_USER_EMAIL));
            $firstName  = $this->getPostVariableString(self::FORM_USER_FNAME);
            $lastName   = $this->getPostVariableString(self::FORM_USER_LNAME);
            $gender     = $this->getPostVariableString(self::FORM_USER_GENDER);
            $playerId   = $this->getPostVariableString(self::FORM_USER_PLAYERID);
            $bday       = $this->issetPostVariable(self::FORM_USER_BDAY) ? date("Y-m-d", strtotime($this->getPostVariable(self::FORM_USER_BDAY))) : "";

            // First filter the email before continue
            if (! $this->validEMail($email)) {
                throw new \Exception(self::ERROR_USER_UPDATE_INVALID_EMAIL);
            }
            // Check if the user is already registered
            // In case it is we have to throw an error
            $res = $this->brdb->selectUserbyEmail($email);
            if (!isset($res) || !is_array($res)) {
                throw new \Exception(sprintf("Cannot find User with E-Mail \"%s\"", $email));
            }

            // now everything is checked and the command for adding
            // the user can be called
            if (!$this->brdb->registerUser($email, $firstName, $lastName, $gender, $bday, $playerId)) {
                throw new \Exception("Cannot create User");
            }
           

            $this->setSuccessMessage(self::SUCCESS_USER_REGISTER);
            return true;
           
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot Register User. %s Details %s", $user, $e->getMessage()), "", "POST");
            $this->setFailedMessage($e->getMessage());
            return false;
        }
    }
}