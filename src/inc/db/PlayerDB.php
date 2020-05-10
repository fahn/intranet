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
trait PlayerDB
{


    /**
     * Get All Player
     *
     * @return array
     */
    public function selectGetAllPlayer(): array
    {
        $query     = "SELECT Player.*, CONCAT_WS(' ', Player.firstName, Player.lastName) as fullName, Club.name AS clubName FROM Player
                    LEFT JOIN `Club` ON Club.clubId = Player.clubId
                    ORDER BY Player.lastName ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end selectGetAllPlayer()


    /**
     * Get All Player in list between N and M
     *
     * @param  integer $min
     * @param  integer $max
     * @return array
     */
    public function selectGetAllPlayerMM(int $min = 0, int $max = 50): array
    {
        $query     = "SELECT Player.*, CONCAT_WS(' ', Player.firstName, Player.lastName) as fullName, Club.name AS clubName FROM Player
                    LEFT JOIN `Club` ON Club.clubId = Player.clubId
                    ORDER BY Player.lastName ASC
                    LIMIT :min, :max";
        $statement = $this->db->prepare($query);
        $statement->bindParam('min', $min, \PDO::PARAM_INT);
        $statement->bindParam('max', $max, \PDO::PARAM_INT);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end selectGetAllPlayerMM()


    /**
     * Get Player by Id
     *
     * @param  integer $playerId
     * @return array
     */
    public function selectPlayerById(int $playerId): array
    {
        $query     = "SELECT Player.*, Club.name AS clubName FROM Player
                    LEFT JOIN `Club` ON Club.clubId = Player.clubId
                    WHERE playerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId, \PDO::PARAM_INT);
        $statement->execute();
       
        return $statement->fetch();
    }//end selectPlayerById()


    /**
     * Get Player by PlayerNr
     *
     * @param  String $playerNr
     * @return array
     */
    public function selectPlayerByPlayerNr(String $playerNr): array
    {
        $query     = "SELECT * FROM Player WHERE playerNr = :playerNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerNr', $playerNr, \PDO::PARAM_STR);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end selectPlayerByPlayerNr()


    /**
     * Inser Player with Data
     *
     * @param  array $data
     * @return boolean
     */
    public function insertPlayer(\Badtra\Intranet\Model\Player $player): bool
    {
        $query     = "INSERT INTO Player (playerNr, clubId, firstName, lastName, bday, gender)
                        VALUES (:playerNr, :clubId, :firstName, :lastName, :bday, :gender)";
        $statement = $this->db->prepare($query);
      
        $statement->bindParam('playerNr', $player->getPlayerNr());
        $statement->bindParam('clubId', $player->getClubId(), \PDO::PARAM_INT);
        $statement->bindParam('firstName', $player->getFirstname());
        $statement->bindParam('lastName', $player->getLastName());
        $statement->bindParam('gender', $player->getGender());
        $statement->bindParam('bday', $player->getBday());

        return $statement->execute();
    }//end insertPlayer()


    /**
     * Update Player
     *
     * @param  Player $player
     * @return boolean
     */
    public function updatePlayer(\Badtra\Intranet\Model\Player $player): bool
    {
        try {
            $query     = "UPDATE `Player` SET firstName = :firstName, lastName = :lastName, gender = :gender, bday = :bday, clubId = :clubId, playerNr = :playerNr
                        WHERE playerId = :playerId";
            $statement = $this->db->prepare($query);
            $statement->bindParam('playerId', $player->getPlayerId());
            $statement->bindParam('clubId', $player->getClubId(), \PDO::PARAM_INT);
            $statement->bindParam('firstName', $player->getFirstname());
            $statement->bindParam('lastName', $player->getLastName());
            $statement->bindParam('gender', $player->getGender());
            $statement->bindParam('bday', $player->getBday());
            $statement->bindParam('playerNr', $player->getPlayerNr());
           
            return $statement->execute();
        } catch (\Exception $e) {
            return false;
        }
    }//end updatePlayer()


    /**
     * delete player hy playerId
     *
     * @param  integer $playerId
     * @return boolean
     */
    public function deletePlayer(int $playerId): bool
    {
        $query     = "DELETE * FROM `Player` WHERE playerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerNr', $playerId);

        return $statement->execute();
    }//end deletePlayer()


    /**
     * Get Player LIKE %term%
     *
     * @param  String $term
     * @return array
     */
    public function getPlayerByTerm(String $term): array
    {
        // preparing
        $term = '%'.$term.'%';

        // sql query
        $query     = "SELECT Player.*, CONCAT_WS(', ', Player.lastName, Player.firstName) AS playerName, Club.name AS clubName FROM Player
                    LEFT JOIN Club ON Club.clubId = Player.clubId
                    WHERE CONCAT_WS(' ', Player.firstName, Player.lastName) LIKE :term
                    ORDER BY Player.lastName";
        $statement = $this->db->prepare($query);
        $statement->bindParam('term', $term, \PDO::PARAM_STR);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end getPlayerByTerm()
}
