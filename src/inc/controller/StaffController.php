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

use Badtra\Intranet\Html\Staff;

class StaffController
{
    private $page;
    /*
    private $user;
    private $smarty;

    public function __construct($pdo, $smarty)
    {
        $this->user = new User($pdo);
        $this->smarty = $smarty;
    } */

    public function __construct()
    {
            
        $this->page = new Staff();
    }


    public function showStaffList()
    {
        echo $this->page->listView();
    }

}