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
trait RankingDB
{

    /**
     * Get Ranking
     *
     * @return void
     */
    public function statementGetRanking()
    {
        $query = "SELECT elo.*, CONCAT_WS(' ', Player.firstName, Player.lastName) AS name FROM `EloRanking` AS elo
                    LEFT JOIN Player ON Player.playerId = elo.playerId
                    #WHERE elo.win != 0 OR elo.loss != 0
                    ORDER BY elo.points DESC";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();
    }

    /**
     * Get all Matches
     *
     * @return array
     */
    public function getMatches():array
    {
        $query = "SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `EloGames` AS games
                    LEFT JOIN Player AS player ON player.playerId = games.playerId
                    LEFT JOIN Player AS opponent ON opponent.playerId = games.opponentId
                    WHERE hidden = '0'
                    ORDER BY games.gameTime DESC";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();

    }

    /**
     * Get Matches
     *
     * @return array
     */
    public function getMatchesGroupedByDate(): array
    {
        $query = "SELECT DATE(gameTime) AS gameTime, COUNT(gameId) AS games
                    FROM `EloGames`
                    WHERE hidden = '0'
                    GROUP BY gameTime";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetchAll();

    }

    /**
     * Get Game by Id
     *
     * @param integer $gameId
     * @return array
     */
    public function getGameById(int $gameId): array
    {
        $query = "SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `EloGames` AS games
                    LEFT JOIN Player AS player ON player.playerId = games.playerId
                    LEFT JOIN Player AS opponent ON opponent.playerId = games.opponentId
                    WHERE games.gameId = :gameId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('gameId', $gameId);
        $statement->execute();
       
        return $statement->fetchAll();

    }

    /**
     * Insert Match
     *
     * @param integer $playerId
     * @param integer $oppentId
     * @param String $sets
     * @param integer $winnerId
     * @param boolean $gameTime
     * @return boolean
     */
    public function insertMatch(int $playerId, int $oppentId, String $sets, int $winnerId, bool $gameTime = false): bool
    {
        $gameTime = $gameTime == false ? strtotime("now") : $gameTime;
       
        $query = "INSERT INTO `EloGames` (playerId, opponentId, sets, winnerId, gameTime) VALUES (:playerId, :oppentId, :sets, :winnerId, :gameTime)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('oppentId', $oppentId);
        $statement->bindParam('sets', $sets);
        $statement->bindParam('winnerId', $winnerId);
        $statement->bindParam('gameTime', $gameTime);
       
        return $statement->execute();
    }

    /**
     * Get Points from Player
     *
     * @param integer $playerId
     * @return array
     */
    public function selectPoints(int $playerId): array
    {
        $query = "SELECT IFNULL( (SELECT points FROM `EloRanking` WHERE playerId = :playerId) , '0')";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->execute();
       
        return $statement->fetchAll();
    }

    /**
     * Update points from player by Id
     *
     * @param integer $playerId
     * @param integer $points
     * @param string $win
     * @param string $loss
     * @return boolean
     */
    public function updatePoints(int $playerId, int $points, string $win, string $loss): bool
    {
        $query = "INSERT INTO `EloRanking` (playerId, points)
                    VALUES (playerId, points) ON DUPLICATE KEY UPDATE playerId= :playerId, points= points, win=win+:win, loss=loss+:loss, lastGame = NOW() ";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('points', $points);
        $statement->bindParam('win', $win);
        $statement->bindParam('loss', $loss);

        return $statement->execute();
    }

    /**
     * Get latest Gamey by Player
     *
     * @param array $actionId
     * @return void
     */
    public function selectLatestRankingGamesByPlayerId($actionId): array
    {
        $query = "SELECT games.*, CONCAT_WS(' ', Player.firstName, Player.lastName) AS name FROM EloGames AS games
                                LEFT JOIN Player ON Player.playerId = games.opponentId
                                WHERE (games.playerId = :actionId OR games.opponentId = :actionId) AND games.hidden = 0
                                ORDER BY gameTime DESC LIMIT 5";
        $statement = $this->db->prepare($query);
        $statement->bindParam('actionId', $actionId);

        $statement->execute();

        $games = $statement->fetchAll();

        return $games;
    }

    /**
     * Delete Match by Id
     *
     * @param integer $gameId
     * @return boolean
     */
    public function deleteMatch(int $gameId): bool
    {
        $query = "UPDATE `EloGames` SET hidden='1' WHERE gameId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('gameId', $gameId);

        return $statement->execute();
    }

    /**
     * truncate Ranking
     *
     * @return boolean
     */
    public function truncateRanking(): bool
    {
        $query = "TRUNCATE `EloRanking`";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

}

