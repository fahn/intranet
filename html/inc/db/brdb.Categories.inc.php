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
trait CategoryDB 
{

    public function statementGetAllCategories(): array
    {
        $query = "SELECT * FROM `Category` ORDER BY pid, title";
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function adminStatementGetAllCategories(): array
    {
        $query = "SELECT Category.*, C1.title AS pidName FROM `Category`
                    LEFT JOIN `Category` AS C1 ON C1.categoryId = Category.pid
                    ORDER BY Category.pid, Category.title";
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function statementGetCategoryById(int $id): array
    {
        $query = "SELECT * FROM `Category` WHERE id = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function insertCategory(Category $cat):bool
    {
        $query = "INSERT INTO `Category` (`pid`, `title`) VALUES (:pid, :title)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('pid', $cat->getPid());
        $statement->bindParam('title', $cat->getTitle());

        return $statement->execute();
    }

    public function updateCategory(Category $cat):bool
    {
        $query = "UPDATE `Category` SET `pid` = :pid `title` = :title WHERE `id` = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('pid', $cat->getPid());
        $statement->bindParam('title', $cat->getTitle());
        $statement->bindParam('id', $cat->getId());

        return $statement->execute();
    }
}
?>