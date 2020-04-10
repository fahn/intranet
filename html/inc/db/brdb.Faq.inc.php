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

trait FaqDB 
{

    /**
     * Get all FAQ
     *
     * @return array
     */
    public function statementGetAllFaq(): array
    {
        $query = "SELECT Faq.*, Cat.title as categoryTitle, Cat.categoryId FROM `Faq`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = Faq.categoryId";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get FAQById
     *
     * @param integer $faqId
     * @return array
     */
    public function statementGetFAQById(int $faqId): array
    {
        $query = "SELECT * FROM `Faq` WHERE faqId = :faqId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('faqId', $faqId);
        $statement->execute();

        return $statement->fetchAll();
    }
    

    /**
     * Get FAQ with CategoryId
     *
     * @param integer $categoryId
     * @return array
     */
    public function statementGetFaqByCategoryId(int $categoryId): array
    {
        $query = "SELECT * FROM `Faq` WHERE categoryId = :categoryId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('categoryId', $categoryId);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    /**
     * Get Categories and Count dependent categories
     *
     * @return array
     */
    public function statementGetCategoryAndCountItems():array
    {
        $query = "SELECT *, (SELECT count(*) FROM Faq WHERE Faq.categoryId = Category.categoryId) AS items FROM `Category`";
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    /**
     * Insert FAQ by Id
     *
     * @param Faq $faq
     * @return boolean
     */
    public function insertFaq(Faq $faq): bool
    {
        $query = "INSERT INTO `Faq` (`title`, `categoryId`, `text`) VALUES (:title, :categoryId, :text)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $faq->getTitle());
        $statement->bindParam('categoryId', $faq->getCategoryId());
        $statement->bindParam('text', $faq->getText());
        
        return $statement->execute();
    }

    /**
     * Update FAQ by Id
     *
     * @param Faq $faq
     * @return boolean
     */
    public function updateFaqById(Faq $faq): bool
    {
        $query = "UPDATE `Faq` SET `title` = :title, `categoryId` = :categoryId, `text` = :text WHERE `faqId` = :faqId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $faq->getTitle());
        $statement->bindParam('categoryId', $faq->getCategoryId());
        $statement->bindParam('text', $faq->getText());
        $statement->bindParam('faqId', $faq->getFaqId());

        return $statement->execute();
    }

    /**
     * Delete FAQ by Id
     *
     * @param integer $faqId
     * @return boolean
     */
    public function deleteFaq(int $faqId): bool
    {
        $query = "DELETE FROM `Faq` WHERE faqId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('faqId', $faqId);

        return $statement->execute();
    }
}
?>