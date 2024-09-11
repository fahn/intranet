<?php
/*******************************************************************************
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
 ******************************************************************************/
namespace Badtra\Intranet\Widget;

use Badtra\Intranet\DB\BrankDB;
use \Smarty;

abstract class DefaultWidget
{
   
    // // smarty object
    protected $smarty;

    // // brdb object
    protected $brdb;

    function __construct(Smarty $smarty, BrankDB $brdb)
    {
        $this->smarty = $smarty;
        $this->brdb = $brdb;
    }

    public function processPost(): void {}
    public function processGet(): void {}

    //abstract protected function showWidget(?string $name);
}
