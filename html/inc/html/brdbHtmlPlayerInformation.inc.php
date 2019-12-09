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
include_once('brdbHtmlPage.inc.php');

class BrdbHtmlPlayerInformation extends BrdbHtmlPage {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function processPage() {
        parent::processPage();
    }
    
    
    protected function htmlBody() {
        $content = $this->loadContent($this->tools->get('id'));
        
        $this->smarty->assign(array(
            'content'              => $content,
        ));
        
        $this->smarty->display('index.tpl');
    }
    
    
    private function loadContent($id) {
        if (!isset($id) || !is_numeric($id)) {
            return "";
        }
        
        $player = $this->brdb->selectPlayerById($id)->fetch_assoc();
        
        $this->smarty->assign(array(
            'player'       => $player,
        ));
        
        return $this->smarty->fetch('player/profil.tpl');
    }
    
    
    public function getGetVariable($variableName) {
        return Tools::escapeInput($_GET[$variableName]);
    }
    
}
?>
