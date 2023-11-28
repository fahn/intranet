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
namespace Badtra\Intranet\Html;


# libary



abstract class HtmlPageProcessor
{
    /**
     * Standard constructor which gets called
     * by some derived classes
     */
    public function __construct() {}


    /**
     * Call this method to process / render the complete HTML page
     */
    public function processPage() {}


    /**
     * Override this method to change the body content of the html.
     * In most derived classes this method is changed to display the specific
     * content of the html.
     */
    protected function htmlBody() {} 
}


