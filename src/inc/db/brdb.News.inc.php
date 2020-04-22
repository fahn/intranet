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
trait NewsDB
{


    /**
     * Get All News
     *
     * @return array
     */
    public function statementGetAllNews(): array
    {
        $query     = "SELECT News.*, Cat.title AS categoryTitle, CONCAT_WS(' ', User.firstName, User.lastName) as userName FROM `News`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                    LEFT JOIN `User` ON User.userId = News.userId";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }//end statementGetAllNews()


    /**
     * Get N latest News
     *
     * @param  integer $max
     * @return array
     */
    public function selectLatestNews(int $max = 5): array
    {
        $query     = "SELECT News.*, Cat.title as categoryTitle, Cat.categoryId FROM `News`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                    LIMIT :max";
        $statement = $this->db->prepare($query);
        $statement->bindParam('max', $max);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectLatestNews()


    /**
     * Get News by Id
     *
     * @param  integer $newsId
     * @return array
     */
    public function statementGetNewsById(int $newsId): array
    {
        $query     = "SELECT * FROM `News` WHERE newsId = :newsId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('newsId', $newsId);
        $statement->execute();

        return $statement->fetchAll();
    }//end statementGetNewsById()


    /**
     * Inser News with title, categoryID and Text
     *
     * @param  News $news
     * @return boolean
     */
    public function insertNews(News $news): bool
    {
        $query     = "INSERT INTO `News` (`title`, `categoryId`, `text`) VALUES (:title, :categoryId, :newsText)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $news->getTitle());
        $statement->bindParam('newsText', $news->getText());
        $statement->bindParam('categoryId', $news->getCategoryId());

        return $statement->execute();
    }//end insertNews()


    /**
     * Update News by Id
     *
     * @param  News $news
     * @return boolean
     */
    public function updateNewsById(News $news): bool
    {
        $query     = "UPDATE `News` set `title` = :title, `categoryId` = :categoryId, `text` = :newsText WHERE `newsId` = :newsId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('newsId', $news->getNewsId());
        $statement->bindParam('title', $news->getTitle());
        $statement->bindParam('newsText', $news->getText());
        $statement->bindParam('categoryId', $news->getCategoryId());

        return $statement->execute();
    }//end updateNewsById()


    /**
     * Delete News by Id
     *
     * @param  integer $newsId
     * @return boolean
     */
    public function deleteNews(int $newsId): bool
    {
        $query     = "DELETE FROM `News` WHERE newsId = :newsId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('newsId', $newsId);

        return $statement->execute();
    }//end deleteNews()
}
