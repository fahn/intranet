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

use Badtra\Intranet\Html\TournamentPage;

class TournamentController
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
            
        $this->page = new TournamentPage();
    }


    public function showTournamentList()
    {
        echo $this->page->listView();
    }

    public function showTournamentDetails($id)
    {
        echo $this->page->detailView($id);
    }

    public function showTournamentAddForm()
    {
        echo $this->page->addView();
    }

    public function showTournamentAddPlayerForm($id)
    {
        echo $this->page->addPlayerView($id);
    }
}