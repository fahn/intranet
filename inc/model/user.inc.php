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

/**
 * This class implements a user object to simplyfy access to the data base
 * @author philipp
 *
 */
class User {

    // Constants for the User table in the database
    const USER_CLM_ID       = "userId";
    const USER_CLM_LNAME    = "lastName";
    const USER_CLM_FNAME    = "firstName";
    const USER_CLM_EMAIL    = "email";
    const USER_CLM_GENDER   = "gender";
    const USER_CLM_PLAYER   = "activePlayer";
    const USER_CLM_ADMIN    = "admin";
    const USER_CLM_REPORTER = "reporter";
    const USER_CLM_PASS     = "password";
    const USER_CLM_PLAYERID = "playerId";
    const USER_CLM_CLUBID   = "clubId";
    const USER_CLM_PHONE    = "phone";
    const USER_CLM_BDAY     = "bday";
    const USER_CLM_IMAGE    = "image";
    
    const _USER_IMAGE_PATH_   = "/static/img/user/";
    const _USER_IMAGE_MALE_   = "default_m.png";
    const _USER_IMAGE_FEMALE_ = "default_w.png";

    public $userId;
    public $email;
    public $firstName;
    public $lastName;
    public $gender;
    private $isAdmin;
    private $isPlayer;
    private $isReporter;
    public $passHash;
    public $playerId;
    public $clubId;
    public $clubName;
    public $phone;
    public $bday;
    private $userImage;

    /**
     * Conmstructor that knows how to retrieve all fields from a given data set
     * @param array $dataSet a data set prefrably directly from an SQL statement
     */
    public function __construct($dataSet = null) {
        if ($dataSet != null) {
            $this->userId     = intval($dataSet[self::USER_CLM_ID]);
            $this->email      = strval($dataSet[self::USER_CLM_EMAIL]);
            $this->firstName  = strval($dataSet[self::USER_CLM_FNAME]);
            $this->lastName   = strval($dataSet[self::USER_CLM_LNAME]);
            $this->gender     = strval($dataSet[self::USER_CLM_GENDER]);
            $this->isAdmin    = boolval($dataSet[self::USER_CLM_ADMIN]);
            $this->isPlayer   = boolval($dataSet[self::USER_CLM_PLAYER]);
            $this->isReporter = boolval($dataSet[self::USER_CLM_REPORTER]);
            $this->passHash   = strval($dataSet[self::USER_CLM_PASS]);
            $this->playerId   = strval($dataSet[self::USER_CLM_PLAYERID]);
            $this->clubId     = strval($dataSet[self::USER_CLM_CLUBID]);
            $this->phone      = strval($dataSet[self::USER_CLM_PHONE]);
            $this->bday       = strval($dataSet[self::USER_CLM_BDAY]);
            $this->clubName   = "";
            $this->userImage  = strval($dataSet[self::USER_CLM_IMAGE]);
        } else {
            $this->userId     = 0;
            $this->email      = "N/A";
            $this->firstName  = "N/A";
            $this->lastName   = "N/A";
            $this->gender     = "N/A";
            $this->isAdmin    = false;
            $this->isPlayer   = false;
            $this->isReporter = false;
            $this->passHash   = "N/A";
            $this->playerId   = "";
            $this->clubId     = "";
            $this->clubName   = "";
            $this->phone      = "";
            $this->bday       = "";
            $this->userImage  = $this->getDefaultUserImage();
        }
    }

    /**
     * Method to retrieve the full name consisting of first and last name
     * @return string the full name
     */
    public function getFullName() {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }
    
    
    public function getUserImage() {
        return strlen($this->userImage) == 0 ? $this->getDefaultUserImage : self::_USER_IMAGE_PATH_ ."/". $this->userImage;
    }
    
    public function getDefaultUserImage() {
        return self::_USER_IMAGE_PATH_  . ($this->gender == 'Male' ? self::_USER_IMAGE_MALE_ : self::_USER_IMAGE_FEMALE_);
    }

    public function getID() {
        return $this->userId;
    }

    public function isAdmin() {
        return $this->isAdmin == true;
    }

    public function isPlayer() {
        return $this->isPlayer == true;
    }

    public function isReporter() {
        return $this->isReporter == true;
    }

}
?>
