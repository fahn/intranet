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
namespace Badtra\Intranet\DB;
trait ApiDB
{


    public function APIGetTournamentFromToday(): array
    {
        $query     = "SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName, User.email FROM Tournament
                                   LEFT JOIN User ON User.userId = Tournament.reporterId
                                   WHERE Tournament.reporterId != '' AND Tournament.visible = 1 AND Tournament.deadline = CURDATE() ";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end APIGetTournamentFromToday()


    public function APIGetTournamentList(): array
    {
        $query     = "SELECT Tournament.* FROM Tournament";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end APIGetTournamentList()
}
