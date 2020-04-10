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
include_once(dirname(dirname(__FILE__)) .'/config.php');
include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';

// load db
require_once(BASE_DIR .'/inc/db/brdb.inc.php');

// load smarty
require_once(BASE_DIR .'/smarty/libs/Smarty.class.php');

abstract class Widget extends APrgPatternElement {
    
    // smarty object
    protected $smarty;
/*
    // database
    protected $brdb;

    protected $prgPatternElementLogin;
    */

    function __construct() 
    {
        parent::__construct("Widget");
        //$this->brdb = new BrankDB();

        $this->smarty = new Smarty;
 
        $this->smarty->setTemplateDir(BASE_DIR .'/templates');
        $this->smarty->setCompileDir(BASE_DIR .'/templates_c');
        $this->smarty->setConfigDir(BASE_DIR .'/smarty/configs');       
    }

    public function processPost(): void {}
    public function processGet(): void {}

    abstract protected function showWidget($name);
}
?>
