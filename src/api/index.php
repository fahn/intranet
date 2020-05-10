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
use Badtra\Intranet\API\ReminderTournment;

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
                $reminder = new ReminderTournment();
                return $reminder->reminder();
                break;
            default:
                # code...
                break;
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


