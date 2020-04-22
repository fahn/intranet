<?php
/*******************************************************************************
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
require_once dirname(dirname(__FILE__)) ."/config.php";
require_once BASE_DIR ."/inc/logic/prgPattern.inc.php";

// load smarty
require_once BASE_DIR ."/smarty/libs/Smarty.class.php";

abstract class Widget extends APrgPatternElement {
   
    // smarty object
    protected $smarty;

    function __construct()
    {
        parent::__construct("Widget");

        // load own smarty element
        $this->smarty = new Smarty;
        $this->smarty->setTemplateDir(BASE_DIR .'/templates');
        $this->smarty->setCompileDir(BASE_DIR .'/templates_c');
        $this->smarty->setConfigDir(BASE_DIR .'/smarty/configs');      
    }

    public function processPost(): void {}
    public function processGet(): void {}

    abstract protected function showWidget(?string $name);
}

