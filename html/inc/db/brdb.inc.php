<?php 
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/
#declare(strict_types=1);

// load BASE_DIR
require_once(dirname(dirname(__FILE__)) .'/config.php');
require_once(BASE_DIR .'/inc/exception/badtra.exception.php');

include_once 'brdb.Api.inc.php';
include_once 'brdb.Categories.inc.php';
include_once 'brdb.Club.inc.php';
include_once 'brdb.Faq.inc.php';
include_once 'brdb.Log.inc.php';
include_once 'brdb.News.inc.php';
include_once 'brdb.Notification.inc.php';
include_once 'brdb.Player.inc.php';
include_once 'brdb.Ranking.inc.php';
include_once 'brdb.Settings.inc.php';
include_once 'brdb.Staff.inc.php';
include_once 'brdb.Tournament.inc.php';
include_once 'brdb.User.inc.php';


class BrankDB 
{

    private $db;

    private $error;

    private $hasError;

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
        /*
        if (!defined('PDO::ATTR_DRIVER_NAME')) 
        {
            throw new Exception\Badtra("123");
        } */
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
                $this->setError($e);
                print_r($e);
                return NULL;
            }
        }
    }

    private function getEnv(string $envvarname): string
    {
        return getenv($envvarname, true) ?: getenv($envvarname);
    }

    /**
     * Destructor that closes the DB connection
     */
    public function __destruct() 
    {
        #$this->db->close();
    }

    /**
     * Call this method to check if an error occurred
     *
     * @return boolean true in case there is an error pending
     */
    public function hasError() 
    {
        return $this->hasError;
    }

    /**
     * Call this method to get the error.
     * this method will also reset the current error state.
     * the message will be kept.
     *
     * @return unknown
     */
    public function getError(): string
    {
        $this->hasError = false;
        return $this->error;
    }

    /**
     * Internal method to set an error
     *
     * @param string $error
     *            the error to be set
     */
    private function setError(string $error): void
    {
        $this->hasError = true;
        $this->error = $error;
    }

    public function insert_id(): int
    {
        return $this->db->insert_id;
    }

    /**
     * Internal method that executes a prepared statement.
     * Automatically sets the error state in case things go wrong.
     *
     * @param mysqli_stmt $statement
     *            the prepared and bound statement to be executed
     * @return mysqli_result the result of the executed statement
     */
    public function executeStatement(string $statement): bool
    {
        if (! $statement->execute()) {
            $this->setError($statement->error);
        }
        return $statement->get_result();
    }

    public function countRows(string $statement): int
    {
        return $statement->rowCount();
    }
    /*
    private function doPrepareBind($param) 
    {
        try {
            foreach($param as $par){
                switch($par[2]):
                    case 'int':
                        $query->bindParam($par[0], $par[0], PDO::PARAM_INT);
                    break;

                    case 'str':
                        $query->bindParam($par[0], $par[0], PDO::PARAM_STR);
                    break;

                    case 'blob':
                        $query->bindParam($par[0], $par[0], PDO::PARAM_LOB);
                    break;

                    default:
                        $query->bindParam($par[0], $par[0], PDO::PARAM_STR);
                    break;

                endswitch;
            }

            return $query;
        } catch(PDOException $e) {
            throw new myPdoException($e);
        }
    } */

    private function executeFetchAll(string $statement): array
    {
        if (!$statement->execute()) 
        {
            $this->setError($statement->error);
        }
        return $statement->fetchAll();
    }

    private function executeFetch(string $statement): array
    {
        if (!$statement->execute()) {
            $this->setError($statement->error);
        }
        return $statement->fetch();
    }

    private function debug(string $statement): array
    {
        return $statement->debugDumpParams();
    }    
}
?>