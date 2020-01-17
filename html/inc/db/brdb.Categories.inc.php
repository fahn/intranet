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
trait CategoryDB {

    public function statementGetAllCategories() {
        $query = "SELECT * FROM `Category` ORDER BY pid, title";
        $statement = $this->db->prepare($query);
       
        return $statement->execute();
    }

    public function adminStatementGetAllCategories() {
        $query = "SELECT Category.*, C1.title AS pidName FROM `Category`
                    LEFT JOIN `Category` AS C1 ON C1.categoryId = Category.pid
                    ORDER BY Category.pid, Category.title";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    public function insertCategory($title, $pid) {
        $query = "INSERT INTO `Category` (pid, title) VALUES (:pid, :title)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('pid', $pid);
        $statement->bindParam('title', $title);

        return $statement->execute();
    }
}
?>
