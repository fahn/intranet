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
class Player {
    private $playerId;
    private $playerNr;
    private $firstName;
    private $lastName;
    private $gender;
    private $bday;
    private $clubId;
    private $clubNr;

    /**
     * Conmstructor that knows how to retrieve all fields from a given data set
     * @param array $dataSet a data set prefrably directly from an SQL statement
     */
    public function __construct($dataSet = null) {
        if($dataSet) {
            foreach($dataSet as $key => $value) {
                if ( property_exists($this,$key) ) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Method to retrieve the full name consisting of first and last name
     * @return string the full name
     */
    public function getFullName() {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    public function getPlayerId() {
        return $this->playerId;
    }

    public function getPlayerNr() {
        return $this->playerNr;
    }

    public function getSqlData() {
        return array(
            'playerNr'  => $this->playerNr,
            'clubId'    => $this->clubId,
            'firstName' => $this->firstName,
            'lastName'  => $this->lastName,
            'gender'    => $this->gender,
            //'bday'      => $this->bday,
        );
    }

    public function __toString() {
        return sprintf("%s %s: Gender %s; SpNr: %s\n", $this->firstName, $this->lastName, $this->gender, $this->playerNr);
    }
}
?>
