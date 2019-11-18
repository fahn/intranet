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
        $cmd = $this->db->prepare("SELECT * FROM Club WHERE clubId = ? LIMIT 1");
        $cmd->bind_param("i", $clubId);
        return $this->executeStatement($cmd);
    }

    public function selectClubByClubNr(string $clubNr) {
        $cmd = $this->db->prepare("SELECT * FROM Club WHERE clubNr = ?");
        $cmd->bind_param("s", $clubNr);

        return $this->executeStatement($cmd);
    }



    /**
     * insert club
     * @param unknown $name
     * @param unknown $number
     * @param unknown $association
     * @return unknown
     */
     public function insertClub($name, $number, $association) {
         $cmd = $this->db->prepare("INSERT INTO Club (name, clubNr, association) VALUES (?, ?, ?)");
         $cmd->bind_param("sss", $name, $number, $association);

         return $this->executeStatement($cmd);
     }
     /*
     public function insertClubByModel($club) {

         $statement = $this->db->prepare('INSERT INTO Club (name, clubNr, association) VALUES (:clubName, :clubNr, :association)');
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
     public function updateClubById($clubId, $name, $number, $association) {
         $cmd = $this->db->prepare("UPDATE Club set name = ?, clubNr = ?, association = ? WHERE clubId = ?");
         $cmd->bind_param("sssi", $name, $number, $association, $clubId);

         return $this->executeStatement($cmd);
     }

     public function updateClubByClubNr($clubNr, $clubName, $association) {
         $cmd = $this->db->prepare("UPDATE Club set name = ?, association = ? WHERE clubNr = ?");
         $cmd->bind_param("sss", $clubName, $association, $clubNr);

         return $this->executeStatement($cmd);
     }

    /**
     * Select all Clubs
     * @param number $min
     * @param number $max
     * @return unknown
     */
    public function selectAllClubs($min = 0, $max = 0) {
        if($min != $max) {
            $cmd = $this->db->prepare("SELECT * FROM Club ORDER by sort, name LIMIT ?,?");
            $cmd->bind_param("ii", $min, $max);
            #echo $min;
        } else {
            $cmd = $this->db->prepare("SELECT * FROM Club ORDER by sort, name ASC");
        }

        return $this->executeStatement($cmd);
    }

    /**
     * delete club
     * @param unknown $clubId
     * @return unknown
     */
    public function deleteClubById($clubId) {
        $cmd = $this->db->prepare("DELETE Club  WHERE clubId = ?");
        $cmd->bind_param("i", $clubId);

        return $this->executeStatement($cmd);
    }

}
?>
