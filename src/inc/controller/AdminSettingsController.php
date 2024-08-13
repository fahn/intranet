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

use Badtra\Intranet\Html\Admin\AdminSettingsPage;

class AdminSettingsController
{
    private $page;

    public function __construct()
    {
            
        $this->page = new AdminSettingsPage();
    }


    public function showAdminSettingsList()
    {
        echo $this->page->listView();
    }

    public function showAdminSettingsAddForm()
    {
        echo $this->page->addView();
    }

    public function showAdminSettingsUpdateForm($id)
    {
        echo $this->page->updateView($id);
    }

    public function showAdminSettingsDeleteForm($id)
    {
        echo $this->page->deleteView($id);
    }

}