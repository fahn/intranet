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

class Player
{
    private int $playerId;
    private string $playerNr;
    private string $firstName;
    private string $lastName;
    private string $gender;
    private string $bday;

    // Club
    private int $clubId;
    private string $clubNr; // obsolete

    /**
     * Method to retrieve the full name consisting of first and last name
     * @return string the full name
     */
    public function getFullName():string
    {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    /**
     * get firstname of player
     *
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstName;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstName = $firstname;
    }

    /**
     * get Lastname of player
     *
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastName;
    }


    public function setLastname(string $lastname): void
    {
        $this->lastName = $lastname;
    }



    /**
     * get PlayerId from Player
     *
     * @return integer
     */
    public function getPlayerId():int
    {
        return $this->playerId;
    }

    public function setPlayerId(int $playerId): void
    {
        $this->playerId = $playerId;
    }

    /**
     * Get PlayerNr from Player
     *
     * @return string
     */
    public function getPlayerNr(): string
    {
        return $this->playerNr;
    }

    /**
     * Set PlayerNr
     *
     * @param string $playerNr
     * @return void
     */
    public function setPlayerNr(string $playerNr): void
    {
        $this->playerNr = $playerNr;
    }

    /**
     * Get ClubNr from PlayerId
     *
     * @return string
     */
    public function getClubNr(): string
    {
        return $this->clubNr;
    }

    /**
     * set clubNr
     *
     * @param string $clubNr
     * @return void
     */
    public function setClubNr(string $clubNr): void
    {
        $this->clubNr = $clubNr;
    }

    /**
     * get ClubId
     *
     * @return integer
     */
    public function getClubId(): int
    {
        return $this->clubId;
    }

    public function setClubId(int $clubId): void
    {
        $this->clubId = $clubId;
    }

    /**
     * Ger Bday from Player
     *
     * @return string
     */
    public function getBday():string
    {
        return $this->bday;
    }

    public function setBday(string $bday):void
    {
        $this->bday = $bday;
    }

    /**
     * return gender
     *
     * @return string Female|Male
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Set Gender
     *
     * @param string $gender
     * @return void
     */
    public function setGender(string $gender): void {
        $genderOptions = new Gender();
        if (!in_array($gender, $genderOptions->getGenderArray())) {
            throw new Exception("No valid Gender");
        }
        $this->gender = $gender;
        unset($gender);
    }

    public function __toString():string
    {
        return sprintf("%s %s: Gender %s; SpNr: %s\n", $this->firstName, $this->lastName, $this->gender, $this->playerNr);
    }
}

