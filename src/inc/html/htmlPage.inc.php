<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
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


abstract class HtmlPageProcessor
{
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
    public function __construct() {}


    /**
     * Call this method to process / render the complete HTML page
     */
    public function processPage()
    {
        $this->htmlBody();
    }


    /**
     * Override this method to change the body content of the html.
     * In most derived classes this method is changed to display the specific
     * content of the html.
     */
    protected function htmlBody()
    {
        $this->smarty->display('index.tpl');
    }
}


