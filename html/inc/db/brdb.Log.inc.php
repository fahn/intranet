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

 trait LogDB {

     public function statementGetAllLogs() {
         $cmd = $this->db->prepare("SELECT * FROM Log ORDER BY tstamp DESC");

         return  $this->executeStatement($cmd);
     }

     public function insertLog($table, $details, $data, $action, $userId = Null) {
         $cmd = $this->db->prepare("INSERT `Log` (userId, action, fromTable, details, logdata) VALUES (?, ?, ?, ?, ?)");
         $cmd->bind_param("issss", $userId, $action, $table, $details, $data);

         return $this->executeStatement($cmd);
     }
}

?>
