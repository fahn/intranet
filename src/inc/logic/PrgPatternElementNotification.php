<?php
/*******************************************************************************
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
namespace Badtra\Intranet\Logic;
require_once "PrgPattern.php";

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementNotification extends APrgPatternElement {
    protected PrgPatternElementLogin $prgElementLogin;

    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("player");

        $this->prgElementLogin = $prgElementLogin;
    }

    public function processPost():void
    {

    }

    public function processGet():void
    {

    }

}