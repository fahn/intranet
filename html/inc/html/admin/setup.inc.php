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
$path=dirname(dirname(__FILE__));
require($path .'/brdbHtmlPage.inc.php');
include_once BASE_DIR .'/inc/logic/prgSetup.inc.php';


class BrdbHtmlAdminSetup extends BrdbHtmlPage {
    private $PrgPatternElementSetup;

    private $_page;

    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }

        $this->prgPatternElementSetup = new PrgPatternElementSetup($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSetup);

        # load links
        $links = array(
            'startSync' => $this->PrgPatternElementSetup->linkTo(array('page' => $this->_page, 'action' => 'sync')),
        );

        $this->smarty->assign('links', $links);
    }

    public function htmlBody(): void
    {

        switch ($this->action) {
            case 'setup':
                $content = "INSTALL MODE";
                break;

            case 'update':
                $content = "UPDATE MODE";
                break;

            default:
                $content = $this->loadContent();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent(): string
    {
        return $this->smarty->fetch('setup/overview.tpl');
    }







}

?>
