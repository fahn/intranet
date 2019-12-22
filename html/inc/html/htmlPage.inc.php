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

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$stage = getenv('INTRANET_STAGE', true) ?: getenv('INTRANET_STAGE');
if ($stage == "development") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


require_once BASE_DIR .'/smarty/libs/Smarty.class.php';
require_once BASE_DIR .'/inc/logic/tools.inc.php';


abstract class HtmlPageProcessor {
    // smarty
    protected $smarty;
    // content
    protected $content;
    // tools
    protected $tools;

    // stage
    protected $stage;

    /**
     * Standard constructor which gets called
     * by some derived classes
     */
    public function __construct() {
        // load tools
        $this->tools = new Tools;

        // set stage
        $this->stage = getenv('INTRANET_STAGE', true) ?: getenv('INTRANET_STAGE');

        // load smarty
        $this->smarty = new Smarty;

        $this->smarty->setTemplateDir(BASE_DIR .'/templates');
        $this->smarty->setCompileDir(BASE_DIR  .'/templates_c');
        $this->smarty->setConfigDir(BASE_DIR  .'/smarty/configs');

        if ($this->stage == "development") {
            // @TODO: set debug bar
            #$this->smarty->force_compile  = true;
            #$this->smarty->debugging      = true;
            #$this->smarty->caching        = true;
            #$this->smarty->cache_lifetime = 120;
        }

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
