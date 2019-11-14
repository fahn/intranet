<?php

trait TournamentDB {


    public function selectTournamentList() {
        $sql = "SELECT T.*, (SELECT count(*) from TournamentPlayer AS TP where TP.visible = 1 AND TP.tournamentId = T.tournamentId) AS userCounter FROM Tournament AS T
      WHERE T.visible = 1 AND T.enddate > NOW() - INTERVAL 4 DAY ORDER by T.startdate ASC";
        $cmd = $this->db->prepare($sql);

        return $this->executeStatement($cmd);
    }

    public function selectOldTournamentList() {
        $cmd = $this->db->prepare("SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate DESC");

        return $this->executeStatement($cmd);
    }

    public function selectLatestTournamentList($max = 5) {
        $cmd = $this->db->prepare("SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate ASC LIMIT ?");
        $cmd->bind_param("i", $max);

        return $this->executeStatement($cmd);
    }

    public function selectUpcomingTournamentList($max = 5) {
        /*  ?
                                   SELECT *, (SELECT COUNT(*) FROM ( SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = 1 GROUP BY playerId UNION SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = Tournament.tournamentId and partnerId > 0 GROUP BY partnerId ) AS T ) AS participant
                                                              FROM Tournament
                                                              WHERE visible = 1 AND startdate > NOW()
                                                              ORDER by startdate ASC LIMIT*/
        $cmd = $this->db->prepare("SELECT *, (SELECT COUNT(*) FROM TournamentPlayer AS TP WHERE TP.tournamentId = Tournament.tournamentId AND TP.visible = 1  ) AS participant
                                   FROM Tournament
                                   WHERE visible = 1 AND startdate > NOW()
                                   ORDER by startdate ASC LIMIT ?");
        $cmd->bind_param("i", $max);

        return $this->executeStatement($cmd);
    }

    public function selectUpcomingTournamentPlayer($tournamentId) {
        $cmd = $this->db->prepare("SELECT DISTINCT playerId FROM TournamentPlayer WHERE tournamentId = ? AND visible = 1 GROUP BY playerId
                                   UNION
                                   SELECT DISTINCT partnerId FROM TournamentPlayer WHERE tournamentId = ? AND visible = 1 AND partnerId > 0 GROUP BY partnerId");
        $cmd->bind_param("ii", $tournamentId, $tournamentId);

        return $this->executeStatement($cmd);
    }


    public function getTournamentData($tournamentId) {
        $sql = "SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName FROM Tournament
              LEFT JOIN User ON User.userId = Tournament.reporterId
              WHERE Tournament.tournamentId = ?";
        $cmd = $this->db->prepare($sql);
        $cmd->bind_param("i",$tournamentId);

        return $this->executeStatement($cmd);
    }

    public function getPlayersByTournamentId($tournamentId) {
        $cmd = $this->db->prepare(
            "SELECT
      TP.*,
      CONCAT_WS(' ', Player.firstName, Player.lastName) AS playerName,
      CONCAT_WS(' ', Partner.firstName, Partner.lastName) as partnerName,
      CONCAT_WS(' ', Reporter.firstName, Reporter.lastName) as reporterName
      FROM TournamentPlayer AS TP
      LEFT JOIN Player ON TP.playerId = Player.playerId
      LEFT JOIN Player as Partner ON TP.partnerId = Partner.playerId
      LEFT JOIN User AS Reporter ON TP.reporterId = Reporter.userId
      WHERE TP.tournamentId = ? AND TP.visible = 1"
            );
        $cmd->bind_param("i", $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function getPlayersByTournamentIdToExport($tournamentId) {
        $cmd = $this->db->prepare(
            "SELECT
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
              WHERE TP.tournamentId = ? AND TP.visible = 1"
            );
        $cmd->bind_param("i", $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function getDisciplinesByTournamentId($tournamentId) {
        $cmd =  $this->db->prepare("SELECT * FROM TournamentClass AS TC WHERE TC.tournamentId = ? AND TC.visible = 1");
        $cmd->bind_param("i", $tournamentId);
        return $this->executeStatement($cmd);
    }

    public function insertPlayerToTournament($tournamentId, $playerId, $partnerId, $classification, $reporterId) {
        $cmd = $this->db->prepare("INSERT INTO TournamentPlayer (tournamentId, playerId, partnerId, classification, reporterId) VALUES (?, ?, ?, ?, ?)");
        $cmd->bind_param("iiisi", $tournamentId, $playerId, $partnerId, $classification, $reporterId);

        return $this->executeStatement($cmd);
    }


    public function insertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description) {
        $cmd = $this->db->prepare("INSERT INTO Tournament (name, place, startdate, enddate, deadline, link, classification, additionalClassification, discipline, reporterId, tournamentType, latitude, longitude, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $cmd->bind_param("sssssssssissss", $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description);

        return $this->executeStatement($cmd);
    }

    /**
     *
     */


    /** Insert class from Tournament
     *
     */
    public function insertTournamentClass($id, $name, $mode) {
        $cmd = $this->db->prepare("INSERT INTO TournamentClass set (tournamentId, name, modus) VALUES (?, ?, ?)");
        $cmd->bind_param("iss", $id, $name, $mode);

        return $this->executeStatement($cmd);
    }

    public function deletePlayersFromTournamentId($tournamentId, $playerId) {
        $cmd = $this->db->prepare("UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = ? AND tournamentPlayerId = ?");
        $cmd->bind_param("ii", $tournamentId, $playerId);

        return $this->executeStatement($cmd);
    }

    public function deleteAllPlayersFromTournamentById($tournamentId) {
        $cmd = $this->db->prepare("UPDATE TournamentPlayer set visible = 0 WHERE tournamentId = ?");
        $cmd->bind_param("i", $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function deleteClassFromTournamentById($tournamentId) {
        $cmd = $this->db->prepare("UPDATE TournamentClass set visible = 0 WHERE tournamentId = ?");
        $cmd->bind_param("i", $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function deleteTournamentById($tournamentId) {
        $cmd = $this->db->prepare("UPDATE Tournament set visible = 0 WHERE tournamentId = ?");
        $cmd->bind_param("i", $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function updateTournamentById($tournamentId, $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description) {
        $cmd = $this->db->prepare("UPDATE Tournament set name = ?, place= ?, startdate=?, enddate=?, deadline=?, link=?, classification = ?, additionalClassification = ?, discipline = ?, reporterId = ?, tournamentType = ?, latitude = ?, longitude = ?, description = ? WHERE tournamentId = ?");
        $cmd->bind_param("ssssssssssssssi", $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description, $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function selectGetLatestTournamentFromUserId($id) {
        $cmd = $this->db->prepare("SELECT * FROM Tournament WHERE tournamentId IN (SELECT tournamentId FROM TournamentPlayer Where playerId = ? or partnerId = ? GROUP BY tournamentId) ORDER BY enddate DESC LIMIT 10");
        $cmd->bind_param("ii", $id, $id);

        return $this->executeStatement($cmd);
    }

    public function getTournamentPlayerByData($tournamentId, $player, $partner, $disziplin) {
        $cmd = $this->db->prepare("SELECT * FROM TournamentPlayer
        WHERE tournamentId = ? AND playerId = ? AND partnerId = ? AND classification = ? AND visible = 1");
        $cmd->bind_param("iiis", $tournamentId, $player, $partner, $disziplin);

        return $this->executeStatement($cmd);
    }

    public function insertTournamentBackup($id, $data) {
        $cmd = $this->db->prepare("INSERT INTO TournamentBackup (tournamentId, data, date) VALUES (?, ?, NOW())");
        $cmd->bind_param("is", $id, $data);

        return $this->executeStatement($cmd);
    }

    public function getTournamentBackup($id) {
        $cmd = $this->db->prepare("SELECT * FROM TournamentBackup WHERE tournamentId = ? ORDER BY backupId DESC");
        $cmd->bind_param("i", $id);

        return $this->executeStatement($cmd);
    }

    public function getTournamentBackupDiff($first, $second){
        $cmd = $this->db->prepare("SELECT data FROM TournamentBackup WHERE backupId in (?,?) ORDER BY backupId DESC");
        $cmd->bind_param("ii", $first, $second);

        return $this->executeStatement($cmd);
    }

    public function getPlayerFromTournamentById($playerId) {
        $cmd = $this->db->prepare("SELECT * FROM TournamentPlayer WHERE tournamentPlayerId = ?");
        $cmd->bind_param("i", $playerId);

        return $this->executeStatement($cmd);
    }

    public function unlockPlayerFromTournament($tournamentId, $playerId) {
        $cmd = $this->db->prepare("UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = ? AND tournamentPlayerId = ? AND visible = 1");
        $cmd->bind_param("ii", $tournamentId, $playerId);

        return $this->executeStatement($cmd);
    }

    public function unlockAllPlayerFromTournament($tournamentId) {
        $cmd = $this->db->prepare("UPDATE TournamentPlayer set locked = 0 WHERE tournamentId = ? AND visible = 1");
        $cmd->bind_param("i", $tournamentId);

        return $this->executeStatement($cmd);
    }

    public function lockPlayerFromTournament($tournamentId, $playerId) {
        $cmd = $this->db->prepare("UPDATE TournamentPlayer set locked = 1 WHERE tournamentId = ? AND tournamentPlayerId = ? AND visible = 1");
        $cmd->bind_param("ii", $tournamentId, $playerId);

        return $this->executeStatement($cmd);
    }
}

?>
