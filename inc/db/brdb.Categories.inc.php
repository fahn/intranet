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

trait Categories {
    
    public function statementGetAllCategories() {
        $cmd = $this->db->prepare("SELECT * FROM `Category` ORDER BY pid, title");
        return  $this->executeStatement($cmd);
    }

    public function adminStatementGetAllCategories() {
        $cmd = $this->db->prepare("SELECT Category.*, C1.title AS pidName FROM `Category`
            LEFT JOIN `Category` AS C1 ON C1.categoryId = Category.pid
            ORDER BY Category.pid, Category.title");
        return  $this->executeStatement($cmd);
    }

    public function insertCategory($title, $pid) {
        $cmd = $this->db->prepare("INSERT INTO `Category` (pid, title) VALUES (?, ?)");
        $cmd->bind_param("is", $pid, $title);

        return  $this->executeStatement($cmd);
    }
}
?>