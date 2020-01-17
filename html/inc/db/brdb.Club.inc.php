<?php
declare(strict_types=1);

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

trait ClubDB {
    /**
     * get club by clubId
     * @param unknown $id
     * @return unknown
     */
    public function selectGetClubById($clubId) {
        $query = "SELECT * FROM Club WHERE clubId = :clubId LIMIT 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);
        
        return $statement->execute();
    }

    public function selectClubByClubNr(string $clubNr) {
        $query = "SELECT * FROM Club WHERE clubNr = :clubNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubNr', $clubNr);

        return $statement->execute();
    }

    /**
     * insert club
     * @param unknown $name
     * @param unknown $number
     * @param unknown $association
     * @return unknown
     */
    public function insertClub($clubName, $clubNr, $association) {
        $query = "INSERT INTO Club (name, clubNr, association) VALUES (:clubName, :clubNr, :association)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubName', $clubName);
        $statement->bindParam('association', $association);
        $statement->bindParam('clubNr', $clubNr);

        return $statement->execute();
    }
     /*
     public function insertClubByModel($club) {

         $statement = 'INSERT INTO Club (name, clubNr, association) VALUES (:clubName, :clubNr, :association)');
         $statement->bindParam('clubName', $club['clubName']);
         $statement->bindParam('clubNr', $club['clubNr']);
         $statement->bindParam('association', $club['association']);

         return $this->executeStatement($statement);
     }
     */


    /**
     * update club by Id
     * @param unknown $clubId
     * @param unknown $name
     * @param unknown $number
     * @param unknown $association
     * @return unknown
     */
    public function updateClubById($clubId, $clubName, $clubNr, $association) {
        $query = "UPDATE Club set name = :name, clubNr = :clubNr, association = :association WHERE clubId = :clubId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);
        $statement->bindParam('clubName', $clubName);
        $statement->bindParam('association', $association);
        $statement->bindParam('clubNr', $clubNr);

        return $statement->execute();
    }

    public function updateClubByClubNr($clubNr, $clubName, $association) {
        $query = "UPDATE Club set name = :clubName, association = :association WHERE clubNr = :clubNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubName', $clubName);
        $statement->bindParam('association', $association);
        $statement->bindParam('clubNr', $clubNr);

        return $statement->execute();
    }

    /**
     * Select all Clubs
     * @param number $min
     * @param number $max
     * @return unknown
     */
    public function selectAllClubs($min = 0, $max = 0) {
        $limit = $min != $max ? "LIMIT :min, :max" : "ASC";
        $query = "SELECT * FROM Club ORDER by sort, name $limit";
        $statement = $this->db->prepare($query);
        $statement->bindParam('min', $min);
        $statement->bindParam('max', $max);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * delete club
     * @param unknown $clubId
     * @return unknown
     */
    public function deleteClubById($clubId) {
        $query = "DELETE Club  WHERE clubId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);

        return $statement->execute();
    }
}
?>
