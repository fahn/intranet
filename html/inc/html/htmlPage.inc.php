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
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$stage = getenv('INTRANET_STAGE', true) ?: getenv('INTRANET_STAGE');
if ($stage == "development") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once BASE_DIR .'/smarty/libs/Smarty.class.php';
# libary
require_once BASE_DIR .'/vendor/autoload.php';

#use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\Routing\Annotation\Route;


abstract class HtmlPageProcessor { # extends AbstractController {
    // smarty
    protected Smarty $smarty;
   
    // content
    protected string $content;

    // stage
    protected string $stage;


    /**
     * Standard constructor which gets called
     * by some derived classes
     */
    public function __construct() {

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
