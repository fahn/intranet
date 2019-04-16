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

trait News {
    
    public function statementGetAllNews() {
        $cmd = $this->db->prepare("SELECT News.*, Cat.title AS categoryTitle, CONCAT_WS(' ', User.firstName, User.lastName) as userName FROM `News`
                                   LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                                   LEFT JOIN `User` ON User.userId = News.userId");
        
        return  $this->executeStatement($cmd);
    }
    
    public function selectLatestNews($max=5) {
        $cmd = $this->db->prepare("SELECT News.*, Cat.title as categoryTitle, Cat.categoryId FROM `News`
                                   LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                                   LIMIT ?");
        $cmd->bind_param("i", $max);
        
        return  $this->executeStatement($cmd);
    }
    
    
    
    public function statementGetNewsById($id) {
        $cmd = $this->db->prepare("SELECT * FROM `News` WHERE newsId = ?");
        $cmd->bind_param("i", $id);
        
        return  $this->executeStatement($cmd);
    }
    
    public function insertNews($title, $categoryId, $text) {
        $cmd = $this->db->prepare("INSERT INTO `News` (title, categoryId, text) VALUES (?, ?, ?)");
        $cmd->bind_param("sis", $title, $categoryId, $text);
        
        return  $this->executeStatement($cmd);
    }
    
    public function updateNewsById($id, $title, $categoryId, $text) {
        $cmd = $this->db->prepare("UPDATE `News` set title = ?, categoryId = ?, text = ? WHERE newsId = ?");
        $cmd->bind_param("sisi", $title, $categoryId, $text, $id);
        
        
        return  $this->executeStatement($cmd);
    }
    
    public function deleteNews($id) {
        $cmd = $this->db->prepare("DELETE FROM `News` WHERE newsId = ?");
        $cmd->bind_param("i", $id);
        
        return  $this->executeStatement($cmd);
    }
    
}

?>