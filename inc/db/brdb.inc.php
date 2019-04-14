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

require_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

class BrankDB {

  private $db;

  private $error;
  private $hasError;



  public function __construct() {
      // load connection
      $this->connection();

  }

  private function connection() {
    if( $this->db == NULL ){
      try {
        $tools = new Tools();

        $this->db = new mysqli(
          $tools->getIniValue('db_host'),
          $tools->getIniValue('db_user'),
          $tools->getIniValue('db_pass'),
          $tools->getIniValue('db_name')
        );
        $this->db->set_charset("utf8mb4");

        return $this->db;

      } catch (Exception $e) {
        $this->setError($e);
        return NULL;
      }
    }
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
    $this->error    = $error;
  }


  public function insert_id() {
    return $this->db->insert_id;
  }


  /**
   * Internal method that executes a prepared statement.
   * Automatically sets the error state in case things go wrong.
   * @param mysqli_stmt $statement the prepared and bound statement to be executed
   * @return mysqli_result the result of the executed statement
   */
  public function executeStatement($statement) {
    if ( ! $statement->execute() ) {
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
    $cmd = $this->db->prepare("SELECT * FROM User WHERE email = ?");
    $cmd->bind_param("s", $email);

    return $this->executeStatement($cmd);
  }

  /**
   * Get a user from the data base by a given Id
   * @param integer $userId The user ID as integer
   * @return mysqli_result the user from the database as SQL Result
   */
  public function selectUserById($userId) {
    $cmd = $this->db->prepare("SELECT * FROM User WHERE userId = ?");
    $cmd->bind_param("i", $userId);

    return $this->executeStatement($cmd);
  }

  /**
   * This method deletes a User from the DB with the given userId
   * @param integer $userId the id of the user to be deleted
   * @return mysqli_result result of the sql execution
   */
  public function deleteUserById($userId) {
    $cmd = $this->db->prepare("Update User set email = '', password = '', reporter = 0, admin = 0 WHERE userId = ?");
    $cmd->bind_param("i", $userId);

    return $this->executeStatement($cmd);
  }

  /**
   * Get all users from the DB
   * @return mysqli_result all users from the database as SQL Result
   */
   public function selectAllUser() {
     $cmd = $this->db->prepare("SELECT * FROM User");

     return $this->executeStatement($cmd);
   }

   public function selectAllUserSortBy($sort, $asc = 'ASC') {
     $cmd = $this->db->prepare("SELECT * FROM User ORDER BY ? ?");
     $cmd->bind_param("ss", $sort, $asc);

     return $this->executeStatement($cmd);
   }

  public function selectAllUserPagination($min = 0, $max = 50) {
    $cmd = $this->db->prepare("SELECT User.*, Club.name as clubName FROM User LEFT JOIN Club ON Club.clubId = User.ClubId ORDER BY User.lastName LIMIT ?,?");
    $cmd->bind_param("ii", $min, $max);

    return $this->executeStatement($cmd);
  }

  public function GetActiveAndReporterOrAdminPlayer() {
    $cmd = $this->db->prepare("SELECT * FROM User WHERE activePlayer = 1 AND (admin = 1 OR reporter = 1) ORDER BY lastName ASC");

    return $this->executeStatement($cmd);
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
    $cmd = $this->db->prepare("INSERT INTO User (email, firstName, lastName, gender, phone, bday, playerId, clubId, activePlayer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $cmd->bind_param("ssssssss", $email, $firstName, $lastName, $gender, $phone, $bday, $playerId, $clubId);

    return $this->executeStatement($cmd);
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
    $cmd = $this->db->prepare("Update User set email = ?, firstName = ?, lastName = ?, gender = ?, phone = ?, bday = ? WHERE userId = ?"); //$this->db->prepare("CALL UpdateUser(?, ?, ?, ?, NULL, ?, NULL, NULL, NULL)");
    $cmd->bind_param("ssssssi", $email, $fname, $lName, $gender, $phone, $bday, $userId);

    return $this->executeStatement($cmd);
  }

  public function updateUserPassword($userId, $pass) {
    $cmd = $this->db->prepare("UPDATE User set password = ? WHERE userId = ?");
    $cmd->bind_param("si", $pass, $userId);

    return $this->executeStatement($cmd);
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
    $cmd = $this->db->prepare("UPDATE User set email=?, firstName=?, lastName=?, gender=?, phone=?, bday = ?, playerId = ?, clubId = ?, activePlayer = ?, reporter = ?, admin = ? WHERE UserId = ?");
    $cmd->bind_param("sssssssiiiii", $email, $fname, $lName, $gender, $phone, $bday, $playerId, $clubId,  $isPlayer, $isReporter, $isAdmin, $userId);

    return $this->executeStatement($cmd);
  }

  public function selectNextBirthdays() {
      $cmd = $this->db->prepare("SELECT userId, CONCAT_WS(' ', firstName, lastName) AS userName, bday
                                 FROM User
                                 WHERE DATE_ADD(bday, INTERVAL YEAR(CURDATE())-YEAR(bday) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(bday),1,0) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                                 ORDER BY DAY(bday) ASC");

      return $this->executeStatement($cmd);
  }

  /**
   * This method hands back all active players from the DB
   * @return mysqli_result the result of the mysql statement
   */
   public function selectAllPlayer() {
     $cmd = $this->db->prepare("SELECT userId, fullName FROM UserActivePlayer ORDER BY fullName ASC");

     return $this->executeStatement($cmd);
   }

   public function selectAllPlayerByOurClub($clubId = 1) {
     $cmd = $this->db->prepare("SELECT userId, CONCAT_WS(' ', firstName, lastName) as fullName FROM User WHERE clubId = ? ORDER BY fullName ASC");
     $cmd->bind_param("i", $clubId);

     return $this->executeStatement($cmd);
   }

   // User Password
   public function insertUserPassHash($id, $token, $ip) {
     $cmd = $this->db->prepare("INSERT INTO UserPassHash (userId, token, ip) VALUES (?, ?, ?)");
     $cmd->bind_param("iss", $id, $token, $ip);

     return $this->executeStatement($cmd);
   }

   public function GetUserPassHash($mail, $token) {
     $cmd = $this->db->prepare("SELECT * FROM User
         LEFT JOIN UserPassHash AS PASS ON PASS.userId = User.userId
         WHERE User.email = ? AND PASS.token = ? AND PASS.valid = 1 AND PASS.createDate > NOW()-86440");
     $cmd->bind_param("ss", $mail, $token);

     return $this->executeStatement($cmd);
   }

   public function DeleteUserPassHash($userid, $token) {
     $cmd = $this->db->prepare("UPDATE UserPassHash set valid = 0 WHERE userId = ? AND token = ?");
     $cmd->bind_param("ss", $userid, $token);

     return $this->executeStatement($cmd);
   }


  /**
   * This method hands back a user by a given full name
   * @param String $fullUserName the user to be handed back by the DB
   * @return mysqli_result
   */
  public function getUserIdByFullName($fullUserName) {
    $cmd = $this->db->prepare("SELECT userId FROM User WHERE LOWER(_UserFullName(firstName, lastName)) = LOWER(?)");
    $cmd->bind_param("s",$fullUserName);

    return $this->executeStatement($cmd);
  }

  /*******************************************************************************
                               TOURNAMENT
  *******************************************************************************/
  public function selectTournamentList() {
    $cmd = $this->db->prepare("SELECT T.*, (SELECT count(*) from TournamentPlayer AS TP where TP.visible = 1 AND TP.tournamentId = T.tournamentId) AS userCounter FROM Tournament AS T WHERE T.visible = 1 AND T.enddate > NOW() - INTERVAL 4 DAY ORDER by T.startdate ASC");
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
    $cmd = $this->db->prepare("SELECT * FROM Tournament WHERE visible = 1 AND startdate > NOW() ORDER by startdate ASC LIMIT ?");
    $cmd->bind_param("i", $max);

    return $this->executeStatement($cmd);
  }


  public function getTournamentData($tournamentId) {
    $cmd = $this->db->prepare("SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName FROM Tournament LEFT JOIN User ON User.userId = Tournament.reporterId WHERE Tournament.tournamentId = ?");
    $cmd->bind_param("i",$tournamentId);
    return $this->executeStatement($cmd);
  }

  public function getPlayersByTournamentId($tournamentId) {
    $cmd = $this->db->prepare(
      "SELECT
      TP.*,
      CONCAT(User.firstName, ' ', User.lastName) AS playerName,
      CONCAT(Partner.firstName, ' ',Partner.lastName) as partnerName,
      CONCAT(Reporter.firstName, ' ',Reporter.lastName) as reporterName
      FROM TournamentPlayer AS TP
      LEFT JOIN User ON User.userid = TP.playerId
      LEFT JOIN User as Partner ON TP.partnerId = Partner.userId
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
        User.firstName AS p1FirstName, User.lastName AS p1LastName, User.gender AS p1Gender, User.bday AS p1Bday, User.playerId AS p1PlayerNumber, C1.name as p1ClubName, C1.clubNumber AS p1ClubNumber, C1.association AS p1ClubAssociation,
        Partner.firstName AS p2FirstName, Partner.lastName AS p2LastName, Partner.gender AS p2Gender, Partner.bday AS p2Bday, Partner.playerId AS p2PlayerNumber, C2.name as p2ClubName, C2.clubNumber AS p2ClubNumber, C2.association AS p2ClubAssociation
      FROM TournamentPlayer AS TP
      LEFT JOIN User ON User.userid = TP.playerId
      LEFT JOIN User as Partner ON TP.partnerId = Partner.userId
      LEFT JOIN Club AS C1 ON C1.clubID = User.clubId
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

  public function insetPlayerToTournament($data) {
    $this->statementInsetPlayerToTournament->bind_param("iiiii", $data['playerId'], $data['partnerId'], $id['tournamentId'], $data['classId'], $data['reporterId'] );
    return $this->executeStatement($this->statementInsetPlayerToTournament);
  }

  public function insertPlayerToTournament($tournamentId, $playerId, $partnerId, $classification, $reporterId) {
    $cmd = $this->db->prepare("INSERT INTO TournamentPlayer set tournamentId = ?, playerId = ?, partnerId = ?, classification = ?, reporterId = ?, fillingDate = NOW()");
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
    public function getPlayerByTerm($term) {
        #$term = sprintf("%%s%", $term);
        $term = "%".$term."%";
        $cmd = $this->db->prepare("SELECT User.firstName, User.lastName, User.playerId, User.clubId, Club.name AS clubName FROM User
                                   LEFT JOIN Club ON Club.clubId = User.clubId
                                   WHERE CONCAT_WS(User.firstName, User.lastName) LIKE ?
                                   ORDER BY User.lastName");

        $cmd->bind_param("s", $term);

        return $this->executeStatement($cmd);
    }

  /** Insert class from Tournament
    *
    */
  public function insertTournamentClass($id, $name, $mode) {
    $cmd = $this->db->prepare("INSERT INTO TournamentClass set tournamentId = ?, name = ?, modus = ?");
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
    $cmd = $this->db->prepare("SELECT * FROM TournamentPlayer WHERE tournamentId = ? AND playerId = ? AND partnerId = ? AND classification = ? AND visible = 1");
    $cmd->bind_param("iiis", $tournamentId, $player, $partner, $disziplin);

    return $this->executeStatement($cmd);
  }

  public function insertTournamentBackup($id, $data) {
    $cmd = $this->db->prepare("INSERT INTO TournamentBackup (TournamentId, data, date) VALUES (?, ?, NOW())");
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
    $cmd = $this->db->prepare("SELECT * FROM TournamentPlayer WHERE TournamentPlayerId = ?");
    $cmd->bind_param("i", $playerId);

    return $this->executeStatement($cmd);
  }
/*******************************************************************************
                             CLUB
*******************************************************************************/
    public function selectGetClubById($id) {
        $cmd = $this->db->prepare("SELECT * FROM Club WHERE clubId = ? LIMIT 1");
        $cmd->bind_param("i", $id);
        return $this->executeStatement($cmd);
    }

  public function insertClub($name, $number, $association) {
    $cmd = $this->db->prepare("INSERT INTO Club (name, clubNumber, association) VALUES (?, ?, ?)");
    $cmd->bind_param("sss", $name, $number, $association);

    return $this->executeStatement($cmd);
  }

  public function updateClubById($id, $name, $number, $association) {
    $cmd = $this->db->prepare("UPDATE Club set name = ?, clubNumber = ?, association = ? WHERE clubId = ?");
    $cmd->bind_param("sssi", $name, $number, $association, $id);

    return $this->executeStatement($cmd);
  }

  public function selectAllClubs($min = 0, $max = 0) {
    if($min != $max) {
        $cmd = $this->db->prepare("SELECT * FROM Club ORDER by sort, name LIMIT ?,?");
        $cmd->bind_param("ii", $min, $max);
    } else {
        $cmd = $this->db->prepare("SELECT * FROM Club ORDER by sort, name ASC ");
    }

    return $this->executeStatement($cmd);
  }

  /*******************************************************************************
                               STAFF
  *******************************************************************************/

  public function selectStaffList() {
      $cmd = $this->db->prepare("SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name, User.image, User.gender FROM UserStaff AS US
                                 LEFT JOIN User ON User.userId = US.userId
                                 ORDER BY US.row ASC, US.sort ASC, User.lastName ASC");

      return $this->executeStatement($cmd);
  }

/*******************************************************************************
                             API
*******************************************************************************/
    public function APIGetTournamentFromToday() {
        $cmd = $this->db->prepare("SELECT Tournament.*, CONCAT_WS(' ', User.firstName, User.lastName) AS reporterName, User.email FROM Tournament
                                   LEFT JOIN User ON User.userId = Tournament.reporterId
                                   WHERE Tournament.reporterId != '' AND Tournament.visible = 1 AND Tournament.deadline = CURDATE() ");

        return $this->executeStatement($cmd);
    }

    public function APIGetTournamentList() {
        $cmd = $this->db->prepare("SELECT Tournament.* FROM Tournament");

        return $this->executeStatement($cmd);
    }

    public function APIinsertTournament($name, $place, $startdate, $enddate, $deadline, $link, $classification, $tournamentType, $description, $latitude, $longitude) {
        $cmd = $this->db->prepare("INSERT INTO Tournament (name, place, startdate, enddate, deadline, link, classification, tournamentType, description, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $cmd->bind_param("sssssssssss", $name, $place, $startdate, $enddate, $deadline, $link, $classification, $tournamentType, $description, $latitude, $longitude);

        return $this->executeStatement($cmd);
    }

   /*******************************************************************************
                                ELOranking
   *******************************************************************************/
   public function statementGetRanking() {
      $cmd = $this->db->prepare("SELECT elo.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name FROM `EloRanking` AS elo
                                 LEFT JOIN User ON User.userId = elo.userId
                                 #WHERE elo.win != 0 OR elo.loss != 0
                                 ORDER BY elo.points DESC");

      return $this->executeStatement($cmd);
   }

   /**
    *
    */
    public function statementGetMatches() {
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
   public function statementGetMatchesGroupedByDate() {
     $cmd = $this->db->prepare("SELECT DATE(time) AS gamedate, COUNT(gameId) AS games
                                FROM `eloGames`
                                WHERE hidden = '0'
                                GROUP BY gamedate");

     return $this->executeStatement($cmd);
   }

   /**
    * get Game by ID
    */
    public function statementGetGameById($id) {
        $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', player.firstName, player.lastName) playerName, CONCAT_WS(' ', opponent.firstName, opponent.lastName) opponentName FROM `eloGames` AS games
                                   LEFT JOIN User AS player ON player.userId = games.playerId
                                   LEFT JOIN User AS opponent ON opponent.userId = games.opponentId
                                   WHERE games.gameId = ?");
        $cmd->bind_param("i", $id);
        return $this->executeStatement($cmd);
    }

   public function deleteRanking() {
     $cmd = $this->db->prepare("TRUNCATE `eloRanking`");

     return $this->executeStatement($cmd);
   }

   public function insertMatch($a, $b, $sets, $winner) {
     $cmd = $this->db->prepare("INSERT INTO `eloGames` (playerId, opponentId, sets, winner) VALUES (?, ?, ?, ?)");
     $cmd->bind_param("iisi", $a, $b, $sets, $winner);

     return $this->executeStatement($cmd);
   }

   public function selectPoints($userId) {
      $cmd = $this->db->prepare("SELECT IFNULL( (SELECT points FROM `eloRanking` WHERE userId = ?) , '0')");
      $cmd->bind_param("i", $userId);

      return $this->executeStatement($cmd);
   }

   public function updatePoints($userId, $points, $win, $loss) {
      $cmd = $this->db->prepare("INSERT INTO `eloRanking` (userId, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE userId= ?, points= ?, win=win+?, loss=loss+?, lastMatch = NOW() ");
      $cmd->bind_param("iiiiii", $userId, $points, $userId, $points, $win, $loss);

      return $this->executeStatement($cmd);
   }


   public function selectLatestRankingGamesByPlayerId($id) {
     $cmd = $this->db->prepare("SELECT games.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name FROM eloGames AS games
                                LEFT JOIN User ON User.userId = games.opponentId
                                WHERE (games.playerId = ? OR games.opponentId = ?) AND games.hidden = 0
                                ORDER BY time DESC LIMIT 5");
     $cmd->bind_param("ii", $id, $id);

     return $this->executeStatement($cmd);
   }

    /** delete game by ID
     *
     */
    public function deleteMatch($id) {
        $cmd = $this->db->prepare("UPDATE `eloGames` SET hidden='1' WHERE gameId = ?");
        $cmd->bind_param("i", $id);

        return  $this->executeStatement($cmd);
    }

    /*******************************************************************************
                                 FAQ
    *******************************************************************************/
    public function statementGetAllFaq() {
        $cmd = $this->db->prepare("SELECT Faq.*, Cat.title as categoryTitle, Cat.categoryId FROM `Faq`
                                   LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = Faq.categoryId");

        return  $this->executeStatement($cmd);
    }

    public function statementGetFAQById($id) {
        $cmd = $this->db->prepare("SELECT * FROM `Faq` WHERE faqId = ?");
        $cmd->bind_param("i", $id);

        return  $this->executeStatement($cmd);
    }

    public function insertFaq($title, $categoryId, $text) {
        $cmd = $this->db->prepare("INSERT INTO `Faq` (title, categoryId, text) VALUES (?, ?, ?)");
        $cmd->bind_param("sis", $title, $categoryId, $text);

        return  $this->executeStatement($cmd);
    }

    public function updateFaqById($id, $title, $categoryId, $text) {
        $cmd = $this->db->prepare("UPDATE `Faq` set title = ?, categoryId = ?, text = ? WHERE faqId = ?");
        $cmd->bind_param("sisi", $title, $categoryId, $text, $id);


        return  $this->executeStatement($cmd);
    }

    public function deleteFaq($id) {
        $cmd = $this->db->prepare("DELETE FROM `Faq` WHERE faqId = ?");
        $cmd->bind_param("i", $id);

        return  $this->executeStatement($cmd);
    }

    /*******************************************************************************
                                 News
    *******************************************************************************/
    public function statementGetAllNews() {
        $cmd = $this->db->prepare("SELECT News.*, Cat.title AS categoryTitle, CONCAT_WS(' ', User.firstName, User.lastName) as userName FROM `News`
                                   LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                                   LEFT JOIN `User` ON User.userId = News.userId");

        return  $this->executeStatement($cmd);
    }

    public function selectLatestNews($max=5) {
        $cmd = $this->db->prepare("SELECT News.*, Cat.title as categoryTitle, Cat.categoryId FROM `News`
                                   LEFT JOIN `Category` AS `Cat` ON Cat.categoryId = News.categoryId
                                   LIMIT ?");
        $cmd->bind_param("i", $max);

        return  $this->executeStatement($cmd);
    }



    public function statementGetNewsById($id) {
        $cmd = $this->db->prepare("SELECT * FROM `News` WHERE newsId = ?");
        $cmd->bind_param("i", $id);

        return  $this->executeStatement($cmd);
    }

    public function insertNews($title, $categoryId, $text) {
        $cmd = $this->db->prepare("INSERT INTO `News` (title, categoryId, text) VALUES (?, ?, ?)");
        $cmd->bind_param("sis", $title, $categoryId, $text);

        return  $this->executeStatement($cmd);
    }

    public function updateNewsById($id, $title, $categoryId, $text) {
        $cmd = $this->db->prepare("UPDATE `News` set title = ?, categoryId = ?, text = ? WHERE newsId = ?");
        $cmd->bind_param("sisi", $title, $categoryId, $text, $id);


        return  $this->executeStatement($cmd);
    }

    public function deleteNews($id) {
        $cmd = $this->db->prepare("DELETE FROM `News` WHERE newsId = ?");
        $cmd->bind_param("i", $id);

        return  $this->executeStatement($cmd);
    }



    /*******************************************************************************
                                 Categories
    *******************************************************************************/

    public function statementGetAllCategories() {
        $cmd = $this->db->prepare("SELECT * FROM `Category` ORDER BY pid, title");
        return  $this->executeStatement($cmd);
    }

    public function adminStatementGetAllCategories() {
        $cmd = $this->db->prepare("SELECT Category.*, C1.title AS pidName FROM `Category`
            LEFT JOIN `Category` AS C1 ON C1.categoryId = Category.pid
            ORDER BY Category.pid, Category.title");
        return  $this->executeStatement($cmd);
    }

    public function insertCategory($title, $pid) {
        $cmd = $this->db->prepare("INSERT INTO `Category` (pid, title) VALUES (?, ?)");
        $cmd->bind_param("is", $pid, $title);

        return  $this->executeStatement($cmd);
    }



    /*******************************************************************************
                                 NOTIFICATION
    *******************************************************************************/
    public function statementGetNotifationByUserId($userId) {
        $cmd = $this->db->prepare("SELECT * FROM `Notification` WHERE userId = ?");
        $cmd->bind_param("i", $userId);

        return  $this->executeStatement($cmd);
    }

    public function statementGetNonReadNotifationByUserId($userId) {
        $cmd = $this->db->prepare("SELECT * FROM `Notification` WHERE userId = ? and isRead = 0");
        $cmd->bind_param("i", $userId);

        return  $this->executeStatement($cmd);
    }

    /**
     * read Message by UserId & notificationId
     */
    public function statementReadMessageById($id, $userId) {
        $cmd = $this->db->prepare("UPDATE `Notification` set isRead=1 WHERE id = ? AND userId = ?");
        $cmd->bind_param("ii", $id, $userId);

        return  $this->executeStatement($cmd);
    }

    /**
     *  read all messages by User
     */
    public function statementAllReadMessageByUserId($userId) {
        $cmd = $this->db->prepare("UPDATE `Notification` set isRead=1 WHERE userId = ?");
        $cmd->bind_param("i", $userId);

        return  $this->executeStatement($cmd);
    }


 /*******************************************************************************
                              SETTING @TODO
 *******************************************************************************/
    /**
     * loadSettings @TODO: upcoming feature
     */
    public function loadSettings() {
        $cmd = $this->db->prepare("SELECT * FROM Settings");

        return $this->executeStatement($cmd);
    }


}

?>
