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

trait RankingDB {

    public function statementGetRanking() {
        $query = "SELECT elo.*, CONCAT_WS(' ', Player.firstName, Player.lastName) AS name FROM `EloRanking` AS elo
                    LEFT JOIN Player ON Player.playerId = elo.playerId
                    #WHERE elo.win != 0 OR elo.loss != 0
                    ORDER BY elo.points DESC";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    /**
     */
    public function statementGetMatches() {
        $query = "SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `EloGames` AS games
                    LEFT JOIN Player AS player ON player.playerId = games.playerId
                    LEFT JOIN Player AS opponent ON opponent.playerId = games.opponentId
                    WHERE hidden = '0'
                    ORDER BY games.gameTime DESC";
        $statement = $this->db->prepare($query);

        return $statement->execute();

    }

    /**
     * get Games group by Date for stats
     */
    public function statementGetMatchesGroupedByDate() {
        $query = "SELECT DATE(gameTime) AS gameTime, COUNT(gameId) AS games
                    FROM `EloGames`
                    WHERE hidden = '0'
                    GROUP BY gameTime";
        $statement = $this->db->prepare($query);

        return $statement->execute();

    }

    /**
     * get Game by ID
     */
    public function statementGetGameById($gameId) {
        $query = "SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `EloGames` AS games
                    LEFT JOIN Player AS player ON player.playerId = games.playerId
                    LEFT JOIN Player AS opponent ON opponent.playerId = games.opponentId
                    WHERE games.gameId = :gameId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('gameId', $gameId);

        return $statement->execute();

    }

    public function deleteRanking(){
        $query = "TRUNCATE `EloRanking`";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    public function insertMatch($playerId, $b, $sets, $winnerId, $gameTime = false) {
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

    public function selectPoints($playerId) {
        $query = "SELECT IFNULL( (SELECT points FROM `EloRanking` WHERE playerId = :playerId) , '0')";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        

        return $statement->execute();
    }

    public function updatePoints($playerId, $points, $win, $loss) {
        $query = "INSERT INTO `EloRanking` (playerId, points) 
                    VALUES (playerId, points) ON DUPLICATE KEY UPDATE playerId= :playerId, points= points, win=win+:win, loss=loss+:loss, lastGame = NOW() ";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('points', $points);
        $statement->bindParam('win', $win);
        $statement->bindParam('loss', $loss);

        return $statement->execute();
    }

    public function selectLatestRankingGamesByPlayerId($actionId) {
        $query = "SELECT games.*, CONCAT_WS(' ', Player.firstName, Player.lastName) AS name FROM EloGames AS games
                                LEFT JOIN Player ON Player.playerId = games.opponentId
                                WHERE (games.playerId = :actionId OR games.opponentId = :actionId) AND games.hidden = 0
                                ORDER BY gameTime DESC LIMIT 5";
        $statement = $this->db->prepare($query);
        $statement->bindParam('actionId', $actionId);
        var_dump($statement->debugDumpParams());
        echo "<br>";

        $statement->execute();

        $games = $statement->fetchAll();

        return $games;
    }

    /**
     * delete game by ID
     */
    public function deleteMatch($gameId) {
        $query = "UPDATE `EloGames` SET hidden='1' WHERE gameId = ?";
        $statement->bindParam('gameId', $gameId);

        return $statement->execute();
    }

}
?>
