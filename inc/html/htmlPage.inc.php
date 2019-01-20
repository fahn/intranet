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

require_once __PFAD__ .'/smarty/libs/Smarty.class.php';
require_once __PFAD__ .'/inc/logic/tools.inc.php';
#require_once '../inc/Config.php';
#require_once '../inc/Route.php';

/**
 * This class helps to build up HTML pages
 * using object oriented php classes. It
 * is based on HTML 5 and provides a standard
 * layout to show a html page. It also links to a
 * CSS style sheet and provides all relevant information
 * such as the header, meta data (UTF-8), title, etc.
 *
 * @author philipp
 *
 */
abstract class HtmlPageProcessor {

    protected $smarty;

    protected $content;

    protected $tools;

  /**
   * Standard constructor which gets called
   * by some derived classes
   */
  public function __construct() {
    $this->tools = new Tools;

    $this->smarty = new Smarty;
    // @TODO: set debug bar
    //$smarty->force_compile = true;
    #$this->smarty->debugging = true;
    #$smarty->caching = true;
    #$smarty->cache_lifetime = 120;
    $this->smarty->setTemplateDir(__PFAD__ .'smarty/templates');
    $this->smarty->setCompileDir(__PFAD__ .'smarty/templates_c');
    $this->smarty->setConfigDir(__PFAD__ .'smarty/configs');

    // remove notice
    $this->smarty->error_reporting = E_ALL & ~E_NOTICE;


    $ini = $this->tools->getIni();
    $this->smarty->assign(array(
        'pageTitle' => $ini['pageTitle'],
      ));

  }

  /**
   * Call this method to process / render the complete HTML page
   */
  public function processPage() {
    $this->htmlBody();
  }


  /**
   * Override this method to change the body content of the html.
   * In most derived classes this method is changed to display the specific
   * content of the html.
   */
  protected function htmlBody() {
    $this->smarty->display('index.tpl');
  }
}
?>
