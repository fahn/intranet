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

include_once '../inc/logic/tools.inc.php';
require_once '../smarty/libs/Smarty.class.php';

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

	/**
	 * Standard constructor which gets called
	 * by some derived classes
	 */
	public function __construct() {
    $this->smarty = new Smarty;
    //$smarty->force_compile = true;
    #$this->smarty->debugging = true;
    #$smarty->caching = true;
    #$smarty->cache_lifetime = 120;
    $this->smarty->setTemplateDir('./../smarty/templates');
    $this->smarty->setCompileDir('./../smarty/templates_c');
    $this->smarty->setConfigDir('./../smarty/configs');
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
