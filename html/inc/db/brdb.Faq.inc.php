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

trait FaqDB {

    public function statementGetAllFaq() {
        $query = "SELECT Faq.*, Cat.title as categoryTitle, Cat.categoryId FROM `Faq`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = Faq.categoryId";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    public function statementGetFAQById($faqId) {
        $query = "SELECT * FROM `Faq` WHERE faqId = :faqId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('faqId', $faqId);

        return $statement->execute();
    }
    
    public function statementGetFaqByCategoryId($categoryId) {
        $query = "SELECT * FROM `Faq` WHERE categoryId = :categoryId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('categoryId', $categoryId);
        
        return $statement->execute();
    }
    
    public function statementGetCategoryAndCountItems() {
        $query = "SELECT *, (SELECT count(*) FROM Faq WHERE Faq.categoryId = Category.categoryId) AS items FROM `Category`";
        $statement = $this->db->prepare($query);
        
        return $statement->execute();
    }

    public function insertFaq($title, $categoryId, $text) {
        $query = "INSERT INTO `Faq` (title, categoryId, text) VALUES (:title, :categoryId, :text)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $title);
        $statement->bindParam('categoryId', $categoryId);
        $statement->bindParam('text', $text);
        
        return $statement->execute();
    }

    public function updateFaqById($faqId, $title, $categoryId, $text) {
        $query = "UPDATE `Faq` set title = :title, categoryId = :categoryId, text = :text WHERE faqId = :faqId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $title);
        $statement->bindParam('categoryId', $categoryId);
        $statement->bindParam('text', $text);
        $statement->bindParam('faqId', $faqId);

        return $statement->execute();
    }

    public function deleteFaq($faqId) {
        $query = "DELETE FROM `Faq` WHERE faqId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('faqId', $faqId);

        return $statement->execute();
    }
}
?>