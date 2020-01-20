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
declare(strict_types=1);

trait TournamentDB {
    public function selectTournamentList(){
        $query = "SELECT T.*, (SELECT COUNT(*) from TournamentPlayer AS TP where TP.visible = 1 AND TP.tournamentId = T.tournamentId) AS userCounter FROM Tournament AS T
                  WHERE T.visible = 1 AND T.enddate > (NOW() - INTERVAL 4 DAY) ORDER by T.startdate ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectOldTournamentList() {
        $query = "SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate DESC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

/*
    public function selectLatestTournamentList($max = 5) {
        $cmd = $this->db->prepare("SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate ASC LIMIT ?");
        $cmd->bind_param("i", $max);

        return $this->executeStatement($cmd);
    } */

    public function selectLatestTournamentList(int $max = 5) {
        $query = "SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate ASC LIMIT :limit";
        $statement = $this->db->prepare($query);
        $statement->bindParam('limit', $max);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectUpcomingTournamentList($max = 5) {
        /*  ?
        SELECT *, (SELECT COUNT(*) FROM ( SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = 1 GROUP BY playerId UNION SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = Tournament.tournamentId and partnerId > 0 GROUP BY partnerId ) AS T ) AS participant
        FROM Tournament
        WHERE visible = 1 AND startdate > NOW()
        ORDER by startdate ASC LIMIT*/
        $query = "SELECT Tournament.*
                    FROM Tournament
                    WHERE visible = 1 AND startdate > NOW()
                    ORDER by startdate ASC LIMIT :max";
        $statement = $this->db->prepare($query);
        $statement->bindParam('max', $max);
        #(SELECT COUNT(*) FROM TournamentPlayer AS TP WHERE TP.tournamentId = Tournament.tournamentId AND TP.visible = 1  ) AS participant#
        $statement->execute();

        return $statement->fetchAll();
        
    }

    public function selectUpcomingTournamentPlayer($tournamentId) {
        $query = "SELECT COUNT(DISTINCT concat(playerId,partnerId)) from TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();
        /*SELECT COUNT(*) FROM (
                                      SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = ? AND visible = 1 GROUP BY playerId
                                      UNION
                                      SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = ? AND visible = 1 AND partnerId > 0 GROUP BY partnerId
                                  ) AS result GROUP BY playerId");
                                  */

        return $statement->fetchAll();
    }

    public function selectUpcomingTournamentPlayerOrginal($tournamentId) {
        $query = "SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1 GROUP BY playerId
                    UNION
                    SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1 AND partnerId > 0 GROUP BY partnerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }


    public function getTournamentData($tournamentId) {
        $query = "SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName FROM Tournament
                    LEFT JOIN User ON User.userId = Tournament.reporterId
                    WHERE Tournament.tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetch();
    }

    public function getPlayersByTournamentId($tournamentId) {
        $query = "SELECT
                    TP.*,
                    CONCAT_WS(' ', Player.firstName, Player.lastName) AS playerName,
                    CONCAT_WS(' ', Partner.firstName, Partner.lastName) as partnerName,
                    CONCAT_WS(' ', Reporter.firstName, Reporter.lastName) as reporterName,
                    Partner.playerNr as partnerNr
                    FROM TournamentPlayer AS TP
                    LEFT JOIN Player ON TP.playerId = Player.playerId
                    LEFT JOIN Player as Partner ON TP.partnerId = Partner.playerId
                    LEFT JOIN User AS Reporter ON TP.reporterId = Reporter.userId
                    WHERE TP.tournamentId = :tournamentId AND TP.visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getPlayersByTournamentIdToExport($tournamentId) {
        $query = "SELECT
                    TP.*,
                    Player.firstName AS p1FirstName, Player.lastName AS p1LastName, Player.gender AS p1Gender, Player.bday AS p1Bday, Player.playerNr AS p1PlayerNumber,
                    C1.name as p1ClubName, C1.clubNr AS p1ClubNr, C1.association AS p1ClubAssociation,
                    Partner.firstName AS p2FirstName, Partner.lastName AS p2LastName, Partner.gender AS p2Gender, Partner.bday AS p2Bday, Partner.playerNr AS p2PlayerNumber,
                    C2.name as p2ClubName, C2.clubNr AS p2ClubNr, C2.association AS p2ClubAssociation
                    FROM TournamentPlayer AS TP
                    LEFT JOIN Player ON TP.playerId = Player.playerId
                    LEFT JOIN Player as Partner ON TP.partnerId = Partner.playerId
                    LEFT JOIN Club AS C1 ON C1.clubID = Player.clubId
                    LEFT JOIN Club AS C2 ON C2.clubID = Partner.clubId
                    WHERE TP.tournamentId = ? AND TP.visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getDisciplinesByTournamentId($tournamentId) {
        $query =  "SELECT * FROM TournamentClass AS TC WHERE TC.tournamentId = :tournamentId AND TC.visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function insertPlayerToTournament($tournamentId, $playerId, $partnerId, $classification, $reporterId) {
        $query = "INSERT INTO TournamentPlayer (tournamentId, playerId, partnerId, classification, reporterId) 
                    VALUES (:tournamentId, :playerId, :partnerId, :classification, :reporterId)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('partnerId', $partnerId);
        $statement->bindParam('classification', $classification);
        $statement->bindParam('reporterId', $reporterId);

        return $statement->execute();
    }


    public function insertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description) {
        $query = "INSERT INTO Tournament (name, place, startdate, enddate, deadline, link, classification, additionalClassification, discipline, reporterId, tournamentType, latitude, longitude, description) 
                    VALUES (:name, :place, :startdate, :enddate, :deadline, :link, :classification, :additionalClassification, :discipline, :reporterId, :tournamentType, :latitude, :longitude, :description)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('place', $place);
        $statement->bindParam('startdate', $startdate);
        $statement->bindParam('enddate', $enddate);
        $statement->bindParam('deadline', $deadline);
        $statement->bindParam('link', $link);
        $statement->bindParam('classification', $classification);
        $statement->bindParam('additionalClassification', $additionalClassification);
        $statement->bindParam('discipline', $discipline);
        $statement->bindParam('reporterId', $reporterId);
        $statement->bindParam('tournamentType', $tournamentType);
        $statement->bindParam('latitude', $latitude);
        $statement->bindParam('longitude', $longitude);
        $statement->bindParam('description', $description);

        return $statement->execute();
    }

    /**
     *
     */


    /** Insert class from Tournament
     *
     */
    public function insertTournamentClass($id, $name, $mode) {
        $query = "INSERT INTO TournamentClass set (tournamentId, name, modus) VALUES (:id, :name, :mode)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->bindParam('name', $name);
        $statement->bindParam('mode', $mode);

        return $statement->execute();
    }

    public function deletePlayersFromTournamentId($tournamentId, $playerId) {
        $query = "UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }

    public function deleteAllPlayersFromTournamentById($tournamentId) {
        $query = "UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    public function deleteClassFromTournamentById($tournamentId) {
        $query = "UPDATE TournamentClass set visible = 0 WHERE tournamentId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    public function deleteTournamentById($tournamentId) {
        $query = "UPDATE Tournament set visible = 0 WHERE tournamentId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    public function updateTournamentById($tournamentId, $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description) {
        $query = "UPDATE Tournament 
                    set name = :name, place= :place, startdate=:startdate, enddate=:enddate, deadline=:deadline, link=:link, classification = :classification, additionalClassification = :additionalClassification, 
                    discipline = :discipline, reporterId = :reporterId, tournamentType = :tournamentType, latitude = :latitude, longitude = :longitude, description = :description
                    WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('name', $name);
        $statement->bindParam('place', $place);
        $statement->bindParam('startdate', $startdate);
        $statement->bindParam('enddate', $enddate);
        $statement->bindParam('deadline', $deadline);
        $statement->bindParam('link', $link);
        $statement->bindParam('classification', $classification);
        $statement->bindParam('additionalClassification', $additionalClassification);
        $statement->bindParam('discipline', $discipline);
        $statement->bindParam('reporterId', $reporterId);
        $statement->bindParam('tournamentType', $tournamentType);
        $statement->bindParam('latitude', $latitude);
        $statement->bindParam('longitude', $longitude);
        $statement->bindParam('description', $description);
        
        return $statement->execute();
    }

    public function selectGetLatestTournamentFromUserId($id) {
        $query = "SELECT * FROM Tournament 
                    WHERE tournamentId IN (SELECT tournamentId FROM TournamentPlayer Where playerId = :id or partnerId = :id 
                    GROUP BY tournamentId) 
                    ORDER BY enddate DESC LIMIT 10";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getTournamentPlayerByData($tournamentId, $playerId, $partnerId, $classification) {
        $query = "SELECT * FROM TournamentPlayer
                    WHERE tournamentId = :tournamentId AND playerId = :playerId AND partnerId = :partnerId AND classification = :classification AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('tournamentId', $playerId);
        $statement->bindParam('partnerId', $partnerId);
        $statement->bindParam('classification', $classification);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function insertTournamentBackup($tournamentId, $data) {
        $query = "INSERT INTO TournamentBackup (tournamentId, data, date) VALUES (:tournamentId, :data, NOW())";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('data', $data);

        return $statement->execute();
    }

    public function getTournamentBackup($tournamentId) {
        $query = "SELECT * FROM TournamentBackup WHERE tournamentId = :tournamentId ORDER BY backupId DESC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getTournamentBackupDiff($first, $second){
        $query = "SELECT data FROM TournamentBackup WHERE backupId in (:first,:second) ORDER BY backupId DESC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('first', $first);
        $statement->bindParam('second', $second);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getPlayerFromTournamentById($playerId) {
        $query = "SELECT TournamentPlayer.*,
                (SELECT playerNr FROM Player WHERE playerId = TournamentPlayer.playerId ) AS playerNr,
                (SELECT playerNr FROM Player WHERE playerId = TournamentPlayer.partnerId ) AS partnerNr,
                (SELECT playerNr FROM User WHERE userId = TournamentPlayer.reporterId ) AS reporterNr FROM TournamentPlayer
                WHERE tournamentPlayerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function unlockPlayerFromTournament($tournamentId, $playerId) {
        $query = "UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }

    public function unlockAllPlayerFromTournament($tournamentId) {
        $query = "UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = :tournamentId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    public function lockPlayerFromTournament($tournamentId, $playerId) {
        $query = "UPDATE TournamentPlayer set locked = 1 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }
}

?>
