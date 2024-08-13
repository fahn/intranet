<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2024
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
 * @copyright 2017-2024 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 **/
namespace Badtra\Intranet\Controller;

use Badtra\Intranet\Html\Admin\AdminTournamentPage;

class AdminTournamentController
{
    private $page;

    public function __construct()
    {
            
        $this->page = new AdminTournamentPage();
    }


    public function showAdminTournamentList()
    {
        echo $this->page->listView();
    }

    public function showAdminTournamentAddForm()
    {
        echo $this->page->addView();
    }

    public function showAdminTournamentUpdateForm($id)
    {
        echo $this->page->updateView($id);
    }

    public function showAdminTournamentDeleteForm($id)
    {
        echo $this->page->deleteView($id);
    }

    public function showAdminTournamentBackupForm($id)
    {
        echo $this->page->backupView($id);
    }

    public function showAdminTournamentlockForm($id)
    {
        echo $this->page->lockView($id);
    }

    public function showAdminTournamentUnlockForm($id)
    {
        echo $this->page->unlockView($id);
    }

    public function showAdminTournamentExportForm($id)
    {
        echo $this->page->exportView($id);
    }

}