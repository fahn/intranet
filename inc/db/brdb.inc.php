<?php

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

    private $ini;

	private $statementSelectUserByEmail;
	private $statementSelectUserById;
	private $statementSelectAllUser;
	private $statementSelectAllPlayer;
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

	// tournament
	private $statementTournamentList;
	private $statementTournamentById;
	private $statementPlayersByTournamentId;
	private $statementDisciplinesByTournamentId;
  private $statementLatestGamesByPlayerId;
	private $statementGetActiveAndReporterOrAdminPlayer;
	private $statementGetClubById;

	// insert
	private $statementInsertPlayerToTournament;
	private $statementInsertTournament;
	private $statementInsertClassTournament;

	// delete
	private $statementDeletePlayerFromTournament;

	/**
	 * Constructor tha already prepares all needed dp commands
	 */
	public function prepareCommands() {
		$this->statementSelectUserByEmail
			= $this->db->prepare("SELECT * FROM User WHERE email = ?");
		$this->statementSelectUserById
			= $this->db->prepare("SELECT * FROM User WHERE userId = ?");
		$this->statementSelectAllUser
			= $this->db->prepare("SELECT * FROM User");
		$this->statementInsertUser
			= $this->db->prepare("CALL InsertUser(?, ?, ?, ?, ?, TRUE, FALSE, FALSE)");
		$this->statementDeleteUser
			= $this->db->prepare("CALL DeleteUser(?)");
		$this->statementUpdateUser
			= $this->db->prepare("Update User set email = ?, firstName = ?, lastName = ?, phone = ?, bday = ? WHERE userId = ?"); //$this->db->prepare("CALL UpdateUser(?, ?, ?, ?, NULL, ?, NULL, NULL, NULL)");

			$this->statementUpdateUserPassword
				= $this->db->prepare("UPDATE User set password = ? WHERE userId = ?");
		$this->statementUpdateAdminUser
			= $this->db->prepare("CALL UpdateUser(?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$this->statementSelectAllPlayer
			= $this->db->prepare("SELECT userId, fullName FROM UserActivePlayer ORDER BY fullName ASC");
		$this->statementInsertGame
			= $this->db->prepare("CALL InsertGame(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$this->statementDeleteGame
			= $this->db->prepare("CALL DeleteGame(?);");
		$this->statementSelectAllGames
			= $this->db->prepare("SELECT * FROM GameOverviewList;");
		$this->statementSelectGameById = $this->db->prepare("SELECT * FROM GameOverviewList WHERE matchId = ?;");
		$this->statementSelectPlayerIdByName = $this->db->prepare("SELECT userId FROM User WHERE LOWER(_UserFullName(firstName, lastName)) = LOWER(?)");

		$this->statementTournamentList = $this->db->prepare("SELECT * FROM Tournament WHERE enddate > NOW() - INTERVAL 4 DAY ORDER by startdate ASC");
    $this->statementTournamentListMax = $this->db->prepare("SELECT * FROM Tournament WHERE enddate > NOW() ORDER by startdate ASC LIMIT ?");
		$this->statementTournamentById = $this->db->prepare("SELECT * FROM Tournament WHERE tournamentID = ?");
		$this->statementPlayersByTournamentId = $this->db->prepare(
			"SELECT TP.*, TC.name AS disciplineName, TC.modus AS disciplineModus, CONCAT(User.firstName, ' ', User.lastName) AS playerName, CONCAT(Partner.firstName, ' ',Partner.lastName) as partnerName
			FROM TournamentPlayer AS TP
			LEFT JOIN User ON User.userid = TP.playerID
			LEFT JOIN TournamentClass AS TC ON TC.classID = TP.classID
			LEFT JOIN User as Partner ON TP.partnerID = Partner.userId
			WHERE TP.tournamentID = ?"
		);

		$this->statementDisciplinesByTournamentId = $this->db->prepare("SELECT * FROM TournamentClass AS TC WHERE TC.tournamentID = ?;");
		$this->statementInsertPlayerToTournament = $this->db->prepare("INSERT INTO TournamentPlayer set tournamentID = ?, playerID = ?, partnerID = ?, classID = ?, reporterID = ?, fillingDate = NOW()");

		$this->statementInsertTournament = $this->db->prepare("INSERT INTO Tournament set name = ?, place = ?, startdate = ?, enddate = ?, deadline = ?, link = ?");


    $this->statementLatestGamesByPlayerId = $this->db->prepare("SELECT * FROM GameOverviewList ORDER BY datetime DESC LIMIT 5;");

		$this->statementGetActiveAndReporterOrAdminPlayer = $this->db->prepare("SELECT * FROM User WHERE activePlayer = 1 AND (admin = 1 OR reporter = 1) ORDER BY lastName ASC");

		$this->statementGetClubById = $this->db->prepare("SELECT * FROM Club WHERE clubId = ? LIMIT 1");

		$this->statementInsertClassTournament = $this->db->prepare("INSERT INTO TournamentClass set tournamentID = ?, name = ?, modus = ?");

		$this->statementDeletePlayerFromTournament = $this->db->prepare("UPDATE TournamentPlayer set visible = 0 WHERE tournamentID = ? AND tournamentPlayerId = ?");


	}

    public function __construct() {
        $this->ini = parse_ini_file('../inc/config.ini');
    }

	/**
	 * Destructor that closes the DB connection
	 */
	public function __destruct() {
		$this->db->close();
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
            #die(print_r($statement));
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
	public function registerUser($email, $fname, $lname, $gender, $pass) {
		$this->statementInsertUser->bind_param("sssss", $email, $fname, $lname, $gender, $pass);
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
	public function updateUser($userId, $email, $fname, $lName, $phone, $bday) {
		$this->statementUpdateUser->bind_param("sssssi", $email, $fname, $lName, $phone, $bday, $userId);
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
	public function updateAdminUser($userId, $email, $fname, $lName, $gender, $pass, $isAdmin, $isPlayer, $isReporter) {
		$this->statementUpdateAdminUser->bind_param("isssssiii", $userId, $email, $fname, $lName, $gender, $pass, $isAdmin, $isPlayer, $isReporter);
		return $this->executeStatement($this->statementUpdateAdminUser);
	}

	/**
	 * This method hands back all active players from the DB
	 * @return mysqli_result the result of the mysql statement
	 */
	public function selectAllPlayer() {
		return $this->executeStatement($this->statementSelectAllPlayer);
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

  public function selectTournamentListMax($id) {
    $this->statementTournamentListMax->bind_param("i", $id);
		return $this->executeStatement($this->statementTournamentListMax);
	}

  public function selectLatestGamesByPlayerId($id) {
    #$this->statementLatestGamesByPlayerId->bind_param("i", $id);
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


	public function insertPlayerToTournament($tournamentId, $playerId, $partnerId, $classId, $reporterId) {
		$this->statementInsertPlayerToTournament->bind_param("iisii", $tournamentId, $playerId, $partnerId, $classId, $reporterId);
		return $this->executeStatement($this->statementInsertPlayerToTournament);
	}

	public function insertTournament($name, $place, $startdate, $endddate, $deadline, $link) {
		$this->statementInsertTournament->bind_param("ssssss", $name, $place, $startdate, $endddate, $deadline, $link);
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

}

?>
