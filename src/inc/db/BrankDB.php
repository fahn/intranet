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
namespace Badtra\Intranet\DB;

use PDO;

use Badtra\Intranet\DB\UserDB;
use Badtra\Intranet\DB\ClubDB;
use Badtra\Intranet\DB\PlayerDB;
use Badtra\Intranet\DB\TournamentDB;
use Badtra\Intranet\DB\RankingDB;
use Badtra\Intranet\DB\LogDB;
use Badtra\Intranet\DB\NewsDB;
use Badtra\Intranet\DB\NotificationDB;
use Badtra\Intranet\DB\SettingsDB;
use Badtra\Intranet\DB\StaffDB;
use Badtra\Intranet\DB\FaqDB;
use Badtra\Intranet\DB\CategoryDB;
use Badtra\Intranet\DB\ApiDB;

class BrankDB
{

    private \PDO $db;
    private string $error;

    // load User
    use UserDB;

    // load Club
    use ClubDB;

    // load Player
    use PlayerDB;

    // load Tournament
    use TournamentDB;

    // load Ranking
    use RankingDB;

    // load Log
    use LogDB;

    // load News
    use NewsDB;

    // load Notification
    use NotificationDB;

    // load Settings
    use SettingsDB;

    // load Staff
    use StaffDB;

    // load Faq
    use FaqDB;

    // load Category
    use CategoryDB;

    // load Api
    use ApiDB;

    public function __construct()
    {
        // load connection
        try {
            $database = sprintf('mysql:host=%s;dbname=%s', $this->getEnv('MYSQL_HOST'), $this->getEnv('MYSQL_DATABASE'));
            
            $this->db = new \PDO($database, $this->getEnv('MYSQL_PASSWORD'), $this->getEnv('MYSQL_PASSWORD'));
        } catch (\Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            exit(91);
        }
    }

    /**
     * Destructor that closes the DB connection
     */
    public function __destruct()
    {
        unset($this->db);
    }

    private function getEnv(string $envvarname): ?string
    {
        return getenv($envvarname, true) ?: getenv($envvarname);
    }


    public function insert_id(): int
    {
        return $this->db->lastInsertId;
    }


    private function debug(string $statement): array
    {
        return $statement->debugDumpParams();
    }   
}
