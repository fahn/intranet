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

    public function selectClubByClubNr($clubNr) {
        $cmd = $this->db->prepare("SELECT * FROM Club WHERE clubNr = ? LIMIT 1");
        $cmd->bind_param("i", $clubNr);

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

     public function insertClubByModel($club) {
         $cmd = $this->db->prepare("INSERT INTO Club (name, clubNr, association) VALUES (?, ?, ?)");
         $cmd->bind_param("sss", $club['clubName'], $club['clubNr'], $club['association']);

         return $this->executeStatement($cmd);
     }

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

     public function updateClubByClubNr($clubNr, $name, $association) {
         $cmd = $this->db->prepare("UPDATE Club set name = ?, association = ? WHERE clubNr = ?");
         $cmd->bind_param("sss", $name, $association, $clubNr);

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
        } else {
            $cmd = $this->db->prepare("SELECT * FROM Club ORDER by sort, name ASC ");
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
