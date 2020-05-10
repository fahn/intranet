<?php
/**
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
namespace Badtra\Intranet\DB;
trait FaqDB
{


    /**
     * Get all FAQ
     *
     * @return array
     */
    public function statementGetAllFaq(): array
    {
        $query     = "SELECT Faq.*, Cat.title as categoryTitle, Cat.categoryId FROM `Faq`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = Faq.categoryId";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }//end statementGetAllFaq()


    /**
     * Get FAQById
     *
     * @param  integer $faqId
     * @return array
     */
    public function statementGetFAQById(int $faqId): array
    {
        $query     = "SELECT * FROM `Faq` WHERE faqId = :faqId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('faqId', $faqId);
        $statement->execute();

        return $statement->fetchAll();
    }//end statementGetFAQById()


    /**
     * Get FAQ with CategoryId
     *
     * @param  integer $categoryId
     * @return array
     */
    public function statementGetFaqByCategoryId(int $categoryId): array
    {
        $query     = "SELECT * FROM `Faq` WHERE categoryId = :categoryId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('categoryId', $categoryId);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end statementGetFaqByCategoryId()


    /**
     * Get Categories and Count dependent categories
     *
     * @return array
     */
    public function statementGetCategoryAndCountItems():array
    {
        $query     = "SELECT *, (SELECT count(*) FROM Faq WHERE Faq.categoryId = Category.categoryId) AS items FROM `Category`";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end statementGetCategoryAndCountItems()


    /**
     * Insert FAQ by Id
     *
     * @param  Faq $faq
     * @return boolean
     */
    public function insertFaq(\Badtra\Intranet\Model\Faq $faq): bool
    {
        $query     = "INSERT INTO `Faq` (`title`, `categoryId`, `text`) VALUES (:title, :categoryId, :text)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $faq->getTitle());
        $statement->bindParam('categoryId', $faq->getCategoryId());
        $statement->bindParam('text', $faq->getText());
       
        return $statement->execute();
    }//end insertFaq()


    /**
     * Update FAQ by Id
     *
     * @param  Faq $faq
     * @return boolean
     */
    public function updateFaqById(\Badtra\Intranet\Model\Faq $faq): bool
    {
        $query     = "UPDATE `Faq` SET `title` = :title, `categoryId` = :categoryId, `text` = :text WHERE `faqId` = :faqId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $faq->getTitle());
        $statement->bindParam('categoryId', $faq->getCategoryId());
        $statement->bindParam('text', $faq->getText());
        $statement->bindParam('faqId', $faq->getFaqId());

        return $statement->execute();
    }//end updateFaqById()


    /**
     * Delete FAQ by Id
     *
     * @param  integer $faqId
     * @return boolean
     */
    public function deleteFaq(int $faqId): bool
    {
        $query     = "DELETE FROM `Faq` WHERE faqId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('faqId', $faqId);

        return $statement->execute();
    }//end deleteFaq()
}
