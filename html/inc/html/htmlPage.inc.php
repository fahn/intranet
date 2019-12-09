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

if (isset(getenv('stage')) && getenv('stage') == "development") {
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
}

$path=dirname(dirname(__FILE__));
include($path .'/config.php');

require_once BASE_DIR .'/smarty/libs/Smarty.class.php';
require_once BASE_DIR .'/inc/logic/tools.inc.php';

abstract class HtmlPageProcessor {
    // smarty
    protected $smarty;
    // content
    protected $content;
    // tools
    protected $tools;

    /**
     * Standard constructor which gets called
     * by some derived classes
     */
    public function __construct() {
        // load tools
        $this->tools = new Tools;

        // load smarty
        $this->smarty = new Smarty;

        if (getenv('stage') == "development") {
            // @TODO: set debug bar
            $this->smarty->force_compile  = true;
            $this->smarty->debugging      = true;
            $this->smarty->caching        = true;
            $this->smarty->cache_lifetime = 120;
        }

        $this->smarty->setTemplateDir($_SERVER['BASE_DIR'] .'/templates');
        $this->smarty->setCompileDir($_SERVER['BASE_DIR']  .'/templates_c');
        $this->smarty->setConfigDir($_SERVER['BASE_DIR']  .'/smarty/configs');

        // remove notice
        $this->smarty->error_reporting = E_ALL & ~E_NOTICE;

        $this->smarty->assign(array(
            'pageTitle' => $this->tools->getIniValue('pageTitle'),
            'logoTitle' => $this->tools->getIniValue('logoTitle'),
            'baseUrl'   => $this->tools->getIniValue('baseUrl'),
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
