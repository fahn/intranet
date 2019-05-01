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

    public function statementGetRanking() {
        $cmd = $this->db->prepare("SELECT elo.*, CONCAT_WS(' ', Player.firstName, Player.lastName) AS name FROM `EloRanking` AS elo
                                 LEFT JOIN Player ON Player.playerId = elo.playerId
                                 #WHERE elo.win != 0 OR elo.loss != 0
                                 ORDER BY elo.points DESC");

        return $this->executeStatement($cmd);
    }

    /**
     */
    public function statementGetMatches()
    {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `EloGames` AS games
                                   LEFT JOIN Player AS player ON player.playerId = games.playerId
                                   LEFT JOIN Player AS opponent ON opponent.playerId = games.opponentId
                                   WHERE hidden = '0'
                                   ORDER BY games.gameTime DESC");
        return $this->executeStatement($cmd);
    }

    /**
     * get Games group by Date for stats
     */
    public function statementGetMatchesGroupedByDate()
    {
        $cmd = $this->db->prepare("SELECT DATE(gameTime) AS gameTime, COUNT(gameId) AS games
                                FROM `EloGames`
                                WHERE hidden = '0'
                                GROUP BY gameTime");

        return $this->executeStatement($cmd);
    }

    /**
     * get Game by ID
     */
    public function statementGetGameById($id)
    {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `EloGames` AS games
                                   LEFT JOIN Player AS player ON player.playerId = games.playerId
                                   LEFT JOIN Player AS opponent ON opponent.playerId = games.opponentId
                                   WHERE games.gameId = ?");
        $cmd->bind_param("i", $id);
        
        return $this->executeStatement($cmd);
    }

    public function deleteRanking(){
        $cmd = $this->db->prepare("TRUNCATE `EloRanking`");

        return $this->executeStatement($cmd);
    }

    public function insertMatch($a, $b, $sets, $winner, $time = false) {
        if ($time == false) {
            $time = strtotime("now");
        }
        $cmd = $this->db->prepare("INSERT INTO `EloGames` (playerId, opponentId, sets, winnerId, gameTime) VALUES (?, ?, ?, ?, ?)");
        $cmd->bind_param("iisis", $a, $b, $sets, $winner, $time);

        return $this->executeStatement($cmd);
    }

    public function selectPoints($playerId){
        $cmd = $this->db->prepare("SELECT IFNULL( (SELECT points FROM `EloRanking` WHERE playerId = ?) , '0')");
        $cmd->bind_param("i", $playerId);

        return $this->executeStatement($cmd);
    }

    public function updatePoints($playerId, $points, $win, $loss)
    {
        $cmd = $this->db->prepare("INSERT INTO `EloRanking` (playerId, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE playerId= ?, points= ?, win=win+?, loss=loss+?, lastGame = NOW() ");
        $cmd->bind_param("iiiiii", $playerId, $points, $playerId, $points, $win, $loss);

        return $this->executeStatement($cmd);
    }

    public function selectLatestRankingGamesByPlayerId($id)
    {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', Player.firstName, Player.lastName) AS name FROM EloGames AS games
                                LEFT JOIN Player ON Player.playerId = games.opponentId
                                WHERE (games.playerId = ? OR games.opponentId = ?) AND games.hidden = 0
                                ORDER BY gameTime DESC LIMIT 5");
        $cmd->bind_param("ii", $id, $id);

        return $this->executeStatement($cmd);
    }

    /**
     * delete game by ID
     */
    public function deleteMatch($id)
    {
        $cmd = $this->db->prepare("UPDATE `EloGames` SET hidden='1' WHERE gameId = ?");
        $cmd->bind_param("i", $id);

        return $this->executeStatement($cmd);
    }

}
?>
