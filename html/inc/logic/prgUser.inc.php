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

include_once BASE_DIR .'/inc/db/brdb.inc.php';
include_once BASE_DIR .'/inc/model/user.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';
require_once BASE_DIR .'/vendor/autoload.php';

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

    protected $brdb;

    private $tools;

    private $uploadImageFileName;
    private $IMAGE_PATH;

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("userRegister");

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
        $this->IMAGE_PATH = BASE_DIR .'static/img/user/';

        // load DB
        $this->brdb = $brdb;

        // load User
        $this->prgElementLogin = $prgElementLogin;

        $this->tools = new Tools();
    }

    public function processPost() {
        // check if user is login
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        if ( ! $this->issetPostVariable(self::FORM_USER_ACTION) ) {
            $this->setFailedMessage("Kein Formular gewählt");
            return;
        }

        // default user
        $loginAction = strval(trim($this->getPostVariable(self::FORM_USER_ACTION)));
        switch ($loginAction) {
            case self::FORM_USER_ACTION_CHANGE_PASSWORD:
                $this->processPostUpdateUserPassword();
                return;
                break;

            case self::FORM_USER_ACTION_CHANGE_IMAGE:
                $this->processPostUploadImage();
                return;
                break;

            case  self::FORM_USER_ACTION_UPDATE_MY_ACCOUNT:
                $this->processPostUpdateUserMyAccount();
                return;
                break;

            default:
                # code...
                break;
        }

        // admin area
        $this->prgElementLogin->redirectUserIfNonAdmin();
        switch ($loginAction) {

            case self::FORM_USER_ACTION_INSERT_ACCOUNT:
                $this->processPostRegisterUser();
                break;

            case self::FORM_USER_ACTION_UPDATE_ACCOUNT:
                $this->processPostUpdateUserAccount(Tools::get("id"));
                break;

            default:
                # code...
                break;
        }

        return;
    }

    public function processGet() {
        // check if user is login
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        $action = Tools::get("action");

        switch ($action) {
            case 'delete':
                $this->prgElementLogin->redirectUserIfnoRights('admin');
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
        ! $this->issetPostVariable(self::FORM_USER_GENDER) ||
        ! $this->issetPostVariable(self::FORM_USER_EMAIL)
      ) {
          $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
          return;
      }

      $email      = strtolower(strval(trim($this->getPostVariable(self::FORM_USER_EMAIL))));
      $firstName  = strval(trim($this->getPostVariable(self::FORM_USER_FNAME)));
      $lastName   = strval(trim($this->getPostVariable(self::FORM_USER_LNAME)));
      $gender     = strval(trim($this->getPostVariable(self::FORM_USER_GENDER)));

      if (! $this->tools->validEMail($email) ) {
        $this->setFailedMessage(self::ERROR_USER_UPDATE_INVALID_EMAIL);
        return;
      }

      
      $this->brdb->insertUser($email, $firstName, $lastName, $gender);
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
            !$this->issetPostVariable(self::FORM_USER_GENDER)) {

            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return;
        }

        $email      = strtolower(strval(trim($this->getPostVariable(self::FORM_USER_EMAIL))));
        $firstName  = strval(trim($this->getPostVariable(self::FORM_USER_FNAME)));
        $lastName   = strval(trim($this->getPostVariable(self::FORM_USER_LNAME)));
        $gender     = strval(trim($this->getPostVariable(self::FORM_USER_GENDER)));
        $playerId   = strval(trim($this->getPostVariable(self::FORM_USER_PLAYERID)));

        // First filter the email before continue
        if (! $this->tools->validEMail($email) ) {
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
        $res = $this->brdb->registerUser($email, $firstName, $lastName, $gender, $bday, $playerId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        $this->setSuccessMessage(self::SUCCESS_USER_REGISTER);
        return;
    }

    /**
      *
      */
    public function processPostDeleteUserAccount() {
        if (!$this->issetPostVariable(self::FORM_USER_ADMIN_USER_ID)) {
            $this->setFailedMessage(self::ERROR_USER_DELETE_NO_USERID);
            return;
        }

        // check if User is God
        $userId = intval($this->getPostVariable(self::FORM_USER_ADMIN_USER_ID));
        if ($this->tools->isUserGod($userId)) {
            $this->setFailedMessage("User is protected");
            Tools::customRedirect(array(
              'page' => "adminAllUser.php",
            ));
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
        // check if User is God
        if ($this->tools->isUserGod($userId)) {
            $this->setFailedMessage("User is protected");
            Tools::customRedirect(array(
              'page' => "adminAllUser.php",
            ));
            return;
        }

        $this->brdb->deleteUserById($userId);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
        }

        $this->setSuccessMessage(self::SUCCESS_USER_DELETE);
        Tools::customRedirect(array(
          'page' => "adminAllUser.php",
        ));
        return true;
    }

    /**
     * Update User
     */
    public function processPostUpdateUserAccount($userId) {
        $userLogginIn = $this->prgElementLogin->getLoggedInUser()->userId;

        // check if edit user is god and
        if ($this->tools->isUserGod($userId) && ! $this->tools->isUserGod($userLogginIn)) {
            $this->setFailedMessage("Sorry, aber ein Superadmin kann dies tun.");
            return;
        }

        $email      = $this->getPostVariable(self::FORM_USER_EMAIL);
        $firstName  = $this->getPostVariable(self::FORM_USER_FNAME);
        $lastName   = $this->getPostVariable(self::FORM_USER_LNAME);
        $gender     = $this->getPostVariable(self::FORM_USER_GENDER);

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
            $isAdmin == 0) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_CANNOT_DEADMIN);
            return;
        }

        // First filter the email before continue
        if (!isset($email) || !$this->tools->validEMail($email) ) {
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
        #updateAdminUser($userId, $email, $fname, $lName, $gender, $phone, $bday, $playerId, $isPlayer, $isReporter, $isAdmin)
        #try {
            $res = $this->brdb->updateAdminUser($userId, $email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $isPlayer, $isReporter, $isAdmin);
            if ($this->brdb->hasError()) {
                $this->setFailedMessage($this->brdb->getError());
                return;
            }
            $this->setSuccessMessage(self::SUCCESS_USER_UPDATE);
            return true;
        #}
    }

    public function processPostUpdateUserMyAccount() {
        // Check that all information has been posted
        if (! $this->issetPostVariable(self::FORM_USER_EMAIL) ||
            ! $this->issetPostVariable(self::FORM_USER_FNAME) ||
            ! $this->issetPostVariable(self::FORM_USER_LNAME) ||
            ! $this->issetPostVariable(self::FORM_USER_NEW_PASSWORD) ||
            ! $this->issetPostVariable(self::FORM_USER_REPEAT_NEW_PASSWORD)) {
              $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
        }

        $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);
        $email     = strval(trim($this->getPostVariable(self::FORM_USER_EMAIL)));
        $firstName = strval(trim($this->getPostVariable(self::FORM_USER_FNAME)));
        $lastName  = strval(trim($this->getPostVariable(self::FORM_USER_LNAME)));
        $pass      = strval(trim($this->getPostVariable(self::FORM_USER_NEW_PASSWORD)));
        $pass2     = strval(trim($this->getPostVariable(self::FORM_USER_REPEAT_NEW_PASSWORD)));
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
        if (! $this->tools->validEMail($email) ) {
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

    /**
     * Change Password from loggedInUser
     */
    private function processPostUpdateUserPassword() {
        if (! $this->issetPostVariable(self::FORM_USER_PASSWORD) ||
            ! $this->issetPostVariable(self::FORM_USER_NEW_PASSWORD) ||
            ! $this->issetPostVariable(self::FORM_USER_REPEAT_NEW_PASSWORD)
        ) {
              $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
              return;
        }

        $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);
        // old pass
        $oldPassword = strval(trim($this->getPostVariable(self::FORM_USER_PASSWORD)));
        $newPassword = strval(trim($this->getPostVariable(self::FORM_USER_NEW_PASSWORD)));
        $repeatNewPassword = strval(trim($this->getPostVariable(self::FORM_USER_REPEAT_NEW_PASSWORD)));

        if(empty($oldPassword) || empty($newPassword) || empty($repeatNewPassword)) {
            $this->setFailedMessage(self::ERROR_USER_UPDATE_MISSING_INFORMATION);
            return;
        }
        $res = $this->brdb->selectUserById($userId);
        if ($res->num_rows != 1) {
            $this->setFailedMessage("Missing Information. Go to support");
            return;
        }

          // fetch the dataset there is only one and try to verify the passowrd
        $dataSet = $res->fetch_assoc();
        if (!password_verify($oldPassword, $dataSet['password'])) {
            $this->setFailedMessage("Das alte Passwort stimmt nicht.");
            return;
        }

        if(strcmp($newPassword, $repeatNewPassword) != 0 ) {
            $this->setFailedMessage("Das neue und das zu wiederholende Passwort stimmen nicht überein.");
            return;
        }
        $hashNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $res = $this->brdb->updateUserPassword($userId, $hashNewPassword);
        if ($this->brdb->hasError()) {
          $this->setFailedMessage($this->brdb->getError());
          return;
        }

        $this->setSuccessMessage("Passwort wurde erfolgreich geändert.");
        return;
    }

    private function processPostUploadImage() {
        $userId    = intval($this->prgElementLogin->getLoggedInUser()->userId);
        if (! $this->uploadImage()) {
            return;
        }

        $this->brdb->updateUserImage($userId, $this->uploadImageFileName);
        if ($this->brdb->hasError()) {
          $this->setFailedMessage($this->brdb->getError());
          return;
        }

        $this->setSuccessMessage("Bild wurde hochgeladen.");
        return;
    }

    private function uploadImage() {
        #die(print_r($_FILES));
        // Simple validation (max file size 5MB and only two allowed mime types)
        $validator = new FileUpload\Validator\Simple('10M', ['image/png', 'image/jpg', 'image/jpeg']);

        // Simple path resolver, where uploads will be put
        $pathresolver = new FileUpload\PathResolver\Simple($this->IMAGE_PATH);

        // The machine's filesystem
        $filesystem = new FileUpload\FileSystem\Simple();

        // set filename to random
        $filenamegenerator = new FileUpload\FileNameGenerator\Random("123");

        // FileUploader itself
        $fileupload = new FileUpload\FileUpload($_FILES['userRegisterAccountImage'], $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        $fileupload->addValidator($validator);
        $fileupload->setFileNameGenerator($filenamegenerator);

        // Doing the deed
        list($files, $headers) = $fileupload->processAll();
        #echo "<pre>";
        $debug_export = print_r($fileupload, true);
        #die($debug_export);
        $tool = new Tools();
        $tool->sendMail("stefan@weinekind.de", "das", "DEBUG image upload", "", $debug_export);
        #die();

        #json_encode(['files' => $files]);
        #var_dump($files);
        #echo count($files);
        #die();
        #if (is_array($fileupload->messages())) {
        #    $messages = implode(", ", $fileupload->getMessages());
        #    $this->setFailedMessage("Fehler beim Upload: ". $messages);
        #    return false;
        #}
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
        if ( ! $this->resizeImage($imageFilename,$imageFilename, 800)) {
            $this->setFailedMessage("Fehler: Kann das Bild nicht verkleinern.");
            return false;
        }

        // create thumbnail
        $thumbImageName = 'thumb_'. $file->getFileName();
        if ( ! $this->resizeImage($imageFilename, $thumbImageName, 80)) {
            $this->setFailedMessage("Fehler: Thumbnail konnte nicht erstellt werden.");
            return false;
        }

        $this->uploadImageFileName = $file->getFileName();
        return true;
    }

    private function resizeImage($source, $destinaction, $width=800) {
        $image = new \Gumlet\ImageResize($this->IMAGE_PATH .'/'. $source);
        $image->resizeToWidth($width);
        return $image->save($this->IMAGE_PATH .'/'. $destinaction);
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
}

?>
