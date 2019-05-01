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

trait PlayerDB {
    /**
     * get All Player
     * @return unknown
     */
    public function selectGetAllPlayer() {
        $sql = "SELECT Player.*, CONCAT_WS(' ', Player.firstName, Player.lastName) as fullName, Club.name AS clubName FROM Player
            LEFT JOIN `Club` ON Club.clubId = Player.clubId
            ORDER BY Player.lastName ASC";
        $cmd = $this->db->prepare($sql);

        return $this->executeStatement($cmd);
    }


    public function selectPlayerById($playerId) {
        $sql = "SELECT Player.*, Club.name AS clubName FROM Player 
                LEFT JOIN `Club` ON Club.clubId = Player.clubId
                WHERE playerId = ? ";
        $cmd = $this->db->prepare($sql);
        $cmd->bind_param("i", $playerId);

        return $this->executeStatement($cmd);
    }
    
    public function selectPlayerByPlayerNr($playerNr) {
        $sql = "SELECT Player.*, Club.name AS clubName FROM Player 
                LEFT JOIN `Club` ON Club.clubId = Player.clubId
                WHERE playerNr = ? ";
        $cmd = $this->db->prepare($sql);
        $cmd->bind_param("s", $playerNr);

        return $this->executeStatement($cmd);
    }
    
    

    public function insertPlayer($data) {
        try {
            extract($data);
            $sql = "INSERT INTO PLAYER (playerNr, clubId, firstName, lastName, gender, bday) VALUES (?,?,?, ?, ?, ?)";
            $cmd = $this->db->prepare($sql);
            $cmd->bind_param("sissss", $playerNr, $clubId, $firstName, $lastName, $gender, $bday);

            return $this->executeStatement($cmd);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function updatePlayer($data) {
        try {
            extract($data);
            $sql = "UPDATE PLAYER set
                        firstName = ?,
                        lastName = ?,
                        gender = ?,
                        bday = ?,
                        clubId = ?
                    WHERE playerNr = ?";
            $cmd = $this->db->prepare($sql);
            $cmd->bind_param("ssssis", $firstName, $lastName, $gender, $bday, $clubId, $playerNr);

            return $this->executeStatement($cmd);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getPlayerByTerm($term) {
        $term = $this->db->real_escape_string($term);
        $term = "%".$term."%";
        $cmd  = $this->db->prepare("SELECT Player.*, CONCAT_WS(',', Player.lastName, Player.firstName) AS playerName, Club.name AS clubName FROM Player
                                   LEFT JOIN Club ON Club.clubId = Player.clubId
                                   WHERE CONCAT_WS(' ', Player.firstName, Player.lastName) LIKE ?
                                   ORDER BY Player.lastName");

        $cmd->bind_param("s", $term);
        #print_r($cmd->__toString());

        return $this->executeStatement($cmd);
    }
}

?>
