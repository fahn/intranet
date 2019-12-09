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
            'headline' => $this->tools->getIniValue('maintenanceHeadline'), #$ini["Maintenance"]['maintenanceHeadline'],
            'text'     => $this->tools->getIniValue('maintenanceText'), #$ini["Maintenance"]['maintenanceText'],
            'date'     => $this->tools->getIniValue('maintenanceDate'),  #$ini["Maintenance"]['maintenanceDate'],
            'link'     => $this->tools->linkTo(array('page' => 'index.php')),
        ));

        $content = $this->smarty->fetch('maintenance.tpl');
        $this->smarty->assign('content', $content);

        $this->smarty->display('index.tpl');
    }

}
?>
