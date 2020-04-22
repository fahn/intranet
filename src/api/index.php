#!/usr/bin/php

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
$path=dirname(dirname(__FILE__));
define(BASE_DIR, $path);
require_once BASE_DIR ."/inc/logic/prgPattern.inc.php";

class Api extends APrgPatternElement
{
    private string $content = "";

    public function __construct()
    {
        //global $argv;
        if (isset($argv) && count($argv) > 2 && !empty($argv[1]))
        {
            parse_str($argv[1], $_GET);
        }

        $action = $this->getGetVariable('action');
        switch ($action)
        {
            case 'tournament':
                $this->reminderTournament();
                break;
            default:
                # code...
                break;
        }
    }

    private function reminderTournament():void
    {
        $tournamentList = $this->brdb->APIGetTournamentFromToday();
        if (isset($tournamentList) && !empty($tournamentList))
        {
            foreach($tournamentList as $row)
            {
                if (isset($row) && isset($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL))
                {
                    $subject   = sprintf("Meldeschluss für %s", $row['name']);
                    // content
                    $toUser    = $row['email'];
                    $name      = $row['name'];
                    $link      = $this->linkTo(array(
                        'page'   => 'tournament.php',
                        'action' => 'details',
                        'id'     => $row['tournamentId'],
                    ));
                    // Mail Content
                    $content = sprintf("Hallo %s,<br>Für das Turnier/Rangliste \"%s\" ist heute Meldeschluss.<br><br>Alle weiteren Informationen gibt es <a href='%s'>hier</a>.", $row['reporterName'], $row['name'], $link);
                    // send mail
                    if ($this->sendMail($toUser, $name, $subject, $subject, $content))
                    {
                        $row['mail'] = "success";
                    }

                    if (isset($row) && is_array($row) && count($row) > 0)
                    {
                        $this->content .= implode(", ", $row);
                    }
                }
            }
        }
    }

    /**
     * Return Object
     *
     * @return string
     */
    function __toString(): string
    {
        return $this->content;
    }
}

/* OUTPUT */
$api = new Api();
sprintf("%s\n", $api);
exit(0);


