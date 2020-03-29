<?php 
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
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

require_once(BASE_DIR . '/inc/logic/tools.inc.php');

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
        if (!defined('PDO::ATTR_DRIVER_NAME')) 
        {
            throw new Exception\Badtra("123");
        }
        // load connection
        $this->db = $this->connection();
    }

    private function connection() 
    {
        if ($this->db == NULL) 
        {
            try 
            {
                $tools    = new Tools();

                $database = sprintf('mysql:host=%s;dbname=%s', $tools->getIniValue('db_host'), $tools->getIniValue('db_name'));

                return new \PDO($database, $tools->getIniValue('db_user'), $tools->getIniValue('db_pass'));

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
    public function getError() 
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
    private function setError($error) 
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
    public function executeStatement($statement) 
    {
        if (! $statement->execute()) {
            $this->setError($statement->error);
        }
        return $statement->get_result();
    }

    public function countRows($statement): int
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

    private function executeFetchAll($statement) 
    {
        if (!$statement->execute()) {
            $this->setError($statement->error);
        }
        return $statement->fetchAll();
    }

    private function executeFetch($statement) 
    {
        if (!$statement->execute()) {
            $this->setError($statement->error);
        }
        return $statement->fetch();
    }

    private function debug($statement) 
    {
        return $statement->debugDumpParams();
    }

    
}

?>
