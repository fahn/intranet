<?php

// load tools
require_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

// load db
require_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';

// load smarty
require_once $_SERVER['BASE_DIR'] .'/smarty/libs/Smarty.class.php';

abstract class Widget {
    protected $smarty;

    protected $brdb;

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

        $this->smarty->setTemplateDir($_SERVER['BASE_DIR'] .'/templates');
        $this->smarty->setCompileDir($_SERVER['BASE_DIR'] .'/templates_c');
        $this->smarty->setConfigDir($_SERVER['BASE_DIR'] .'/smarty/configs');
    }

    abstract protected function showWidget($name);
}
?>
