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

trait ApiDB {
    public function APIGetTournamentFromToday() {
        $query = "SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName, User.email FROM Tournament
                                   LEFT JOIN User ON User.userId = Tournament.reporterId
                                   WHERE Tournament.reporterId != '' AND Tournament.visible = 1 AND Tournament.deadline = CURDATE() ";
       $statement = $this->db->prepare($query);
       $statement->execute();
        
       return $statement->fetchAll();
    }

    public function APIGetTournamentList() {
        $query = "SELECT Tournament.* FROM Tournament";
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll();
    }
}
?>
