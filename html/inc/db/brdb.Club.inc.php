<?php
declare(strict_types=1);

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

trait ClubDB 
{
    
    public function selectGetClubById(int $clubId): array
    {
        $query = "SELECT * FROM Club WHERE clubId = :clubId LIMIT 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);
        $statement->execute();

        return  $statement->fetchAll();
    }

    public function selectClubByClubNr(string $clubNr): array
    {
        $query = "SELECT * FROM Club WHERE clubNr = :clubNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubNr', $clubNr);
        $statement->execute();

        return  $statement->fetchAll();
    }


    /**
     * Insert Club with name, nr & association
     *
     * @param Club $club
     * @return boolean
     */
    public function insertClub(Club $club): bool
    {
        $query = "INSERT INTO Club (name, clubNr, association) VALUES (:clubName, :clubNr, :association)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubName', $club->getClubName());
        $statement->bindParam('association', $club->getAssociation());
        $statement->bindParam('clubNr', $club->getClubNr());

        return $statement->execute();
    }
     /*
     public function insertClubByModel($club) {

         $statement = 'INSERT INTO Club (name, clubNr, association) VALUES (:clubName, :clubNr, :association)');
         $statement->bindParam('clubName', $club['clubName']);
         $statement->bindParam('clubNr', $club['clubNr']);
         $statement->bindParam('association', $club['association']);

         return $this->executeStatement($statement);
     }
     */


     /**
      * Update Club
      *
      * @param Club $club
      * @return boolean
      */
    public function updateClubById(Club $club): bool 
    {
        $query = "UPDATE `Club` SET `name` = :name, `clubNr` = :clubNr, `association` = :association WHERE `clubId` = :clubId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $club->getclubId());
        $statement->bindParam('clubName', $club->getClubName());
        $statement->bindParam('association', $club->getAssociation());
        $statement->bindParam('clubNr', $club->getClubNr());

        return $statement->execute();
    }

    /**
     * Update Club by ClubNr
     *
     * @param Club $club
     * @return boolean
     */
    public function updateClubByClubNr(Club $club): bool
    {
        $query = "UPDATE Club SET `name` = :clubName, association = :association WHERE clubNr = :clubNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubName', $club->getClubName());
        $statement->bindParam('association', $club->getAssociation());
        $statement->bindParam('clubNr', $club->getClubNr());

        return $statement->execute();
    }

    /**
     * Select all Clubs between min and max
     *
     * @param integer $min
     * @param integer $max
     * @return array
     */
    public function selectAllClubs(int $min = 0, int $max = 0): array
    {
        $limit = $min != $max ? "LIMIT :min, :max" : "ASC";
        $query = "SELECT * FROM Club ORDER by sort, name $limit";
        $statement = $this->db->prepare($query);
        $statement->bindParam('min', $min);
        $statement->bindParam('max', $max);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Delete Club with ID
     *
     * @param integer $clubId
     * @return boolean
     */
    public function deleteClubById(int $clubId): bool
    {
        $query = "DELETE Club  WHERE clubId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);

        return $statement->execute();
    }
}
?>
