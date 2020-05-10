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
namespace Badtra\Intranet\API;

use Badtra\Intranet\Logic\APrgPatternElement;

class ReminderTournment extends APrgPatternElement
{
    protected APrgPatternElement $aPrgPatternElement;

    public function __construct()
    {
        $this->aPrgPatternElement = new APrgPatternElement("API");
    }

    public function reminder() {
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
                    $link      = $this->aPrgPatternElement->linkTo(array(
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
}