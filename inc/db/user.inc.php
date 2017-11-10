<?php
/**
 * This class implements a user object to simplyfy access to the data base
 * @author philipp
 *
 */
class User {

	// Constants for the User table in the database
	const USER_CLM_ID 		= "userId";
	const USER_CLM_LNAME	= "lastName";
	const USER_CLM_FNAME	= "firstName";
	const USER_CLM_EMAIL 	= "email";
	const USER_CLM_GENDER 	= "gender";
	const USER_CLM_PLAYER 	= "activePlayer";
	const USER_CLM_ADMIN    = "admin";
	const USER_CLM_REPORTER = "reporter";
	const USER_CLM_PASS 	= "password";

	public $userId;
	public $email;
	public $firstName;
	public $lastName;
	public $gender;
	private $isAdmin;
	private $isPlayer;
	private $isReporter;
	public $passHash;

	/**
	 * Conmstructor that knows how to retrieve all fields from a given data set
	 * @param array $dataSet a data set prefrably directly from an SQL statement
	 */
	public function __construct($dataSet = null) {
		if ($dataSet != null) {
			$this->userId 		= intval($dataSet[self::USER_CLM_ID]);
			$this->email	 	= strval($dataSet[self::USER_CLM_EMAIL]);
			$this->firstName 	= strval($dataSet[self::USER_CLM_FNAME]);
			$this->lastName 	= strval($dataSet[self::USER_CLM_LNAME]);
			$this->gender	 	= strval($dataSet[self::USER_CLM_GENDER]);
			$this->isAdmin 		= boolval($dataSet[self::USER_CLM_ADMIN]);
			$this->isPlayer 	= boolval($dataSet[self::USER_CLM_PLAYER]);
			$this->isReporter 	= boolval($dataSet[self::USER_CLM_REPORTER]);
			$this->passHash		= strval($dataSet[self::USER_CLM_PASS]);
		} else {
			$this->userId 		= 0;
			$this->email	 	= "N/A";
			$this->firstName 	= "N/A";
			$this->lastName 	= "N/A";
			$this->gender	 	= "N/A";
			$this->isAdmin 		= false;
			$this->isPlayer 	= false;
			$this->isReporter 	= false;
			$this->passHash		= "N/A";
		}
	}

	/**
	 * Method to retrieve the full name consisting of first and last name
	 * @return string the full name
	 */
	public function getFullName() {
		return $this->firstName . " " . $this->lastName;
	}

	public function getID() {
		return $this->userId;
	}

	public function isAdmin() {
		return $this->isAdmin == true;
	}

	public function isPlayer() {
		return $this->isPlayer == true;
	}

	public function isReporter() {
		return $this->isReporter == true;
	}
}
?>
