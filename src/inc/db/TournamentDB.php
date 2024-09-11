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
        $query     = "SELECT T.*, (SELECT COUNT(*) from TournamentPlayer AS TP where TP.visible = 1 AND TP.tournamentId = T.tournamentId) AS userCounter FROM Tournament AS T
                  WHERE T.visible = 1 AND T.enddate > (NOW() - INTERVAL 4 DAY) ORDER by T.startdate ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectTournamentList()


    /**
     * Select all old tournament
     * NOW > enddata
     *
     * @return array
     */
    public function selectOldTournamentList(): array
    {
        $query     = "SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate DESC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectOldTournamentList()


    public function selectLatestTournamentList(int $max = 5): array
    {
        $query     = "SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate ASC LIMIT :limit";
        $statement = $this->db->prepare($query);
        $statement->bindParam(':limit', $max);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectLatestTournamentList()


    /**
     * Get all upcoming tournaments
     *
     * @param  integer $max
     * @return array
     */
    public function selectUpcomingTournamentList(int $max = 5): array
    {
        /*
            ?
            SELECT *, (SELECT COUNT(*) FROM ( SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = 1 GROUP BY playerId UNION SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = Tournament.tournamentId and partnerId > 0 GROUP BY partnerId ) AS T ) AS participant
            FROM Tournament
            WHERE visible = 1 AND startdate > NOW()
        ORDER by startdate ASC LIMIT*/
        $query     = "SELECT Tournament.*
                    FROM Tournament
                    WHERE visible = 1 AND startdate > NOW()
                    ORDER by startdate ASC LIMIT :limit";
        $statement = $this->db->prepare($query);
        // (SELECT COUNT(*) FROM TournamentPlayer AS TP WHERE TP.tournamentId = Tournament.tournamentId AND TP.visible = 1  ) AS participant#
        $statement->bindValue(':limit', $max, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();

    }//end selectUpcomingTournamentList()


    public function selectUpcomingTournamentPlayer(int $tournamentId): array
    {
        $query     = "SELECT COUNT(DISTINCT concat(playerId,partnerId)) from TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();
        /*
            SELECT COUNT(*) FROM (
                                      SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = ? AND visible = 1 GROUP BY playerId
                                      UNION
                                      SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = ? AND visible = 1 AND partnerId > 0 GROUP BY partnerId
                                  ) AS result GROUP BY playerId");
        */

        return $statement->fetchAll();
    }//end selectUpcomingTournamentPlayer()


    public function selectUpcomingTournamentPlayerOrginal(int $tournamentId): array
    {
        $query     = "SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1 GROUP BY playerId
                    UNION
                    SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = :tournamentId AND visible = 1 AND partnerId > 0 GROUP BY partnerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectUpcomingTournamentPlayerOrginal()


    /**
     * get Tournament details by Id
     *
     * @param  integer $tournamentId
     * @return array
     */
    public function getTournamentData(int $tournamentId): array
    {
        $query     = "SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName FROM Tournament
                    LEFT JOIN User ON User.userId = Tournament.reporterId
                    WHERE Tournament.tournamentId = :tournamentId LIMIT 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }//end getTournamentData()


    /**
     * Get Players from tournament
     *
     * @param  integer $tournamentId
     * @return array
     */
    public function getPlayersByTournamentId(int $tournamentId): array
    {
        $query     = "SELECT
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
    }//end getPlayersByTournamentId()


    /**
     * Get Players from Tournament for Export
     *
     * @param  integer $tournamentId
     * @return array
     */
    public function getPlayersByTournamentIdToExport(int $tournamentId): array
    {
        $query     = "SELECT
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
    }//end getPlayersByTournamentIdToExport()


    /**
     * Get Disciplines by tournament
     *
     * @param  integer $tournamentId
     * @return array
     */
    public function getDisciplinesByTournamentId(int $tournamentId): array
    {
        $query     = "SELECT * FROM TournamentClass AS TC WHERE TC.tournamentId = :tournamentId AND TC.visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();
       
        return $statement->fetchAll();
    }//end getDisciplinesByTournamentId()


    /**
     * Insert Player by Tournament
     *
     * @param  integer $tournamentId
     * @param  integer $playerId
     * @param  integer $partnerId
     * @param  string  $classification
     * @param  integer $reporterId
     * @return boolean
     */
    public function insertPlayerToTournament(int $tournamentId, int $playerId, int $partnerId, string $classification, int $reporterId): bool
    {
        $query     = "INSERT INTO TournamentPlayer (tournamentId, playerId, partnerId, classification, reporterId)
                    VALUES (:tournamentId, :playerId, :partnerId, :classification, :reporterId)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('partnerId', $partnerId);
        $statement->bindParam('classification', $classification);
        $statement->bindParam('reporterId', $reporterId);

        return $statement->execute();
    }//end insertPlayerToTournament()


    /**
     * inset Tournmanent
     *
     * @param  Tournament $tournament
     * @return boolean
     */
    public function insertTournament(\Badtra\Intranet\Model\Tournament $tournament): bool
    {
        $query     = "INSERT INTO Tournament (name, place, startdate, enddate, deadline, link, classification, additionalClassification, discipline, reporterId, tournamentType, latitude, longitude, description)
                    VALUES (:name, :place, :startdate, :enddate, :deadline, :link, :classification, :additionalClassification, :discipline, :reporterId, :tournamentType, :latitude, :longitude, :description)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('name', $tournament->getName());
        $statement->bindParam('place', $tournament->getPlace());
        $statement->bindParam('startdate', $tournament->getStartdate());
        $statement->bindParam('enddate', $tournament->getEndDate());
        $statement->bindParam('deadline', $tournament->getDeadline());
        $statement->bindParam('link', $tournament->getLink());
        $statement->bindParam('classification', $tournament->getClassification());
        $statement->bindParam('additionalClassification', $tournament->getAdditionalClassification());
        $statement->bindParam('discipline', $tournament->getDiscipline());
        $statement->bindParam('reporterId', $tournament->getReporterId());
        $statement->bindParam('tournamentType', $tournament->getTournamentType());
        $statement->bindParam('latitude', $tournament->getLatitude());
        $statement->bindParam('longitude', $tournament->getLongitude());
        $statement->bindParam('description', $tournament->getDescription());

        return $statement->execute();
    }//end insertTournament()


    /**
     *
     */


    /**
     * Insert class from tournament
     *
     * @param  integer $id
     * @param  string  $name
     * @param  string  $mode
     * @return boolean
     */
    public function insertTournamentClass(int $id, string $name, string $mode): bool
    {
        $query     = "INSERT INTO `TournamentClass` SET (`tournamentId`, `name`, `modus`) VALUES (:id, :name, :mode)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->bindParam('name', $name);
        $statement->bindParam('mode', $mode);

        return $statement->execute();
    }//end insertTournamentClass()


    /**
     * Delete Player from Tournament
     *
     * @param  integer $tournamentId
     * @param  integer $playerId
     * @return boolean
     */
    public function deletePlayersFromTournamentId(int $tournamentId, int $tournamentPlayerId): bool
    {
        $query     = "UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :tournamentPlayerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('tournamentPlayerId', $tournamentPlayerId);

        return $statement->execute();
    }//end deletePlayersFromTournamentId()


    /**
     * Delete all players from Tournament
     *
     * @param  integer $tournamentId
     * @return boolean
     */
    public function deleteAllPlayersFromTournamentById(int $tournamentId): bool
    {
        $query     = "UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }//end deleteAllPlayersFromTournamentById()


    /**
     * Delete class from Tournament
     */
    public function deleteClassFromTournamentById(int $tournamentId): bool
    {
        $query     = "UPDATE TournamentClass set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }//end deleteClassFromTournamentById()


    /**
     * Delete tournament by Id
     *
     * @param  integer $tournamentId
     * @return boolean
     */
    public function deleteTournamentById(int $tournamentId): bool
    {
        $query     = "UPDATE Tournament set visible = 0 WHERE tournamentId = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }//end deleteTournamentById()


    /**
     * Update Tournament by id
     *
     * @param  Tournament $tournament
     * @return boolean
     */
    public function updateTournamentById(\Badtra\Intranet\Model\Tournament $tournament): bool
    {
        $query     = "UPDATE `Tournament`
                    SET `name` = :name, `place`= :place, startdate=:startdate, enddate=:enddate, deadline=:deadline, link=:link,
                    classification = :classification, additionalClassification = :additionalClassification,
                    discipline = :discipline, reporterId = :reporterId, tournamentType = :tournamentType, latitude = :latitude,
                    longitude = :longitude, `description` = :description
                    WHERE `tournamentId` = :tournamentId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournament->getTournamentId());
        $statement->bindParam('name', $tournament->getName());
        $statement->bindParam('place', $tournament->getPlace());
        $statement->bindParam('startdate', $tournament->getStartdate());
        $statement->bindParam('enddate', $tournament->getEnddate());
        $statement->bindParam('deadline', $tournament->getDeadline());
        $statement->bindParam('link', $tournament->getLink());
        $statement->bindParam('classification', $tournament->getClassification());
        $statement->bindParam('additionalClassification', $tournament->getAdditionalClassification());
        $statement->bindParam('discipline', $tournament->getDiscipline());
        $statement->bindParam('reporterId', $tournament->getReporterId());
        $statement->bindParam('tournamentType', $tournament->getTournamentType());
        $statement->bindParam('latitude', $tournament->getLatitude());
        $statement->bindParam('longitude', $tournament->getLongitude());
        $statement->bindParam('description', $tournament->getDescription());

        return $statement->execute();
    }//end updateTournamentById()


    /**
     * Select Tournament by User
     *
     * @param  integer $id
     * @return array
     */
    public function selectGetLatestTournamentFromUserId(int $userId): array
    {
        $query     = "SELECT * FROM Tournament
                    WHERE tournamentId IN (SELECT tournamentId FROM TournamentPlayer Where playerId = :userId or partnerId = :userId
                    GROUP BY tournamentId)
                    ORDER BY enddate DESC LIMIT 10";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }//end selectGetLatestTournamentFromUserId()


    /**
     * get player from Tournament
     *
     * @param  integer $tournamentId
     * @param  integer $playerId
     * @param  integer $partnerId
     * @param  string  $classification
     * @return array
     */
    public function getTournamentPlayerByData(int $tournamentId, int $playerId, int $partnerId, string $classification): array
    {
        $query     = "SELECT * FROM TournamentPlayer
                    WHERE tournamentId = :tournamentId AND playerId = :playerId AND partnerId = :partnerId AND classification = :classification AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('partnerId', $partnerId);
        $statement->bindParam('classification', $classification);
        $statement->execute();

        return $statement->fetchAll();
    }//end getTournamentPlayerByData()


    /**
     * set backup from Tournament
     *
     * @param  integer $tournamentId
     * @param  string  $data
     * @return boolean
     */
    public function insertTournamentBackup(int $tournamentId, string $data): bool
    {
        $query     = "INSERT INTO TournamentBackup (tournamentId, data, date) VALUES (:tournamentId, :data, NOW())";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('data', $data);

        return $statement->execute();
    }//end insertTournamentBackup()


    /**
     * get tournament backup
     *
     * @param  integer $tournamentId
     * @return array
     */
    public function getTournamentBackup(int $tournamentId): array
    {
        $query     = "SELECT * FROM `TournamentBackup` WHERE `tournamentId` = :tournamentId ORDER BY `backupId` DESC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->execute();

        return $statement->fetchAll();
    }//end getTournamentBackup()


    /**
     * get tournament backup diff
     *
     * @param  integer $first
     * @param  integer $second
     * @return array
     */
    public function getTournamentBackupDiff(int $first, int $second): array
    {
        $query     = "SELECT data FROM `TournamentBackup` WHERE `backupId` in (:first,:second) ORDER BY `backupId` DESC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('first', $first);
        $statement->bindParam('second', $second);
        $statement->execute();

        return $statement->fetchAll();
    }//end getTournamentBackupDiff()


    /**
     * Get player from Tournament
     *
     * @param  integer $playerId
     * @return array
     */
    public function getPlayerFromTournamentById(int $playerId): array
    {
        $query     = "SELECT TournamentPlayer.*,
                (SELECT playerNr FROM Player WHERE playerId = TournamentPlayer.playerId ) AS playerNr,
                (SELECT playerNr FROM Player WHERE playerId = TournamentPlayer.partnerId ) AS partnerNr,
                (SELECT playerNr FROM User WHERE userId = TournamentPlayer.reporterId ) AS reporterNr FROM TournamentPlayer
                WHERE tournamentPlayerId = :playerId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerId', $playerId);
        $statement->execute();

        return $statement->fetch();
    }//end getPlayerFromTournamentById()


    /**
     * Unlock player from Torunament
     *
     * @param  integer $tournamentId
     * @param  integer $playerId
     * @return boolean
     */
    public function unlockPlayerFromTournament(int $tournamentId, int $playerId): bool
    {
        $query     = "UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }//end unlockPlayerFromTournament()


    /**
     * Unlock all player from Tournament
     *
     * @param  integer $tournamentId
     * @return boolean
     */
    public function unlockAllPlayerFromTournament(int $tournamentId): bool
    {
        $query     = "UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = :tournamentId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);

        return $statement->execute();
    }//end unlockAllPlayerFromTournament()


    /**
     * Lock player from Tournament
     *
     * @param  integer $tournamentId
     * @param  integer $playerId
     * @return boolean
     */
    public function lockPlayerFromTournament(int $tournamentId, int $playerId): bool
    {
        $query     = "UPDATE TournamentPlayer set locked = 1 WHERE tournamentId = :tournamentId AND tournamentPlayerId = :playerId AND visible = 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('tournamentId', $tournamentId);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }//end lockPlayerFromTournament()
}
