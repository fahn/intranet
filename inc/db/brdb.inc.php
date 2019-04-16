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

require_once $_SERVER['BASE_DIR'] . '/inc/logic/tools.inc.php';

include_once 'brdb.Api.inc.php';
include_once 'brdb.Categories.inc.php';
include_once 'brdb.Club.inc.php';
include_once 'brdb.Faq.inc.php';
include_once 'brdb.News.inc.php';
include_once 'brdb.Notification.inc.php';
include_once 'brdb.Player.inc.php';
include_once 'brdb.Ranking.inc.php';
include_once 'brdb.Settings.inc.php';
include_once 'brdb.Staff.inc.php';
include_once 'brdb.Tournament.inc.php';
include_once 'brdb.User.inc.php';

class BrankDB {

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
        // load connection
        $this->connection();
    }

    private function connection()
    {
        if ($this->db == NULL) {
            try {
                $tools = new Tools();

                $this->db = new mysqli($tools->getIniValue('db_host'), $tools->getIniValue('db_user'), $tools->getIniValue('db_pass'), $tools->getIniValue('db_name'));
                $this->db->set_charset("utf8mb4");

                return $this->db;
            } catch (Exception $e) {
                $this->setError($e);
                return NULL;
            }
        }
    }

    /**
     * Destructor that closes the DB connection
     */
    public function __destruct()
    {
        $this->db->close();
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

    public function insert_id()
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
}

?>
