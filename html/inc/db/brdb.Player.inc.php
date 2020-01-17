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
        $query = "SELECT Player.*, CONCAT_WS(' ', Player.firstName, Player.lastName) as fullName, Club.name AS clubName FROM Player
                    LEFT JOIN `Club` ON Club.clubId = Player.clubId
                    ORDER BY Player.lastName ASC";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }


    public function selectPlayerById(int $playerId) {
        $query = "SELECT Player.*, Club.name AS clubName FROM Player
                    LEFT JOIN `Club` ON Club.clubId = Player.clubId
                    WHERE playerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }

    public function selectPlayerByPlayerNr(int $playerNr) {
        $query = "SELECT * FROM Player WHERE playerNr = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }



    public function insertPlayer($data) {
        $query     = "INSERT INTO Player (playerNr, clubId, firstName, lastName, bday, gender) 
                        VALUES (:playerNr, :clubId, :firstName, :lastName, :gender)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerNr', $data['playerNr']);
        $statement->bindParam('clubId', $data['clubId']);
        $statement->bindParam('firstName', $data['firstName']);
        $statement->bindParam('lastName', $data['lastName']);
        $statement->bindParam('gender', $data['gender']);
        $statement->bindParam('bday', $data['bday']);

        return $statement->execute();
    }

    public function updatePlayer($data) {
        try {
            extract($data);
            $query = "UPDATE Player set firstName = :firstName, lastName = :lastName, gender = :gender, bday = :bday, clubId = :clubId
                        WHERE playerNr = :playerNr";
            $statement = $this->db->prepare($query);
            $statement->bindParam('playerNr', $data['playerNr']);
            $statement->bindParam('clubId', $data['clubId']);
            $statement->bindParam('firstName', $data['firstName']);
            $statement->bindParam('lastName', $data['lastName']);
            $statement->bindParam('gender', $data['gender']);
            
            return $statement->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPlayerByTerm($term) {
        $term = $this->db->real_escape_string($term);
        $term = "%".$term."%";
        $query = "SELECT Player.*, CONCAT_WS(', ', Player.lastName, Player.firstName) AS playerName, Club.name AS clubName FROM Player
                    LEFT JOIN Club ON Club.clubId = Player.clubId
                    WHERE CONCAT_WS(' ', Player.firstName, Player.lastName) LIKE :term
                    ORDER BY Player.lastName";
        $statement = $this->db->prepare($query);
        $statement->bindParam('term', $data['term']);

        return $statement->execute();
    }
}

?>
