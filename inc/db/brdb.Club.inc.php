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

trait Club {
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
    
    /**
     * insert club
     * @param unknown $name
     * @param unknown $number
     * @param unknown $association
     * @return unknown
     */
    public function insertClub($name, $number, $association) {
        $cmd = $this->db->prepare("INSERT INTO Club (name, clubNumber, association) VALUES (?, ?, ?)");
        $cmd->bind_param("sss", $name, $number, $association);
        
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
        $cmd = $this->db->prepare("UPDATE Club set name = ?, clubNumber = ?, association = ? WHERE clubId = ?");
        $cmd->bind_param("sssi", $name, $number, $association, $clubId);
        
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