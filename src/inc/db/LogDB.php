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

use PDOException;
trait LogDB
{


    /**
     * get all logs
     *
     * @return array
     */
    public function statementGetAllLogs(): array
    {
        $query     = "SELECT * FROM Log ORDER BY tstamp DESC";
        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }//end statementGetAllLogs()


    /**
     * Insert Log
     *
     * @param  string  $table
     * @param  string  $details
     * @param  string  $logdata
     * @param  string  $action
     * @param  integer $userId
     * @return boolean
     */
    public function insertLog(string $fromTable, string $details, string $logdata, string $action, ?int $userId = 0): bool
    {
        try {
            $query     = "INSERT INTO `Log` (`userId`, `action`, `fromTable`, `details`, `logdata`) 
            VALUES (:userId, :action, :fromTable, :details, :logdata)";
            $statement = $this->db->prepare($query);
            $statement->bindParam('fromTable', $fromTable);
            $statement->bindParam('details', $details);
            $statement->bindParam('logdata', $logdata);
            $statement->bindParam('action', $action);
            $statement->bindParam('userId', $userId);
            echo "<pre>";
            echo "Table: ".$fromTable ."<br>";
            echo "Details: ".$details ."<br>";
            echo "Logdata: ".$logdata ."<br>";
            echo "Action: ".$action ."<br>";
            echo "Userid: ".$userId ."<br>";
            echo "</pre>";

            

            if (!$statement->execute()) {
                $errorInfo = $statement->errorInfo();
                echo "SQL Error: " . $errorInfo[2];
            }

            $statement->errorInfo();
            $statement->debugDumpParams();
           
            return $statement->execute();

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new \Exception($statement->errorInfo());
        }
    }//end insertLog()
}
