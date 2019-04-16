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

trait Faq {
    
    public function statementGetAllFaq() {
        $cmd = $this->db->prepare("SELECT Faq.*, Cat.title as categoryTitle, Cat.categoryId FROM `Faq`
                                   LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = Faq.categoryId");
        
        return  $this->executeStatement($cmd);
    }
    
    public function statementGetFAQById($id) {
        $cmd = $this->db->prepare("SELECT * FROM `Faq` WHERE faqId = ?");
        $cmd->bind_param("i", $id);
        
        return  $this->executeStatement($cmd);
    }
    
    public function insertFaq($title, $categoryId, $text) {
        $cmd = $this->db->prepare("INSERT INTO `Faq` (title, categoryId, text) VALUES (?, ?, ?)");
        $cmd->bind_param("sis", $title, $categoryId, $text);
        
        return  $this->executeStatement($cmd);
    }
    
    public function updateFaqById($id, $title, $categoryId, $text) {
        $cmd = $this->db->prepare("UPDATE `Faq` set title = ?, categoryId = ?, text = ? WHERE faqId = ?");
        $cmd->bind_param("sisi", $title, $categoryId, $text, $id);
        
        
        return  $this->executeStatement($cmd);
    }
    
    public function deleteFaq($id) {
        $cmd = $this->db->prepare("DELETE FROM `Faq` WHERE faqId = ?");
        $cmd->bind_param("i", $id);
        
        return  $this->executeStatement($cmd);
    }

}

?>