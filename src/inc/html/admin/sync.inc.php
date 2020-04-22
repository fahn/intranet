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
require_once BASE_DIR."/inc/logic/prgSync.inc.php";
// load models
require_once BASE_DIR."/inc/model/club.inc.php";
require_once BASE_DIR."/inc/model/player.inc.php";
// load logic
require_once BASE_DIR."/inc/logic/prgClub.inc.php";
require_once BASE_DIR."/inc/logic/prgPlayer.inc.php";

class BrdbHtmlAdminSyncPage extends BrdbHtmlPage
{

    private $prgPatternElementSync;

    private $_page;


    public function __construct($page = null)
    {
        parent::__construct();

        $this->prgPatternElementSync = new PrgPatternElementSync($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSync);

        if ($page != null) {
             $this->_page = $page;
        }

        // load links
        $links = [
            'startSync' => $this->prgPatternElementSync->linkTo(['page' => $this->_page, 'action' => 'sync']),
        ];

        $this->smarty->assign('links', $links);
    }//end __construct()


    public function htmlBody(): void
    {
        switch ($this->action) {
            case 'sync':

            default:
                $content = $this->loadContent();
                break;
        }

        $this->smarty->assign(
            ['content' => $content]
        );

        $this->smarty->display('index.tpl');
    }//end htmlBody()


    private function loadContent(): string
    {
        $this->smarty->assign(
            []
        );
        return $this->smarty->fetch('sync/status.tpl');
    }//end loadContent()
}//end class
