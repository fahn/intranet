<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

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

    const USER_IMAGE_PATH   = "/static/img/user/";
    const USER_IMAGE_MALE   = "default_m.png";
    const USER_IMAGE_FEMALE = "default_w.png";

    public int $userId;
    public string $email;
    public string $firstName;
    public string $lastName;
    public string $gender;
    private bool $isAdmin = false;
    private bool $isPlayer = false;
    private bool $isReporter = false;
    public string $passHash;
    public string $playerId;
    public int $clubId;
    public string $clubName;
    public string $phone;
    public string $bday;
    private string $userImage;

    /**
     * Conmstructor that knows how to retrieve all fields from a given data set
     * @param array $dataSet a data set prefrably directly from an SQL statement
     */
    public function __construct($dataSet = null) {
        if (!isset($dataSet) || $dataSet == null) {
            throw new Exception("NO USER MODELL");
        }
       
       
        if (isset($dataSet) && is_array($dataSet)) {
            try {
               
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
                //$this->clubId     = intval($dataSet->self::USER_CLM_CLUBID]);
                $this->phone      = strval($dataSet[self::USER_CLM_PHONE]);
                $this->bday       = strval($dataSet[self::USER_CLM_BDAY]);
                $this->clubName   = "";
                $this->userImage  = strval($dataSet[self::USER_CLM_IMAGE]);
            } catch (Exception $e) {
                print_r($e);
                exit(33);
            }
        } else {
            $this->userId     = intval($dataSet->{self::USER_CLM_ID});
            $this->email      = strval($dataSet->{self::USER_CLM_EMAIL});
            $this->firstName  = strval($dataSet->{self::USER_CLM_FNAME});
            $this->lastName   = strval($dataSet->{self::USER_CLM_LNAME});
            $this->gender     = strval($dataSet->{self::USER_CLM_GENDER});
            $this->isAdmin    = boolval($dataSet->{self::USER_CLM_ADMIN});
            $this->isPlayer   = boolval($dataSet->{self::USER_CLM_PLAYER});
            $this->isReporter = boolval($dataSet->{self::USER_CLM_REPORTER});
            $this->passHash   = strval($dataSet->{self::USER_CLM_PASS});
            $this->playerId   = strval($dataSet->{self::USER_CLM_PLAYERID});
            //$this->clubId     = intval($dataSet->self::USER_CLM_CLUBID]);
            $this->phone      = strval($dataSet->{self::USER_CLM_PHONE});
            $this->bday       = strval($dataSet->{self::USER_CLM_BDAY});
            $this->clubName   = "";
            $this->userImage  = strval($dataSet->{self::USER_CLM_IMAGE});
        /*} else {
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
            //$this->clubId     = 0;
            $this->clubName   = "";
            $this->phone      = "";
            $this->bday       = "";
            $this->userImage  = $this->getDefaultUserImage();
            */
        }
    }

    /**
     * Method to retrieve the full name consisting of first and last name
     * @return string the full name
     */
    public function getFullName():string
    {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    /**
     * get firstName
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * get lastname
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * set Lastname
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }


    public function getUserImage(): string
    {
        $file = self::USER_IMAGE_PATH ."/". $this->userImage;
        return strlen($this->userImage) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] .'/'. $file) ? $file : $this->getDefaultUserImage();
    }

    public function getUserThumbnailImage(): string
    {
        $file = self::USER_IMAGE_PATH ."/thumb_". $this->userImage;
        return strlen($this->userImage) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] .'/'. $file) ? $file : $this->getDefaultUserImage();
    }

    public function getDefaultUserImage(): string
    {
        $file = self::USER_IMAGE_PATH  .'/'. ($this->gender == 'Male' ? self::USER_IMAGE_MALE : self::USER_IMAGE_FEMALE);
        return $file;
    }

    public function getID(): int
    {
        return $this->userId;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    /**
     * set playerId
     *
     * @param string $playerId
     * @return void
     */
    public function setPlayerId(string $playerId): void
    {
        $this->playerId = $playerId;
    }

    /**
     * return userid of user
     *
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        if ($userId <= 0)
        {
            throw new Exception("id <= 0");
        }

        $this->userId = $userId;
    }

    /**
     * return true if user is admin
     *
     * @return boolean
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * set status
     *
     * @param boolean $status
     * @return void
     */
    public function setAdmin(bool $status): void
    {
        $this->isAdmin = $status;
    }

    /**
     * return if user is player
     *
     * @return boolean
     */
    public function isPlayer():bool
    {
        return $this->isPlayer;
    }

    /**
     * set status true is User is a player
     *
     * @param boolean $status
     * @return void
     */
    public function setPlayer(bool $status):void
    {
        $this->isPlayer = $status;
    }

    /**
     * return if user is reporter.
     *
     * @return boolean
     */
    public function isReporter():bool
    {
        return $this->isReporter;
    }

    /**
     * set status for Reporter
     *
     * @param boolean $status
     * @return void
     */
    public function setReporter(bool $status): void
    {
        $this->isReporter = $status;
    }

}

