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
trait ClubDB
{

   
    public function selectGetClubById(int $clubId): array
    {
        $query     = "SELECT * FROM Club WHERE clubId = :clubId LIMIT 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);
        $statement->execute();

        return  $statement->fetchAll();
    }//end selectGetClubById()


    public function selectClubByClubNr(string $clubNr): array
    {
        $query     = "SELECT * FROM Club WHERE clubNr = :clubNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubNr', $clubNr);
        $statement->execute();

        return  $statement->fetchAll();
    }//end selectClubByClubNr()


    /**
     * Insert Club with name, nr & association
     *
     * @param  Club $club
     * @return boolean
     */
    public function insertClub(\Badtra\Intranet\Model\Club $club): bool
    {
        $query     = "INSERT INTO Club (name, clubNr, association) VALUES (:clubName, :clubNr, :association)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubName', $club->getClubName());
        $statement->bindParam('association', $club->getAssociation());
        $statement->bindParam('clubNr', $club->getClubNr());

        return $statement->execute();
    }//end insertClub()


     /**
      * Update Club
      *
      * @param  Club $club
      * @return boolean
      */
    public function updateClubById(\Badtra\Intranet\Model\Club $club): bool
    {
        $query     = "UPDATE `Club` SET `name` = :name, `clubNr` = :clubNr, `association` = :association WHERE `clubId` = :clubId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $club->getclubId());
        $statement->bindParam('clubName', $club->getClubName());
        $statement->bindParam('association', $club->getAssociation());
        $statement->bindParam('clubNr', $club->getClubNr());

        return $statement->execute();
    }//end updateClubById()


    /**
     * Update Club by ClubNr
     *
     * @param  Club $club
     * @return boolean
     */
    public function updateClubByClubNr(\Badtra\Intranet\Model\Club $club): bool
    {
        $query     = "UPDATE Club SET `name` = :clubName, association = :association WHERE clubNr = :clubNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubName', $club->getClubName());
        $statement->bindParam('association', $club->getAssociation());
        $statement->bindParam('clubNr', $club->getClubNr());

        return $statement->execute();
    }//end updateClubByClubNr()


    /**
     * Select all Clubs between min and max
     *
     * @param  integer $min
     * @param  integer $max
     * @return array
     */
    public function selectAllClubs(int $min = 0, int $max = 0): array
    {
        $limit     = $min != $max ? "LIMIT :min, :max" : "ASC";
        $query     = "SELECT * FROM Club ORDER by sort, name $limit";
        $statement = $this->db->prepare($query);
        $statement->bindParam('min', $min);
        $statement->bindParam('max', $max);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectAllClubs()


    /**
     * Delete Club with ID
     *
     * @param  integer $clubId
     * @return boolean
     */
    public function deleteClubById(int $clubId): bool
    {
        $query     = "DELETE Club  WHERE clubId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);

        return $statement->execute();
    }//end deleteClubById()
}
