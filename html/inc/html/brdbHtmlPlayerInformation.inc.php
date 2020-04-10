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
include_once('brdbHtmlPage.inc.php');

class BrdbHtmlPlayerInformation extends BrdbHtmlPage 
{
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    protected function htmlBody(): void
    {
        $content = $this->loadContent();
        
        $this->smarty->assign(array(
            'content'              => $content,
        ));
        
        $this->smarty->display('index.tpl');
    }
    
    
    private function loadContent(): string
    {
        $this->smarty->assign(array(
            'player'       => $this->brdb->selectPlayerById($this->id),
        ));
        
        return $this->smarty->fetch('player/profil.tpl');
    }
}
?>
