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

    /**
     * Conmstructor that knows how to retrieve all fields from a given data set
     * @param array $dataSet a data set prefrably directly from an SQL statement
     */
    public function __construct($dataSet = null) {
        if($dataSet) {
            foreach($dataSet as $key => $value) {
                $this->$key = $value;
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


    private function findPlayer($playerNr) {
        $res = $this->brdb->selectPlayerByPlayerNr($playerNr);
        $tmp = array();
        if ($this->brdb->hasError()) {
            return false;
        }
        
        return $res->num_rows == 1 ? true : false;
    }

    private function insertPlayer($item) {
        $res = $this->brdb->insertPlayer($item);
        if ($this->brdb->hasError()) {
            return false;
        }
        return true;
    }

    private function updatePlayer($item) {
        $res = $this->brdb->updatePlayer($item);
        if ($this->brdb->hasError()) {
            return false;
        }
        return true;
    }

}
?>
