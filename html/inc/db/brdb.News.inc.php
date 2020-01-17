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

trait NewsDB {

    public function statementGetAllNews() {
        $query = "SELECT News.*, Cat.title AS categoryTitle, CONCAT_WS(' ', User.firstName, User.lastName) as userName FROM `News`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                    LEFT JOIN `User` ON User.userId = News.userId";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    public function selectLatestNews(int $max=5) {
        $query = "SELECT News.*, Cat.title as categoryTitle, Cat.categoryId FROM `News`
                    LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                    LIMIT :max";
        $statement = $this->db->prepare($query);
        $statement->bindParam('max', $max);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function statementGetNewsById(int $newsId) {
        $query = "SELECT * FROM `News` WHERE newsId = :newsId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('newsId', $newsId);

        return $statement->execute();
    }

    public function insertNews($title, $categoryId, $text) {
        $query = "INSERT INTO `News` (title, categoryId, text) VALUES (?, ?, ?)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('title', $title);
        $statement->bindParam('text', $text);
        $statement->bindParam('categoryId', $categoryId);

        return $statement->execute();
    }

    public function updateNewsById(int $newsId, $title, $categoryId, $text) {
        $query = "UPDATE `News` set title = :title, categoryId = :categoryId, text = :text WHERE newsId = :newsId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('newsId', $newsId);
        $statement->bindParam('title', $title);
        $statement->bindParam('text', $text);
        $statement->bindParam('categoryId', $categoryId);

        return $statement->execute();
    }

    public function deleteNews(int $newsId) {
        $query = "DELETE FROM `News` WHERE newsId = :newsId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('newsId', $newsId);

        return $statement->execute();
    }
}
?>