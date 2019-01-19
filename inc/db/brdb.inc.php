<?php

include_once __PFAD__ .'/inc/logic/tools.inc.php';
/**
 * Common methods for accessing the Badminton Ranking
 * Data Base
 * @author philipp
 *
 */
class BrankDB {

  private $db;

  private $error;
  private $hasError;

  // Tools
  private $tools;

  private $ini;

  private $statementSelectUserByEmail;
  private $statementSelectUserById;
  private $statementSelectAllUser;
  private $statementSelectAllUserPagination;
  private $statementSelectAllUserAndSort;
  private $statementSelectAllClubs;
  private $statementSelectAllClubsPN;
  private $statementSelectAllPlayer;
  private $statementSelectAllPlayerByOurClub;
  private $statementSelectAllGames;
  private $statementSelectGameById;
  private $statementSelectPlayerIdByName;
  private $statementInsertUser;
  private $statementDeleteUser;
  private $statementDeleteGame;
  private $statementUpdateUser;
  private $statementUpdateUserPassword;
  private $statementUpdateAdminUser;
  private $statementInsertGame;
  private $statementInsertUserEasyProcess;

  // tournament
  private $statementTournamentList;
  private $statementOldTournamentList;
  private $statementTournamentById;
  private $statementPlayersByTournamentId;
  private $statementPlayersByTournamentIdToExport;
  private $statementDisciplinesByTournamentId;
  private $statementLatestGamesByPlayerId;
  private $statementGetActiveAndReporterOrAdminPlayer;
  private $statementGetClubById;
  private $statementDeleteClassFromTournament;
  private $statementAllDeletePlayerFromTournament;
  private $statementDeleteTournamentById;
  private $statementUpdateTournamentById;
  private $statementInsertTournamentBackup;

  // insert
  private $statementInsertPlayerToTournament;
  private $statementInsertTournament;
  private $statementInsertClassTournament;
  private $statementInsertClub;

  // delete
  private $statementDeletePlayerFromTournament;

  // clubs
  private $statementUpdateClubById;
  private $statementGetLatestTournamentFromUserId;

  // user Password token
  private $statementInsertUserPassHash;
  private $statementGetUserPassHash;
  private $statementDeletePassHash;

  private $statementGetPlayerFromTournamentById;
  private $statementGetTournamentPlayerByData;

  private $statementGetLatestAutoIncrement;

  // SETTINGS
  private $statementSelectSettings;



  // API
  private $APIstatementGetTournamentFromToday;


  // Team
  private $statementGetStaff;

  /**
   * Constructor tha already prepares all needed dp commands
   */
  public function prepareCommands() {
    $this->statementInsertUserEasyProcess   = $this->db->prepare("INSERT INTO User (firstName) VALUES ('automatisch')");

    $this->statementSelectUserByEmail       = $this->db->prepare("SELECT * FROM User WHERE email = ?");
    $this->statementSelectUserById          = $this->db->prepare("SELECT * FROM User WHERE userId = ?");
    $this->statementSelectAllUser           = $this->db->prepare("SELECT * FROM User");
    $this->statementSelectAllUserPagination = $this->db->prepare("SELECT User.*, Club.name as clubName FROM User LEFT JOIN Club ON Club.clubId = User.ClubId ORDER BY User.lastName LIMIT ?,?");
    $this->statementSelectAllUserAndSort    = $this->db->prepare("SELECT * FROM User ORDER BY ? ?");
    $this->statementInsertUser              = $this->db->prepare("INSERT INTO User (email, firstName, lastName, gender, phone, bday, playerId, clubId, activePlayer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $this->statementDeleteUser              = $this->db->prepare("Update User set email = '', password = '', reporter = 0, admin = 0 WHERE userId = ?");
    $this->statementUpdateUser              = $this->db->prepare("Update User set email = ?, firstName = ?, lastName = ?, gender = ?, phone = ?, bday = ? WHERE userId = ?"); //$this->db->prepare("CALL UpdateUser(?, ?, ?, ?, NULL, ?, NULL, NULL, NULL)");
    $this->statementUpdateUserPassword      = $this->db->prepare("UPDATE User set password = ? WHERE userId = ?");
    $this->statementUpdateAdminUser         = $this->db->prepare("UPDATE User set email=?, firstName=?, lastName=?, gender=?, phone=?, bday = ?, playerId = ?, clubId = ?, activePlayer = ?, reporter = ?, admin = ? WHERE UserId = ?");
    $this->statementSelectAllPlayer         = $this->db->prepare("SELECT userId, fullName FROM UserActivePlayer ORDER BY fullName ASC");
    $this->statementSelectAllPlayerByOurClub= $this->db->prepare("SELECT userId, CONCAT_WS(' ', firstName, lastName) as fullName FROM User WHERE clubId = 1 ORDER BY fullName ASC");
    $this->statementInsertGame              = $this->db->prepare("CALL InsertGame(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $this->statementDeleteGame              = $this->db->prepare("CALL DeleteGame(?);");
    $this->statementSelectAllGames          = $this->db->prepare("SELECT * FROM GameOverviewList;");
    $this->statementSelectGameById          = $this->db->prepare("SELECT * FROM GameOverviewList WHERE matchId = ?;");
    $this->statementSelectPlayerIdByName    = $this->db->prepare("SELECT userId FROM User WHERE LOWER(_UserFullName(firstName, lastName)) = LOWER(?)");

    // Tournament
    $this->statementTournamentList          = $this->db->prepare("SELECT T.*, (SELECT count(*) from TournamentPlayer AS TP where TP.visible = 1 AND TP.tournamentID = T.tournamentID) AS userCounter FROM Tournament AS T WHERE T.visible = 1 AND T.enddate > NOW() - INTERVAL 4 DAY ORDER by T.startdate ASC");
    $this->statementTournamentListMax       = $this->db->prepare("SELECT * FROM Tournament WHERE visible = 1 AND enddate > NOW() ORDER by startdate ASC LIMIT ?");
    $this->statementOldTournamentList       = $this->db->prepare("SELECT * FROM Tournament WHERE visible = 1 AND enddate < NOW() ORDER by startdate ASC");
    $this->statementTournamentById          = $this->db->prepare("SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName FROM Tournament LEFT JOIN User ON User.userId = Tournament.reporterId WHERE Tournament.tournamentID = ?");
    $this->statementPlayersByTournamentId   = $this->db->prepare(
      "SELECT
      TP.*,
      CONCAT(User.firstName, ' ', User.lastName) AS playerName,
      CONCAT(Partner.firstName, ' ',Partner.lastName) as partnerName,
      CONCAT(Reporter.firstName, ' ',Reporter.lastName) as reporterName
      FROM TournamentPlayer AS TP
      LEFT JOIN User ON User.userid = TP.playerID
      LEFT JOIN User as Partner ON TP.partnerID = Partner.userId
      LEFT JOIN User AS Reporter ON TP.reporterID = Reporter.userId
      WHERE TP.tournamentID = ? AND TP.visible = 1"
    );

    $this->statementPlayersByTournamentIdToExport = $this->db->prepare(
      "SELECT
        TP.*,
        User.firstName AS p1FirstName, User.lastName AS p1LastName, User.gender AS p1Gender, User.bday AS p1Bday, User.playerId AS p1PlayerNumber, C1.name as p1ClubName, C1.clubNumber AS p1ClubNumber, C1.association AS p1ClubAssociation,
        Partner.firstName AS p2FirstName, Partner.lastName AS p2LastName, Partner.gender AS p2Gender, Partner.bday AS p2Bday, Partner.playerId AS p2PlayerNumber, C2.name as p2ClubName, C2.clubNumber AS p2ClubNumber, C2.association AS p2ClubAssociation
      FROM TournamentPlayer AS TP
      LEFT JOIN User ON User.userid = TP.playerID
      LEFT JOIN User as Partner ON TP.partnerID = Partner.userId
      LEFT JOIN Club AS C1 ON C1.clubID = User.clubId
      LEFT JOIN Club AS C2 ON C2.clubID = Partner.clubId
      WHERE TP.tournamentID = ? AND TP.visible = 1"
    );

    $this->statementInsertTournamentBackup = $this->db->prepare("INSERT INTO TournamentBackup (TournamentId, data, date) VALUES (?, ?, NOW())");

    $this->statementGetTournamentBackup = $this->db->prepare("SELECT * FROM TournamentBackup WHERE tournamentId = ? ORDER BY backupId DESC");
    $this->statementGetTournamentBackupDiff = $this->db->prepare("SELECT data FROM TournamentBackup WHERE backupId in (?,?) ORDER BY backupId DESC");

    $this->statementDisciplinesByTournamentId         = $this->db->prepare("SELECT * FROM TournamentClass AS TC WHERE TC.tournamentID = ? AND TC.visible = 1");
    $this->statementInsertPlayerToTournament          = $this->db->prepare("INSERT INTO TournamentPlayer set tournamentID = ?, playerID = ?, partnerID = ?, classification = ?, reporterID = ?, fillingDate = NOW()");
    $this->statementInsertTournament                  = $this->db->prepare("INSERT INTO Tournament (name, place, startdate, enddate, deadline, link, classification, additionalClassification, discipline, reporterId, tournamentType, latitude, longitude, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $this->statementLatestGamesByPlayerId             = $this->db->prepare("SELECT * FROM GameOverviewList WHERE playerA1 = (SELECT CONCAT_WS(' ', firstName, lastName) FROM User WHERE UserId = ?) OR playerB1 = (SELECT CONCAT_WS(' ', firstName, lastName) FROM User WHERE UserId = ?) ORDER BY datetime DESC LIMIT 5");
    $this->statementGetActiveAndReporterOrAdminPlayer = $this->db->prepare("SELECT * FROM User WHERE activePlayer = 1 AND (admin = 1 OR reporter = 1) ORDER BY lastName ASC");
    $this->statementInsertClassTournament             = $this->db->prepare("INSERT INTO TournamentClass set tournamentID = ?, name = ?, modus = ?");
    $this->statementDeletePlayerFromTournament        = $this->db->prepare("UPDATE TournamentPlayer set visible = 0 WHERE tournamentID = ? AND tournamentPlayerId = ?");

    $this->statementDeleteClassFromTournament         = $this->db->prepare("UPDATE TournamentClass set visible = 0 WHERE tournamentID = ?");
    $this->statementAllDeletePlayerFromTournament     = $this->db->prepare("UPDATE TournamentPlayer set visible = 0 WHERE tournamentID = ?");
    $this->statementDeleteTournamentById              = $this->db->prepare("UPDATE Tournament set visible = 0 WHERE tournamentID = ?");

    $this->statementUpdateTournamentById              = $this->db->prepare("UPDATE Tournament set name = ?, place= ?, startdate=?, enddate=?, deadline=?, link=?, classification = ?, additionalClassification = ?, discipline = ?, reporterId = ?, tournamentType = ?, latitude = ?, longitude = ?, description = ? WHERE tournamentID = ?");

    // Club
    $this->statementGetClubById      = $this->db->prepare("SELECT * FROM Club WHERE clubId = ? LIMIT 1");
    $this->statementSelectAllClubs   = $this->db->prepare("SELECT * FROM Club ORDER by sort, name ASC ");
    $this->statementSelectAllClubsPN = $this->db->prepare("SELECT * FROM Club ORDER by sort, name LIMIT ?,?");
    $this->statementInsertClub       = $this->db->prepare("INSERT INTO Club (name, clubNumber, association) VALUES (?, ?, ?)");
    $this->statementUpdateClubById   = $this->db->prepare("UPDATE Club set name = ?, clubNumber = ?, association = ? WHERE clubId = ?");

        // Tournament
    $this->statementGetLatestTournamentFromUserId = $this->db->prepare("SELECT * FROM Tournament WHERE tournamentID IN (SELECT tournamentID FROM TournamentPlayer Where playerID = ? or partnerID = ? GROUP BY tournamentID) ORDER BY enddate DESC LIMIT 10");

    // Password Hashes
    $this->statementInsertUserPassHash = $this->db->prepare("INSERT INTO UserPassHash (userId, token, ip) VALUES (?, ?, ?)");
    $this->statementGetUserPassHash    = $this->db->prepare("SELECT * FROM User LEFT JOIN UserPassHash AS PASS ON PASS.userId = User.userId WHERE User.email = ? AND PASS.token = ? AND PASS.valid = 1 AND PASS.createDate > NOW()-86440");
    $this->statementDeletePassHash     = $this->db->prepare("UPDATE UserPassHash set valid = 0 WHERE userId = ? AND token = ?");

    $this->statementGetPlayerFromTournamentById = $this->db->prepare("SELECT * FROM TournamentPlayer WHERE TournamentPlayerId = ?");


    $this->statementGetTournamentPlayerByData = $this->db->prepare("SELECT * FROM TournamentPlayer WHERE tournamentID = ? AND playerID = ? AND partnerID = ? AND classification = ? AND visible = 1");

    $this->statementGetLatestAutoIncrement = $this->db->prepare("SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'BRDB' AND   TABLE_NAME = ? ");

    // API
    $this->APIstatementGetTournamentFromToday = $this->db->prepare("SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName, User.email FROM Tournament LEFT JOIN User ON User.userId = Tournament.reporterId WHERE Tournament.reporterId != '' AND Tournament.visible = 1 AND Tournament.deadline = CURDATE() ");


    $this->statementSelectSettings = $this->db->prepare("SELECT * FROM Settings");


    // TEAM
    $this->statementGetStaff = $this->db->prepare("SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name, User.image, User.gender FROM UserStaff AS US LEFT JOIN User ON User.userId = US.userId ORDER BY US.row ASC, US.sort ASC, User.lastName ASC");
  }

    public function __construct() {
      $this->tools = new Tools();
      $this->ini = $this->tools->getIni();

    }

  /**
   * Destructor that closes the DB connection
   */
  public function __destruct() {
    $this->db->close();
  }


  public function loadSettings() {
    return $this->executeStatement($this->statementSelectSettings);
  }

  /**
   * Call this method to check if an error occurred
   * @return boolean true in case there is an error pending
   */
  public function hasError() {
    return $this->hasError;
  }

  public function insert_id() {
    return $this->db->insert_id;
  }

  /**
   * Call this method to get the error.
   * this method will also reset the current error state.
   * the message will be kept.
   * @return unknown
   */
  public function getError() {
    $this->hasError = false;
    return $this->error;
  }

  /**
   * Internal method to set an error
   * @param string $error the error to be set
   */
  private function setError($error) {
    $this->hasError = true;
    $this->error = $error;
  }

  /**
   * Common method to connect to the BRDB
   */
  public function connectAndSelectDB() {
    if ($this->db = new mysqli($this->ini['db_host'], $this->ini['db_user'], $this->ini['db_pass'], $this->ini['db_name'])) {
      return true;
    } else {
      $this->setError($this->db->connect_error);
      return false;
    }
  }

  /**
   * Internal method that executes a prepared statement.
   * Automatically sets the error state in case things go wrong.
   * @param mysqli_stmt $statement the prepared and bound statement to be executed
   * @return mysqli_result the result of the executed statement
   */
  public function executeStatement($statement) {
    if (!$statement->execute()) {
        $this->setError($statement->error);
    }
    return $statement->get_result();
  }

  /**
   * Call this method to hand back the User with the given email from the data base
   * @param String $email the email to look for as string
   * @return mysqli_result
   */
  public function selectUserByEmail($email) {
    $this->statementSelectUserByEmail->bind_param("s", $email);
    return $this->executeStatement($this->statementSelectUserByEmail);
  }

  /**
   * Get a user from the data base by a given Id
   * @param integer $userId The user ID as integer
   * @return mysqli_result the user from the database as SQL Result
   */
  public function selectUserById($userId) {
    $this->statementSelectUserById->bind_param("i", $userId);
    return $this->executeStatement($this->statementSelectUserById);
  }

  /**
   * This method deletes a User from the DB with the given userId
   * @param integer $userId the id of the user to be deleted
   * @return mysqli_result result of the sql execution
   */
  public function deleteUserById($userId) {
    $this->statementDeleteUser->bind_param("i", $userId);
    return $this->executeStatement($this->statementDeleteUser);
  }

  /**
   * Get all users from the DB
   * @return mysqli_result all users from the database as SQL Result
   */
   public function selectAllUser() {
     return $this->executeStatement($this->statementSelectAllUser);
   }

   public function selectAllUserSortBy($sort, $asc = 'ASC') {
     $this->statementSelectAllUserAndSort->bind_param("ss", $sort, $asc);
     return $this->executeStatement($this->statementSelectAllUserAndSort);
   }

  public function selectAllUserPagination($min = 0, $max = 50) {
    $this->statementSelectAllUserPagination->bind_param("ii", $min, $max);
    return $this->executeStatement($this->statementSelectAllUserPagination);
  }

  public function selectAllClubs($min = 0, $max = 0) {
    if($min != $max) {
      $this->statementSelectAllClubsPN->bind_param("ii", $min, $max);
      return $this->executeStatement($this->statementSelectAllClubsPN);
    } else {
      return $this->executeStatement($this->statementSelectAllClubs);
    }

  }


  public function GetActiveAndReporterOrAdminPlayer() {
    return $this->executeStatement($this->statementGetActiveAndReporterOrAdminPlayer);
  }

  /**
   * Call this method to register a new user
   * @param unknown $email the email of the new user
   * @param unknown $fname the first name of the new user
   * @param unknown $lname the last name of the new user
   * @param unknown $pass the password to be used as sha256 hash
   * @return mysqli_result
   */
  public function registerUser($email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $clubId) {
    $this->statementInsertUser->bind_param("ssssssss", $email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $clubId);
    return $this->executeStatement($this->statementInsertUser);
  }

  /**
   * Call this method to update a user
   * @param unknown $userId the id of the user to be updated
   * @param unknown $email the email to be set
   * @param unknown $fname the first name to be set
   * @param unknown $lName the last name to be set
   * @param unknown $phone the phone to be set
   * @return mysqli_result result of the statement execution
   */
  public function updateUser($userId, $email, $fname, $lName, $gender, $phone, $bday) {
    $this->statementUpdateUser->bind_param("ssssssi", $email, $fname, $lName, $gender, $phone, $bday, $userId);
    return $this->executeStatement($this->statementUpdateUser);
  }

  public function updateUserPassword($userId, $pass) {
    $this->statementUpdateUserPassword->bind_param("si", $pass, $userId);
    return $this->executeStatement($this->statementUpdateUserPassword);
  }

  /**
   * Call this method to update a user
   * @param unknown $userId the id of the user to be updated
   * @param unknown $email the email of the user to be set
   * @param unknown $fname the first name of the user
   * @param unknown $lName the last name of the user
   * @param unknown $gender the gender, set to either "Male" or "Female"
   * @param unknown $pass password to be set or null in case the password should not be updated
   * @param unknown $isAdmin set to 1 if the suer is an admin 0 if not
   * @param unknown $isPlayer set to 1 if the user is an actoive palyer 0 if not
   * @param unknown $isReporter set to 1 if the user is a reporter or 0 if not
   * @return mysqli_result the result of the executed maysql statement
   */
  public function updateAdminUser($userId, $email, $fname, $lName, $gender, $phone, $bday, $playerId, $clubId, $isPlayer, $isReporter, $isAdmin) {
    $this->statementUpdateAdminUser->bind_param("sssssssiiiii", $email, $fname, $lName, $gender, $phone, $bday, $playerId, $clubId,  $isPlayer, $isReporter, $isAdmin, $userId);
    return $this->executeStatement($this->statementUpdateAdminUser);
  }

  /**
   * This method hands back all active players from the DB
   * @return mysqli_result the result of the mysql statement
   */
   public function selectAllPlayer() {
     return $this->executeStatement($this->statementSelectAllPlayer);
   }

   public function selectAllPlayerByOurClub() {
     return $this->executeStatement($this->statementSelectAllPlayerByOurClub);
   }

  /**
   * Insert or update a game in the DB
   * @param unknown $matchId The match Id put 0 for a new one
   * @param unknown $datetime The Date time as mysql timestampe
   * @param unknown $playerA1Id The Id of the first palyer of team a
   * @param unknown $playerB1d The id of the first player of team b
   * @param unknown $playerA2Id The id of the second plyer of team a
   * @param unknown $playerB2Id the id of the second player of team b
   * @param unknown $setA1 First set points for team a
   * @param unknown $setB1 First set points for team b
   * @param unknown $setA2 second set points for team a
   * @param unknown $setB2 second set points for team b
   * @param unknown $setA3 third set points for team a
   * @param unknown $setB3 third set points for team b
   * @param unknown $winner the winner of this match either as "Side A" or "Side B"
   * @return mysqli_result result of the mysql statement that got executed
   */
  public function insertGame($matchId, $datetime, $playerA1Id, $playerB1Id, $playerA2Id, $playerB2Id, $setA1, $setB1, $setA2, $setB2, $setA3, $setB3, $winner) {
    $this->statementInsertGame->bind_param("isiiiiiiiiiis",
        $matchId, $datetime,
        $playerA1Id, $playerB1Id,
        $playerA2Id, $playerB2Id,
        $setA1, $setB1,
        $setA2, $setB2,
        $setA3, $setB3,
        $winner);
                #die($this->statementInsertGame);
    return $this->executeStatement($this->statementInsertGame);
  }

  public function getGameById($id) {
    $this->statementGetGameById->bind_param("i", $id);
    return $this->executeStatement($this->statementGetGameById);
  }

  public function updateGameById($id) {

  }

  /**
   * Method to delete a game by a given ID
   * @param int $matchId The match ID of the game to be deleted
   * @return mysqli_result
   */
  public function deleteGame($matchId) {
    $this->statementDeleteGame->bind_param("i", $matchId);
    return $this->executeStatement($this->statementDeleteGame);
  }

  /**
   * This method hands back the results from the given view
   * @param String $userStatsViewName The view from which to get the results
   * @param String $userStatsViewColumn the column which should be used for sorting
   * @param Boolean $isAscending true in case it should be sorted Ascending. False means Descending.
   * @return mysqli_result
   */
  public function getUserStats($userStatsViewName, $userStatsViewColumn, $isAscending) {
    $sqlResultViewQueryStatementFormat = "SELECT stats.* FROM (SELECT * FROM %s) AS stats ORDER BY %s %s";
    if ($isAscending) {
      $sqlResultViewQuery = sprintf($sqlResultViewQueryStatementFormat, $userStatsViewName, $userStatsViewColumn, "ASC");
    } else {
      $sqlResultViewQuery = sprintf($sqlResultViewQueryStatementFormat, $userStatsViewName, $userStatsViewColumn, "DESC");
    }

    $this->statementGetUserStats = $this->db->prepare($sqlResultViewQuery);
    return $this->executeStatement($this->statementGetUserStats);
  }

  /**
   * This method hands back a user by a given full name
   * @param String $fullUserName the user to be handed back by the DB
   * @return mysqli_result
   */
  public function getUserIdByFullName($fullUserName) {
    $this->statementSelectPlayerIdByName->bind_param("s",$fullUserName);
    return $this->executeStatement($this->statementSelectPlayerIdByName);
  }

  /**
   * This method returns all games from the data base
   * @return mysqli_result mysql statement result
   */
  public function selectAllGames() {
    return $this->executeStatement($this->statementSelectAllGames);
  }

  /**
   * This method hands back the game by the given ID
   * @param unknown $matchId the id of the game which to hand back
   * @return mysqli_result
   */
  public function selectGameById($matchId) {
    $this->statementSelectGameById->bind_param("i",$matchId);
    return $this->executeStatement($this->statementSelectGameById);
  }


  public function selectTournamentList() {
    return $this->executeStatement($this->statementTournamentList);
  }

  public function selectOldTournamentList() {
    return $this->executeStatement($this->statementOldTournamentList);
  }

  public function selectTournamentListMax($id) {
    $this->statementTournamentListMax->bind_param("i", $id);
    return $this->executeStatement($this->statementTournamentListMax);
  }

  public function selectLatestGamesByPlayerId($id) {
    $this->statementLatestGamesByPlayerId->bind_param("ii", $id, $id);
    return $this->executeStatement($this->statementLatestGamesByPlayerId);
  }



  public function getTournamentData($tournamentID) {
    $this->statementTournamentById->bind_param("i",$tournamentID);
    return $this->executeStatement($this->statementTournamentById);
  }

  public function getPlayersByTournamentId($tournamentID) {
    $this->statementPlayersByTournamentId->bind_param("i", $tournamentID);
    return $this->executeStatement($this->statementPlayersByTournamentId);
  }

  public function getPlayersByTournamentIdToExport($tournamentID) {
    $this->statementPlayersByTournamentIdToExport->bind_param("i", $tournamentID);
    return $this->executeStatement($this->statementPlayersByTournamentIdToExport);
  }

  public function getDisciplinesByTournamentId($tournamentID) {
    $this->statementDisciplinesByTournamentId->bind_param("i", $tournamentID);
    return $this->executeStatement($this->statementDisciplinesByTournamentId);
  }

  public function insetPlayerToTournament($data) {
    $this->statementInsetPlayerToTournament->bind_param("iiiii", $data['playerID'], $data['partnerID'], $id['tournamentID'], $data['classID'], $data['reporterID'] );
    return $this->executeStatement($this->statementInsetPlayerToTournament);
  }

  public function selectGetClubById($id) {
    $this->statementGetClubById->bind_param("i", $id);
    return $this->executeStatement($this->statementGetClubById);
  }


  public function insertPlayerToTournament($tournamentId, $playerId, $partnerId, $classification, $reporterId) {
    $this->statementInsertPlayerToTournament->bind_param("iiisi", $tournamentId, $playerId, $partnerId, $classification, $reporterId);
    return $this->executeStatement($this->statementInsertPlayerToTournament);
  }


  public function insertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description) {
    $this->statementInsertTournament->bind_param("sssssssssissss", $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description);
    return $this->executeStatement($this->statementInsertTournament);
  }

  public function insertTournamentClass($id, $name, $mode) {
    $this->statementInsertClassTournament->bind_param("iss", $id, $name, $mode);
    return $this->executeStatement($this->statementInsertClassTournament);
  }

  public function deletePlayersFromTournamentId($tournamentId, $playerId) {
    $this->statementDeletePlayerFromTournament->bind_param("ii", $tournamentId, $playerId);
    return $this->executeStatement($this->statementDeletePlayerFromTournament);
  }

  public function deleteAllPlayersFromTournamentById($tournamentId) {
    $this->statementAllDeletePlayerFromTournament->bind_param("i", $tournamentId);
    return $this->executeStatement($this->statementAllDeletePlayerFromTournament);
  }

  public function deleteClassFromTournamentById($tournamentId) {
    $this->statementDeleteClassFromTournament->bind_param("i", $tournamentId);
    return $this->executeStatement($this->statementDeleteClassFromTournament);
  }

  public function deleteTournamentById($tournamentId) {
    $this->statementDeleteTournamentById->bind_param("i", $tournamentId);
    return $this->executeStatement($this->statementDeleteTournamentById);
  }

  public function updateTournamentById($tournamentId, $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description) {
    $this->statementUpdateTournamentById->bind_param("ssssssssssssssi", $name, $place, $startdate, $enddate, $deadline, $link, $classification, $additionalClassification, $discipline, $reporterId, $tournamentType, $latitude, $longitude, $description, $tournamentId);
    return $this->executeStatement($this->statementUpdateTournamentById);
  }



  // CLUB
  public function insertClub($name, $number, $association) {
    $this->statementInsertClub->bind_param("sss", $name, $number, $association);
    return $this->executeStatement($this->statementInsertClub);
  }

  public function updateClubById($id, $name, $number, $association) {
    $this->statementUpdateClubById->bind_param("sssi", $name, $number, $association, $id);
    return $this->executeStatement($this->statementUpdateClubById);
  }

  public function selectGetLatestTournamentFromUserId($id) {
    $this->statementGetLatestTournamentFromUserId->bind_param("ii", $id, $id);
    return $this->executeStatement($this->statementGetLatestTournamentFromUserId);
  }


  // User Password
  public function insertUserPassHash($id, $token, $ip) {
    $this->statementInsertUserPassHash->bind_param("iss", $id, $token, $ip);
    return $this->executeStatement($this->statementInsertUserPassHash);
  }

  public function GetUserPassHash($mail, $token) {
    $this->statementGetUserPassHash->bind_param("ss", $mail, $token);
    return $this->executeStatement($this->statementGetUserPassHash);
  }

  public function DeleteUserPassHash($userid, $token) {
    $this->statementDeletePassHash->bind_param("ss", $userid, $token);
    return $this->executeStatement($this->statementDeletePassHash);
  }

  public function getPlayerFromTournamentById($playerId) {
    $this->statementGetPlayerFromTournamentById->bind_param("i", $playerId);
    return $this->executeStatement($this->statementGetPlayerFromTournamentById);
  }

  public function getTournamentPlayerByData($tournamentId, $player, $partner, $disziplin) {
    $this->statementGetTournamentPlayerByData->bind_param("iiis", $tournamentId, $player, $partner, $disziplin);
    return $this->executeStatement($this->statementGetTournamentPlayerByData);
  }

  public function insertUserEasyProcess() {
    return $this->executeStatement($this->statementInsertUserEasyProcess);
  }

  // API
  public function APIGetTournamentFromToday() {
    return $this->executeStatement($this->APIstatementGetTournamentFromToday);
  }


  public function getLatestAutoIncrement($table) {
    $this->statementGetLatestAutoIncrement->bind_param("s", $table);
    return $this->executeStatement($this->statementGetLatestAutoIncrement);
  }


    /*
      GET TEAM list
    */
    public function selectStaffList() {
        return $this->executeStatement($this->statementGetStaff);
    }


    public function insertTournamentBackup($id, $data) {
      $this->statementInsertTournamentBackup->bind_param("is", $id, $data);
      return $this->executeStatement($this->statementInsertTournamentBackup);
    }

    public function getTournamentBackup($id) {
      $this->statementGetTournamentBackup->bind_param("i", $id);
      return $this->executeStatement($this->statementGetTournamentBackup);
    }

    public function getTournamentBackupDiff($first, $second){
      $this->statementGetTournamentBackupDiff->bind_param("ii", $first, $second);
      return $this->executeStatement($this->statementGetTournamentBackupDiff);
    }





}

?>
