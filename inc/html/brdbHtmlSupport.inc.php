<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.	*
 *														*
 * Copyright 2017										*
 *														*
 * All Rights Reserved									*
 *														*
 * Copying, distribution, usage in any form is not 		*
 * allowed without  written permit.						*
 *														*
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)	*
 *														*
 ********************************************************/

include_once __PFAD__ .'/inc/html/brdbHtmlPage.inc.php';
include_once __PFAD__ .'/inc/logic/prgSupport.inc.php';
include_once __PFAD__ .'/inc/logic/tools.inc.php';


class brdbHtmlSupport extends BrdbHtmlPage {
  private $prgElementSupport;

  private $tools;

  public function __construct() {
    parent::__construct();

    $this->prgElementSupport = new PrgPatternElementSupport($this->brdb, $this->prgPatternElementLogin);
    $this->prgPattern->registerPrg($this->prgElementSupport);

    $this->tools = new Tools();
  }

  public function processPage() {
    parent::processPage();
  }


  protected function htmlBody() {
    $content = $this->loadContent();

    $this->smarty->assign(array(
      'content' => $content,
    ));

    $this->smarty->display('index.tpl');
  }


  private function loadContent() {
    $action = $this->tools->get('action');
    switch ($action) {
      case 'new_player':
        $text = sprintf('Hallo,&#13;ich möchte hiermit folgenden SpielerIn melden.&#13;&#13;Name:???&#13;Spielernummer:???&#13;Verein:???&#13;Vereinsnummer:???&#13;');
        break;

      default:
        $text = "";
        break;
    }

    $this->smarty->assign(array(
      'text'    => $text,
    ));

    return $this->smarty->fetch('support.tpl');
  }


}
?>
