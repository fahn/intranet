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
require_once "htmlLoginPage.inc.php";

class BrdbHtmlPage extends AHtmlLoginPage
{


    public function __construct()
    {
        parent::__construct();
    }//end __construct()


    public function processPage()
    {
        // Call all prgs and process them all
        $this->prgPattern->processPRG();

        parent::processPage();
    }//end processPage()
}//end class
