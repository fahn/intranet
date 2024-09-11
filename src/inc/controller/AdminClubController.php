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

use Badtra\Intranet\Html\Admin\AdminClubPage;

class AdminClubController
{
    private $page;

    public function __construct()
    {
            
        $this->page = new AdminClubPage();
    }


    public function showAdminClubList()
    {
        echo $this->page->listView();
    }

    public function showAdminClubAddForm()
    {
        echo $this->page->addView();
    }

    public function showAdminClubUpdateForm($id)
    {
        echo $this->page->updateView($id);
    }

    public function showAdminClubDeleteForm($id)
    {
        echo $this->page->deleteView($id);
    }

}