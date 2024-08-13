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

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PrgPatternElementUser
 *
 * This class is responsible for the user management
 *
 * @category Badtra
 * @package  Badtra\Intranet\Logic
 */

class PrgPatternElementUser extends APrgPatternElement
{
    const __TABLE__ = "user";
    const FORM_USER_ADMIN_USER_ID = "accountAdminUserId";
    const FORM_USER_EMAIL         = "accountEmail";
    const FORM_USER_FNAME         = "accountFirstName";
    const FORM_USER_LNAME         = "accountLastName";
    const FORM_USER_GENDER        = "accountGender";
    const FORM_USER_IS_PLAYER     = "accountIsPlayer";
    const FORM_USER_IS_ADMIN      = "accountIsAdmin";
    const FORM_USER_IS_REPORTER   = "accountIsReporter";
    const FORM_USER_PASSWORD      = "accountPassword";
    const FORM_USER_NEW_PASSWORD  = "accountNewPassword";
    const FORM_USER_REPEAT_NEW_PASSWORD     = "accountRepeatNewPassword";
    const FORM_USER_PHONE         = "accountPhone";
    const FORM_USER_BDAY          = "accountBday";
    const FORM_USER_PLAYERID      = "accountPlayerId";
    const FORM_USER_CLUBID        = "accountClubId";
    const FORM_USER_IMAGE         = "accountImage";

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
    const FORM_USER_ACTION_CHANGE_PASSWORD   = "changePassword";
    const FORM_USER_ACTION_CHANGE_IMAGE      = "changeImage";

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

    private $uploadImageFileName;
    private $IMAGE_PATH;

    public function __construct(PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("userRegister");

        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_USER_EMAIL);
        $this->registerPostSessionVariable(self::FORM_USER_FNAME);
        $this->registerPostSessionVariable(self::FORM_USER_LNAME);
        $this->registerPostSessionVariable(self::FORM_USER_GENDER);
        $this->registerPostSessionVariable(self::FORM_USER_PLAYERID);
        $this->registerPostSessionVariable(self::FORM_USER_IS_PLAYER);
        $this->registerPostSessionVariable(self::FORM_USER_IS_ADMIN);
        $this->registerPostSessionVariable(self::FORM_USER_IS_REPORTER);
        $this->registerPostSessionVariable(self::FORM_USER_ADMIN_USER_ID);

        // set Image Path
        $this->IMAGE_PATH = __BASE_DIR__ ."/static/img/user/";
       
    }

    public function processGet():void {}

    public function processPost() {
        // check if user is login
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        if (! $this->issetPostVariable(self::FORM_USER_ACTION) )
        {
            $this->setFailedMessage("Kein Formular gewählt");
            return;
        }

        // default user
        $loginAction = $this->getPostVariableString(self::FORM_USER_ACTION);
        switch ($loginAction)
        {
            case self::FORM_USER_ACTION_CHANGE_PASSWORD:
                $this->processPostUpdateUserPassword();
                break;

            case self::FORM_USER_ACTION_CHANGE_IMAGE:
                $this->processPostUploadImage();
                break;

            case self::FORM_USER_ACTION_UPDATE_MY_ACCOUNT:
                $this->processPostUpdateUserMyAccount();
                break;

            default:
                # code...
                break;
        }

        // admin area
        $this->prgElementLogin->redirectUserIfNonAdmin();
        switch ($loginAction)
        {
            case self::FORM_USER_ACTION_UPDATE_ACCOUNT:
                $this->processPostUpdateUserAccount($this->getGetVariable("id"));
                break;
            default:
                # code...
                break;
        }

        return;
    }


    /**
     * Inser new User
     *
     * @return boolean
     */
    private function processPostInsertUserAccount(): bool
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_USER_FNAME, self::FORM_USER_LNAME, self::FORM_USER_GENDER, self::FORM_USER_EMAIL);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return false;
        }

        $email      = strtolower($this->getPostVariableString(self::FORM_USER_EMAIL));
        $firstName  = $this->getPostVariableString(self::FORM_USER_FNAME);
        $lastName   = $this->getPostVariableString(self::FORM_USER_LNAME);
        $gender     = $this->getPostVariableString(self::FORM_USER_GENDER);

        if (! $this->validEMail($email) )
        {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_INVALID_EMAIL);
            return false;
        }

     
        ;
        if (!$this->brdb->insertUser($email, $firstName, $lastName, $gender))
        {
            $this->setFailedMessage("Nutzer konnte nicht angelegt werden.");
            return false;
        }
        $id = $this->brdb->insert_id();

        if (!$this->processPostUpdateUserAccount($id))
        {
            $this->setFailedMessage("Probleme beim anlegen. Bitte editieren Sie den Nutzer!");
            $this->customRedirectArray(array(
                "page"   => "adminAllUser.php",
                "action" => "edit",
                "id"     => $id,
            ));
            return false;
        }

        $this->setSuccessMessage(sprintf("Nutzer \"%s %s\" wurde angelegt", $firstName, $lastName));
        $this->customRedirectArray(array(
            "page"   => "adminAllUser.php",
            "action" => "edit",
            "id"     => $id,
        ));
        return true;

    }

    

    /**
     * Delete User
     *
     * @return boolean
     */
    public function processPostDeleteUserAccount(): bool
    {
        try
        {
            if (!$this->issetPostVariable(self::FORM_USER_ADMIN_USER_ID))
            {
                throw new \Exception(self::ERROR_USER_DELETE_NO_USERID);
            }

            $userId = $this->getPostVariableInt(self::FORM_USER_ADMIN_USER_ID);
           
            if ($this->isUserGod($userId))
            {
                throw new \Exception("User is protected");
            }

            if (!$this->brdb->deleteUserById($userId)) {
                throw new \Exception("Cannote delete User");
            }

            $this->setSuccessMessage(self::SUCCESS_USER_DELETE);
            return true;

        }
        catch (\Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot Delete User. %s Details %s", $userId, $e->getMessage()), "", "POST");
            $this->setFailedMessage($e->getMessage());
            $this->customRedirectArray(array(
              "page" => "adminAllUser.php",
            ));
            return false;
        }
    }


    /**
     * Update User
     *
     * @param integer $userId
     * @return boolean
     */
    public function processPostUpdateUserAccount(int $userId): bool
    {
        try {
            $userLogginIn = $this->prgElementLogin->getLoggedInUser()->userId;

            // check if edit user is god and
            if ($this->isUserGod($userId) && ! $this->isUserGod($userLogginIn))
            {
                throw new \Exception("Sorry, aber ein Superadmin kann dies tun.");
            }
   
            $email      = $this->getPostVariableString(self::FORM_USER_EMAIL);
            $firstName  = $this->getPostVariableString(self::FORM_USER_FNAME);
            $lastName   = $this->getPostVariableString(self::FORM_USER_LNAME);
            $gender     = $this->getPostVariableString(self::FORM_USER_GENDER);
   
            // is Admin
            $isAdmin = $this->issetPostVariable(self::FORM_USER_IS_ADMIN) ? $this->getPostVariable(self::FORM_USER_IS_ADMIN) : 0;
   
            // isReporter
            $isReporter = $this->issetPostVariable(self::FORM_USER_IS_REPORTER) ? $this->getPostVariable(self::FORM_USER_IS_REPORTER) : 0;
   
            // isPlayer
            $isPlayer = $this->issetPostVariable(self::FORM_USER_IS_PLAYER) ? $this->getPostVariable(self::FORM_USER_IS_PLAYER) : 0;
   
   
            // additional
            $playerId = $this->getPostVariable(self::FORM_USER_PLAYERID);
            $phone    = $this->getPostVariable(self::FORM_USER_PHONE);
            $bday     = $this->issetPostVariable(self::FORM_USER_BDAY) ? date("Y-m-d", strtotime($this->getPostVariable(self::FORM_USER_BDAY))) : "";
   
            // Check if oyu try to deadmin yourself this is not allowed, otherwise
            // it would be possible to lockout oneself from the data base
            if ($userId == $this->prgElementLogin->getLoggedInUser()->userId &&
                $this->prgElementLogin->getLoggedInUser()->isAdmin() &&
                $isAdmin == 0)
                {
                throw new \Exception(self::ERROR_USER_UPDATE_CANNOT_DEADMIN);
            }
   
            // First filter the email before continue
            if (!isset($email) || !$this->validEMail($email))
            {
                throw new \Exception(self::ERROR_USER_UPDATE_INVALID_EMAIL);
            }
   
            $user = $this->brdb->selectUserbyEmail($email);
            if (!isset($user) || !is_array($user))
            {
                throw new \Exception(sprintf("Cannot find User with E-Mail \"%s\"", $email));
            }
   
            // Check that it is not my email, which is allowed to be set!
            $dataSetUser = new \Badtra\Intranet\Model\User($user);
            if ($dataSetUser->userId != $userId)
            {
                throw new \Exception(self::ERROR_USER_EMAIL_EXISTS);
            }
   
   
            if (!$this->brdb->updateAdminUser($userId, $email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $isPlayer, $isReporter, $isAdmin))
            {
                throw new \Exception("Cannot update User");
            }
   
            $this->setSuccessMessage(self::SUCCESS_USER_UPDATE);
            return true;
        }
        catch (\Exception $e)
        {
            // Log
            $details = sprintf("Cannot Update User. %s", $userId);
            $message = $e->getMessage();
            $this->log($this->__TABLE__, $details, $message, "POST");
            unset($details, $message);

            // Prepaire user message
            $this->setFailedMessage($e->getMessage());
            $this->customRedirectArray(array(
                "page" => "adminAllUser.php",
            ));
            return false;
        }
       
    }

    public function processPostUpdateUserMyAccount()
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_USER_EMAIL, self::FORM_USER_FNAME, self::FORM_USER_LNAME, self::FORM_USER_NEW_PASSWORD, self::FORM_USER_REPEAT_NEW_PASSWORD);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return false;
        }

        try
        {
            $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);
            $email     = strtolower($this->getPostVariableString(self::FORM_USER_EMAIL));
            $firstName = $this->getPostVariableString(self::FORM_USER_FNAME);
            $lastName  = $this->getPostVariableString(self::FORM_USER_LNAME);
            $pass      = $this->getPostVariableString(self::FORM_USER_NEW_PASSWORD);
            $pass2     = $this->getPostVariableString(self::FORM_USER_REPEAT_NEW_PASSWORD);
            $passHash  = password_hash($pass, PASSWORD_DEFAULT);
            $phone     = $this->getPostVariableString(self::FORM_USER_PHONE);
            $gender    = $this->getPostVariableString(self::FORM_USER_GENDER);
            $bday      = date("Y-m-d", strtotime($this->getPostVariableString(self::FORM_USER_BDAY)));

            if (!empty($pass) && !empty($pass2) && $pass != $pass2)
            {
                throw new \Exception("self::ERROR_USER_UPDATE_PASSWORD_MISSMATCH");
            }
       
            $this->brdb->updateUserPassword($userId, $passHash);

            // First filter the email before continue
            if (! $this->validEMail($email))
            {
                throw new \Exception(self::ERROR_USER_UPDATE_INVALID_EMAIL);
            }

            // Check if the user is already registered
            // In case it is we have to throw an error
            $user = $this->brdb->selectUserbyEmail($email);


            // Check that it is not my email, which is allowed to be set!
            $dataSetUser = new \Badtra\Intranet\Model\User($user);
            if ($dataSetUser->userId != $userId)
            {
                throw new \Exception(self::ERROR_USER_EMAIL_EXISTS);
            }

            if (!$this->brdb->updateUser($userId, $email, $firstName, $lastName, $gender, $phone, $bday))
            {
                throw new \Exception("Cannot Update User");
            }

            $this->setSuccessMessage(self::SUCCESS_USER_UPDATE);
            return true;
        }
        catch (\Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot Update User Information. Details %s", $e->getMessage()), "", "POST");
            $this->setFailedMessage($e->getMessage());
            return false;
        }  

       
    }

    /**
     * Change Password from loggedInUser
     */
    private function processPostUpdateUserPassword(): bool
    {

        $data = $_POST;
        // Validator erstellen
        $validator = Validation::createValidator();

        // Constraints für jedes Feld definieren
        $constraints = new Assert\Collection([
            'current_password' => [
                new Assert\NotBlank(),
                new Assert\Callback(function ($currentPassword, $context) use ($currentPasswordInDatabase) {
                    if ($currentPassword !== $currentPasswordInDatabase) {
                        $context->buildViolation('Das aktuelle Passwort ist falsch.')
                            ->addViolation();
                    }
                }),
            ],
            'new_password' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 8]),
                // Hier könnten zusätzliche Regeln wie Zahlen, Sonderzeichen usw. hinzugefügt werden
            ],
            'confirm_new_password' => [
                new Assert\NotBlank(),
                new Assert\EqualTo([
                    'value' => $data['new_password'],  // Muss mit dem neuen Passwort übereinstimmen
                    'message' => 'Die neuen Passwörter müssen übereinstimmen.',
                ]),
            ],
        ]);

        // Daten validieren
        $violations = $validator->validate($data, $constraints);

        // Daten validieren
        $violations = $validator->validate($data, $constraints);

        // Validierungsergebnisse auswerten
        if (count($violations) > 0) {
            // Es gibt Validierungsfehler
            foreach ($violations as $violation) {
                echo $violation->getPropertyPath() . ': ' . $violation->getMessage() . "\n";
            }
        } else {
            echo "Das Formular wurde erfolgreich validiert!";
        }

        $requireFields = array(self::FORM_USER_PASSWORD, self::FORM_USER_NEW_PASSWORD, self::FORM_USER_REPEAT_NEW_PASSWORD);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return false;
        }

        try {
            $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);
            // old pass
            $oldPassword       = $this->getPostVariableString(self::FORM_USER_PASSWORD);
            $newPassword       = $this->getPostVariableString(self::FORM_USER_NEW_PASSWORD);
            $repeatNewPassword = $this->getPostVariableString(self::FORM_USER_REPEAT_NEW_PASSWORD);

            if (empty($oldPassword) || empty($newPassword) || empty($repeatNewPassword))
            {
                throw new \Exception(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            }

            $user = $this->brdb->selectUserById($userId);
            if (!isset($user) || empty ($user))
            {
                throw new \Exception("Missing Information. Go to support");
            }

            // fetch the dataset there is only one and try to verify the passowrd
            if (!password_verify($oldPassword, $user["password"]))
            {
                throw new \Exception("Das alte Passwort stimmt nicht.");
            }

            if (strcmp($newPassword, $repeatNewPassword) != 0 )
            {
                throw new \Exception("Das neue und das zu wiederholende Passwort stimmen nicht überein.");
            }

            $hashNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->brdb->updateUserPassword($userId, $hashNewPassword);

            $this->setSuccessMessage("Passwort wurde erfolgreich geändert.");
            return true;

        }
        catch (\Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot Update User Information. Details %s", $e->getMessage()), "", "POST");
            $this->setFailedMessage($e->getMessage());
            return false;
        }  
    }

    private function processPostUploadImage() {
        $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);

        if (!$this->uploadImage() || !$this->brdb->updateUserImage($userId, $this->uploadImageFileName)) {
          $this->setFailedMessage("Image couldnt uploaded!");
          return;
        }

        $this->setSuccessMessage("Bild wurde hochgeladen.");
        return;
    }

    private function uploadImage() {
        // Simple validation (max file size 5MB and only two allowed mime types)
        $validator = new \FileUpload\Validator\Simple("10M", ["image/png", "image/jpg", "image/jpeg"]);

        // Simple path resolver, where uploads will be put
        $pathresolver = new \FileUpload\PathResolver\Simple($this->IMAGE_PATH);

        // The machine"s filesystem
        $filesystem = new \FileUpload\FileSystem\Simple();

        // set filename to random
        $filenamegenerator = new \FileUpload\FileNameGenerator\Random("123");

        // FileUploader itself
        $fileupload = new \FileUpload\FileUpload($_FILES["userRegisterAccountImage"], $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        $fileupload->addValidator($validator);
        $fileupload->setFileNameGenerator($filenamegenerator);

        // Doing the deed
        list($files, $headers) = $fileupload->processAll();
        $debug_export = print_r($fileupload, true);
        $this->sendMail("stefan@weinekind.de", "das", "DEBUG image upload", "", $debug_export);


        if (! is_array($files) || count($files) != 1) {
            $this->setFailedMessage("Fehler beim Upload");
            #$this->setFailedMessage(var_dump($file));
            return false;
        }

        $file = $files[0];
        if ($file->error > 0) {
            $this->setFailedMessage("Fehler beim Upload");
            return false;
        }

        // resize image
        $imageFilename = $file->getFileName();
        if (! $this->resizeImage($imageFilename,$imageFilename, 800)) {
            $this->setFailedMessage("Fehler: Kann das Bild nicht verkleinern.");
            return false;
        }

        // create thumbnail
        $thumbImageName = "thumb_". $file->getFileName();
        if (! $this->resizeImage($imageFilename, $thumbImageName, 80)) {
            $this->setFailedMessage("Fehler: Thumbnail konnte nicht erstellt werden.");
            return false;
        }

        $this->uploadImageFileName = $file->getFileName();
        return true;
    }

    private function resizeImage($source, $destinaction, $width=800) {
        $image = new \Gumlet\ImageResize($this->IMAGE_PATH ."/". $source);
        $image->resizeToWidth($width);
        return $image->save($this->IMAGE_PATH ."/". $destinaction);
    }

    public function getAdminUser() {
        // If there is no post we directly get here and we try to set the class
        // information directly from the stored information in the session
        if (! $this->issetSessionVariable(self::FORM_USER_ADMIN_USER_ID)) {
            $this->setFailedMessage(self::ERROR_USER_NO_USER_FOR_ADMIN_SELECTED);
            return;
        }

        // Try to get the user by the ID stored in the session
        $userId = intval($this->getSessionVariable(self::FORM_USER_ADMIN_USER_ID));
        $user = $this->brdb->selectUserById($userId);
        if (!isset($user) || empty($user)) {
            $this->setFailedMessage(self::ERROR_USER_NO_USER_FOR_ADMIN_FOUND);
            return;
        }
       
        return new \Badtra\Intranet\Model\User($user);
    }
}
