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

trait Api {
    public function APIGetTournamentFromToday() {
        $cmd = $this->db->prepare("SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName, User.email FROM Tournament
                                   LEFT JOIN User ON User.userId = Tournament.reporterId
                                   WHERE Tournament.reporterId != '' AND Tournament.visible = 1 AND Tournament.deadline = CURDATE() ");
        
        return $this->executeStatement($cmd);
    }
    
    public function APIGetTournamentList() {
        $cmd = $this->db->prepare("SELECT Tournament.* FROM Tournament");
        
        return $this->executeStatement($cmd);
    }
    
    public function APIinsertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $tournamentType, $description, $latitude, $longitude) {
        $cmd = $this->db->prepare("INSERT INTO Tournament (name, place, startdate, enddate, deadline, link, classification, tournamentType, description, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $cmd->bind_param("sssssssssss", $name, $place, $startdate, $enddate, $deadline, $link, $classification, $tournamentType, $description, $latitude, $longitude);
        
        return $this->executeStatement($cmd);
    }
}
?>