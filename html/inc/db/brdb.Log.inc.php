<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

 trait LogDB 
 {

    public function statementGetAllLogs(): array
    {
        $query = "SELECT * FROM Log ORDER BY tstamp DESC";
        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function insertLog($table, $details, $logdata, $action, $userId = Null): bool
    {
        $query     = "INSERT `Log` (userId, action, fromTable, details, logdata) VALUES (:userId, :action, :fromTable, :details, :logdata)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('action', $action);
        $statement->bindParam('fromTable', $table);
        $statement->bindParam('details', $details);
        $statement->bindParam('logdata', $logdata);
        
        return $statement->execute();
    }
}

?>
