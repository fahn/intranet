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

trait TournamentDB 
{
    /**
     * Select all valid tournament
     * NOW < enddate
     *
     * @return array
     */
    public function selectTournamentList(): array
    {
        $query = "SELECT T.*, (SELECT COUNT(*) from TournamentPlayer AS TP where TP.visible = 1 AND TP.tournamentId = T.tournamentId) AS userCounter FROM Tournament AS T
                  WHERE T.visible = 1 AND T.enddate > (NOW() - INTERVAL 4 DAY) ORDER by T.startdate ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();
        $statement->errorInfo();

        return $statement->fetchAll();
    }

    /**
     * Select all old tournament 
     * NOW > enddata
     *
     * @return array
     */
    public function selectOldTournamentList(): array
    {
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

    public function selectLatestTournamentList(int $max = 5): array
    {
        $query = "SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate ASC LIMIT :limit";
        $statement = $this->db->prepare($query);
        $statement->bindParam('limit', $max);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get all upcoming tournaments
     *
     * @param integer $max
     * @return array
     */
    public function selectUpcomingTournamentList(int $max = 5): array
    {
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

    public function selectUpcomingTournamentPlayer(int $tournamentId): array
    {
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

    public function selectUpcomingTournamentPlayerOrginal(int $tournamentId): array
    {
        $query = "SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1 GROUP BY playerId
                    UNION
                    SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1 AND partnerId > 0 GROUP BY partnerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }


    /**
     * get Tournament details by Id
     *
     * @param integer $tournamentId
     * @return array
     */
    public function getTournamentData(int $tournamentId): array
    {
        $query = "SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName FROM Tournament
                    LEFT JOIN User ON User.userId = Tournament.reporterId
                    WHERE Tournament.tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Get Players from tournament
     *
     * @param integer $tournamentId
     * @return array
     */
    public function getPlayersByTournamentId(int $tournamentId): array 
    {
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

    /**
     * Get Players from Tournament for Export
     *
     * @param integer $tournamentId
     * @return array
     */
    public function getPlayersByTournamentIdToExport(int $tournamentId): array
    {
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

    /**
     * Get Disciplines by tournament
     *
     * @param integer $tournamentId
     * @return array
     */
    public function getDisciplinesByTournamentId(int $tournamentId): array
    {
        $query =  "SELECT * FROM TournamentClass AS TC WHERE TC.tournamentId = :tournamentId AND TC.visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    /**
     * Insert Player by Tournament
     *
     * @param integer $tournamentId
     * @param integer $playerId
     * @param integer $partnerId
     * @param string $classification
     * @param integer $reporterId
     * @return boolean
     */
    public function insertPlayerToTournament(int $tournamentId, int $playerId, int $partnerId,  string $classification, int $reporterId): bool
    {
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

    /**
     * Insert Tournament
     *
     * @param string $name
     * @param string $place
     * @param string $startdate
     * @param string $enddate
     * @param string $deadline
     * @param string $link
     * @param string $classification
     * @param string $additionalClassification
     * @param string $discipline
     * @param string $reporterId
     * @param string $tournamentType
     * @param string $latitude
     * @param string $longitude
     * @param string $description
     * @return boolean
     */
    public function insertTournament(string $name, string $place, string $startdate, string $enddate, string $deadline, string $link, string $classification, string $additionalClassification, string $discipline, string $reporterId, string $tournamentType, string $latitude, string $longitude, string $description): bool
    {
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


    /**
     * Insert class from tournament
     *
     * @param integer $id
     * @param string $name
     * @param string $mode
     * @return boolean
     */
    public function insertTournamentClass(int $id, string $name, string $mode): bool
    {
        $query = "INSERT INTO `TournamentClass` SET (`tournamentId`, `name`, `modus`) VALUES (:id, :name, :mode)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->bindParam('name', $name);
        $statement->bindParam('mode', $mode);

        return $statement->execute();
    }

    /**
     * Delete Player from Tournament
     *
     * @param integer $tournamentId
     * @param integer $playerId
     * @return boolean
     */
    public function deletePlayersFromTournamentId(int $tournamentId, int $playerId): bool
    {
        $query = "UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }

    /**
     * Delete all players from Tournament
     *
     * @param integer $tournamentId
     * @return boolean
     */
    public function deleteAllPlayersFromTournamentById(int $tournamentId): bool
    {
        $query = "UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    /**
     * Delete class from Tournament
     */
    public function deleteClassFromTournamentById(int $tournamentId): bool
    {
        $query = "UPDATE TournamentClass set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    /**
     * Delete tournament by Id
     *
     * @param integer $tournamentId
     * @return boolean
     */
    public function deleteTournamentById(int $tournamentId): bool
    {
        $query = "UPDATE Tournament set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    /**
     * Update tournament by Id
     *
     * @param integer $tournamentId
     * @param string $name
     * @param string $place
     * @param string $startdate
     * @param [type] $enddate
     * @param [type] $deadline
     * @param [type] $link
     * @param [type] $classification
     * @param [type] $additionalClassification
     * @param [type] $discipline
     * @param [type] $reporterId
     * @param [type] $tournamentType
     * @param [type] $latitude
     * @param [type] $longitude
     * @param [type] $description
     * @return boolean
     */
    public function updateTournamentById(int $tournamentId, string $name, string $place, string $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description): bool
    {
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

    /**
     * Select Tournament by User
     *
     * @param integer $id
     * @return array
     */
    public function selectGetLatestTournamentFromUserId(int $userId): array
    {
        $query = "SELECT * FROM Tournament 
                    WHERE tournamentId IN (SELECT tournamentId FROM TournamentPlayer Where playerId = :userId or partnerId = :userId 
                    GROUP BY tournamentId) 
                    ORDER BY enddate DESC LIMIT 10";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * get player from Tournament
     *
     * @param integer $tournamentId
     * @param integer $playerId
     * @param integer $partnerId
     * @param string $classification
     * @return array
     */
    public function getTournamentPlayerByData(int $tournamentId, int $playerId, int $partnerId, string $classification): array
    {
        $query = "SELECT * FROM TournamentPlayer
                    WHERE tournamentId = :tournamentId AND playerId = :playerId AND partnerId = :partnerId AND classification = :classification AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('partnerId', $partnerId);
        $statement->bindParam('classification', $classification);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * set backup from Tournament
     *
     * @param integer $tournamentId
     * @param string $data
     * @return boolean
     */
    public function insertTournamentBackup(int $tournamentId, string $data): bool
    {
        $query = "INSERT INTO TournamentBackup (tournamentId, data, date) VALUES (:tournamentId, :data, NOW())";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('data', $data);

        return $statement->execute();
    }

    /**
     * get tournament backup
     *
     * @param integer $tournamentId
     * @return array
     */
    public function getTournamentBackup(int $tournamentId): array
    {
        $query = "SELECT * FROM `TournamentBackup` WHERE `tournamentId` = :tournamentId ORDER BY `backupId` DESC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * get tournament backup diff
     *
     * @param integer $first
     * @param integer $second
     * @return array
     */
    public function getTournamentBackupDiff(int $first, int $second): array
    {
        $query = "SELECT data FROM `TournamentBackup` WHERE `backupId` in (:first,:second) ORDER BY `backupId` DESC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('first', $first);
        $statement->bindParam('second', $second);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get player from Tournament
     *
     * @param integer $playerId
     * @return array
     */
    public function getPlayerFromTournamentById(int $playerId): array
    {
        $query = "SELECT TournamentPlayer.*,
                (SELECT playerNr FROM Player WHERE playerId = TournamentPlayer.playerId ) AS playerNr,
                (SELECT playerNr FROM Player WHERE playerId = TournamentPlayer.partnerId ) AS partnerNr,
                (SELECT playerNr FROM User WHERE userId = TournamentPlayer.reporterId ) AS reporterNr FROM TournamentPlayer
                WHERE tournamentPlayerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Unlock player from Torunament
     *
     * @param integer $tournamentId
     * @param integer $playerId
     * @return boolean
     */
    public function unlockPlayerFromTournament(int $tournamentId, int $playerId): bool
    {
        $query = "UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }

    /**
     * Unlock all player from Tournament
     *
     * @param integer $tournamentId
     * @return boolean
     */
    public function unlockAllPlayerFromTournament(int $tournamentId): bool
    {
        $query = "UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = :tournamentId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }

    /**
     * Lock player from Tournament
     *
     * @param integer $tournamentId
     * @param integer $playerId
     * @return boolean
     */
    public function lockPlayerFromTournament(int $tournamentId, int $playerId): bool
    {
        $query = "UPDATE TournamentPlayer set locked = 1 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }
}

?>