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

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementSync;

class SyncAdminPage extends BrdbHtmlPage
{

    private PrgPatternElementSync $prgPatternElementSync;

    private $_page;


    public function __construct($page = null)
    {
        parent::__construct();

        $this->prgPatternElementSync = new PrgPatternElementSync($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSync);

        $this->page = $page != null ?: $page;

        // load links
        $links = [
            'startSync' => $this->prgPatternElementSync->linkTo(['page' => $this->_page, 'action' => 'sync']),
        ];

        $this->smarty->assign('links', $links);
    }//end __construct()


    public function listView(): string
    {
        $this->smarty->assign(
            []
        );
        return $this->smartyFetchWrap('sync/status.tpl');
    }//end loadContent()
}//end class
