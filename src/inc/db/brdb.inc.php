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
require_once dirname(dirname(__FILE__)) ."/config.php";
require_once BASE_DIR ."/inc/exception/badtra.exception.php";

require_once "brdb.Api.inc.php";
require_once "brdb.Categories.inc.php";
require_once "brdb.Club.inc.php";
require_once "brdb.Faq.inc.php";
require_once "brdb.Log.inc.php";
require_once "brdb.News.inc.php";
require_once "brdb.Notification.inc.php";
require_once "brdb.Player.inc.php";
require_once "brdb.Ranking.inc.php";
require_once "brdb.Settings.inc.php";
require_once "brdb.Staff.inc.php";
require_once "brdb.Tournament.inc.php";
require_once "brdb.User.inc.php";

require_once "brdb.Cup.inc.php";


class BrankDB
{

    private $db;
    private $error;

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

    // load Api
    use CupDB;

    public function __construct()
    {
        // load connection
        $this->db = $this->connection();
    }

    private function connection()
    {
        if ($this->db == NULL)
        {
            try
            {
                $database = sprintf('mysql:host=%s;dbname=%s', $this->getEnv('MYSQL_HOST'), $this->getEnv('MYSQL_DATABASE'));
               
                return new \PDO($database, $this->getEnv('MYSQL_PASSWORD'), $this->getEnv('MYSQL_PASSWORD'));
            }
            catch (Exception $e)
            {
                echo "<pre>";
                print_r($e);
                echo "</pre>";
                exit(91);
            }
        }
    }

    private function getEnv(string $envvarname): ?string
    {
        return getenv($envvarname, true) ?: getenv($envvarname);
    }

    /**
     * Destructor that closes the DB connection
     */
    public function __destruct()
    {
        $this->db = null;
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
