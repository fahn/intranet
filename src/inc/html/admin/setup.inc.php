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
require_once "brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgSetup.inc.php";


class BrdbHtmlAdminSetup extends BrdbHtmlPage
{
    private $PrgPatternElementSetup;

    private $_page;

    public function __construct($page = null) {
        parent::__construct();

        $this->_page = $page != null ?: $page;

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


