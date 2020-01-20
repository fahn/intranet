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

include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlFaq extends BrdbHtmlPage {
    private $vars;

    public function __construct() {
        parent::__construct();
        
        // secure
        $this->tools->secure_array($_GET);


    }

    public function processPage() {
        parent::processPage();
    }

    protected function htmlBody() {
        $content = $this->TMPL_showFAQ();

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function TMPL_showFAQ() {
        $this->smarty->assign(array(
          'error_message' => $this->tools->getErrorMessage();
        ));

        return $this->smarty->fetch('error.tpl');
    }
}
?>
