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
trait RankingDB
{

    public function statementGetRanking()
    {
        $cmd = $this->db->prepare("SELECT elo.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name FROM `EloRanking` AS elo
                                 LEFT JOIN User ON User.userId = elo.userId
                                 #WHERE elo.win != 0 OR elo.loss != 0
                                 ORDER BY elo.points DESC");

        return $this->executeStatement($cmd);
    }

    /**
     */
    public function statementGetMatches()
    {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `eloGames` AS games
                                   LEFT JOIN User AS player ON player.userId = games.playerId
                                   LEFT JOIN User AS opponent ON opponent.userId = games.opponentId
                                   WHERE hidden = '0'
                                   ORDER BY games.time DESC");
        return $this->executeStatement($cmd);
    }

    /**
     * get Games group by Date for stats
     */
    public function statementGetMatchesGroupedByDate()
    {
        $cmd = $this->db->prepare("SELECT DATE(time) AS gamedate, COUNT(gameId) AS games
                                FROM `eloGames`
                                WHERE hidden = '0'
                                GROUP BY gamedate");

        return $this->executeStatement($cmd);
    }

    /**
     * get Game by ID
     */
    public function statementGetGameById($id)
    {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `eloGames` AS games
                                   LEFT JOIN User AS player ON player.userId = games.playerId
                                   LEFT JOIN User AS opponent ON opponent.userId = games.opponentId
                                   WHERE games.gameId = ?");
        $cmd->bind_param("i", $id);
        return $this->executeStatement($cmd);
    }

    public function deleteRanking()
    {
        $cmd = $this->db->prepare("TRUNCATE `eloRanking`");

        return $this->executeStatement($cmd);
    }

    public function insertMatch($a, $b, $sets, $winner)
    {
        $cmd = $this->db->prepare("INSERT INTO `eloGames` (playerId, opponentId, sets, winner) VALUES (?, ?, ?, ?)");
        $cmd->bind_param("iisi", $a, $b, $sets, $winner);

        return $this->executeStatement($cmd);
    }

    public function selectPoints($userId)
    {
        $cmd = $this->db->prepare("SELECT IFNULL( (SELECT points FROM `eloRanking` WHERE userId = ?) , '0')");
        $cmd->bind_param("i", $userId);

        return $this->executeStatement($cmd);
    }

    public function updatePoints($userId, $points, $win, $loss)
    {
        $cmd = $this->db->prepare("INSERT INTO `eloRanking` (userId, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE userId= ?, points= ?, win=win+?, loss=loss+?, lastMatch = NOW() ");
        $cmd->bind_param("iiiiii", $userId, $points, $userId, $points, $win, $loss);

        return $this->executeStatement($cmd);
    }

    public function selectLatestRankingGamesByPlayerId($id)
    {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name FROM eloGames AS games
                                LEFT JOIN User ON User.userId = games.opponentId
                                WHERE (games.playerId = ? OR games.opponentId = ?) AND games.hidden = 0
                                ORDER BY time DESC LIMIT 5");
        $cmd->bind_param("ii", $id, $id);

        return $this->executeStatement($cmd);
    }

    /**
     * delete game by ID
     */
    public function deleteMatch($id)
    {
        $cmd = $this->db->prepare("UPDATE `eloGames` SET hidden='1' WHERE gameId = ?");
        $cmd->bind_param("i", $id);

        return $this->executeStatement($cmd);
    }
}
?>
