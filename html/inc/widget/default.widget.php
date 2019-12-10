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
include_once(dirname(dirname(__FILE__)) .'/config.php');

// load tools
require(BASE_DIR .'/inc/logic/tools.inc.php');

// load db
require(BASE_DIR .'/inc/db/brdb.inc.php');

// load smarty
require(BASE_DIR .'/smarty/libs/Smarty.class.php');

abstract class Widget {
    // smarty object
    protected $smarty;

    // database
    protected $brdb;

    // tool set
    protected $tools;

    function __construct() {
        $this->tools = new Tools();

        $this->brdb = new BrankDB();

        $this->smarty = new Smarty;
        if ($this->tools->getIniValue('stage') == "development") {
            // @TODO: set debug bar
            $this->smarty->force_compile = true;
            $this->smarty->debugging = true;
            $this->smarty->caching = true;
            $this->smarty->cache_lifetime = 120;
        }

        $this->smarty->setTemplateDir(BASE_DIR .'/templates');
        $this->smarty->setCompileDir(BASE_DIR .'/templates_c');
        $this->smarty->setConfigDir(BASE_DIR .'/smarty/configs');
    }

    abstract protected function showWidget($name);
}
?>
