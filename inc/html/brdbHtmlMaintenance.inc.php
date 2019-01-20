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

 include_once __PFAD__ .'/inc/html/htmlLoginPage.inc.php';


 class BrdbHtmlMaintenance extends HtmlPageProcessor {
 	public function __construct() {
 		parent::__construct();
 	}

   public function processPage() {
 		parent::processPage();
 	}

  protected function htmlBody() {
    $ini = $this->tools->getIni();

    $this->smarty->assign(array(
      'headline' => $ini['maintenanceHeadline'],
      'text'     => $ini['maintenanceText'],
      'date'     => $ini['maintenanceDate'],
      'link'     => $this->tools->linkTo(array('page' => 'index.php')),
    ));

    $content = $this->smarty->fetch('maintenance.tpl');
    $this->smarty->assign('content', $content);

    $this->smarty->display('index.tpl');
  }

 }
