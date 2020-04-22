<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
trait CategoryDB
{


    public function statementGetAllCategories(): array
    {
        $query     = "SELECT * FROM `Category` ORDER BY pid, title";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end statementGetAllCategories()


    public function adminStatementGetAllCategories(): array
    {
        $query     = "SELECT Category.*, C1.title AS pidName FROM `Category`
                    LEFT JOIN `Category` AS C1 ON C1.categoryId = Category.pid
                    ORDER BY Category.pid, Category.title";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end adminStatementGetAllCategories()


    public function statementGetCategoryById(int $id): array
    {
        $query     = "SELECT * FROM `Category` WHERE id = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end statementGetCategoryById()


    public function insertCategory(Category $cat):bool
    {
        $query     = "INSERT INTO `Category` (`pid`, `title`) VALUES (:pid, :title)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('pid', $cat->getPid());
        $statement->bindParam('title', $cat->getTitle());

        return $statement->execute();
    }//end insertCategory()


    public function updateCategory(Category $cat):bool
    {
        $query     = "UPDATE `Category` SET `pid` = :pid `title` = :title WHERE `id` = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('pid', $cat->getPid());
        $statement->bindParam('title', $cat->getTitle());
        $statement->bindParam('id', $cat->getId());

        return $statement->execute();
    }//end updateCategory()


}
