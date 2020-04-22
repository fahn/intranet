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
require_once "/var/www/html/inc/model/tournament.mdl.php";

#try {
    $tournament = new Tournament();
    $tournament->setTournamentId(1);
    $tournament->setName("Test-Turnier");
    $tournament->setPlace("Hannover");
    $tournament->setLink("http://www.google.de");

    echo $tournament;
/*
} catch (Exception $e) {
    echo $e;
}
*/