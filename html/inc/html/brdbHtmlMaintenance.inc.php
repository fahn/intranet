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
include_once('htmlLoginPage.inc.php');

class BrdbHtmlMaintenance extends HtmlPageProcessor {
    public function __construct() {
        parent::__construct();
    }

    public function processPage() {
        parent::processPage();
    }

    protected function htmlBody() {
        $this->smarty->assign(array(
            'headline' => $this->settings->getSettingString('MAINTENANCE_TITLE'),
            'text'     => $this->settings->getSettingString('MAINTENANCE_TEXT'),
            'date'     => $this->settings->getSettingString('MAINTENANCE_DATE'),
            'link'     => $this->prgPatternElementLogin->linkTo(array('page' => 'index.php')),
        ));

        $content = $this->smarty->fetch('maintenance.tpl');
        $this->smarty->assign('content', $content);

        $this->smarty->display('index.tpl');
    }

}
?>
