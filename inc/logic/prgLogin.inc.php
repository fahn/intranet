<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.	*
 *														*
 * Copyright 2017										*
 *														*
 * All Rights Reserved									*
 *														*
 * Copying, distribution, usage in any form is not 		*
 * allowed without  written permit.						*
 *														*
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)	*
 *														*
 ********************************************************/

include_once '../inc/db/brdb.inc.php';
include_once '../inc/db/user.inc.php';
include_once '../inc/logic/prgPattern.inc.php';

class PrgPatternElementLogin extends APrgPatternElement {
	
	private $brdb;
	private $loggedInUser;
	
	const FORM_LOGIN_EMAIL 		= "formLoginEmail";
	const FORM_LOGIN_PASSWORD 	= "formLoginPass";
	const FORM_LOGIN_ACTION 	= "formLoginAction";
	const FORM_LOGIN_ACTION_LOGIN 	= "Log In1";
	const FORM_LOGIN_ACTION_LOGOUT 	= "Log Out";
	
	// Errors that can be set by methods of this class
	const ERROR_LOGIN_INVALID_EMAIL 			= "Please use a valid email!";
	const ERROR_LOGIN_INVALID_ID 				= "Could not find a registered user!";
	const ERROR_LOGIN_UNKNOWN_EMAIL_PASSWORD 	= "Unknown email and/or incorrect password!";
	const ERROR_LOGIN_NO_SESSION 				= "You are not logged in!";
	const SUCCESS_LOGIN 						= "You are successfully logged in!";
	const SUCCESS_LOGOUT 						= "You are successfully logged out!";
	
	// Wait time after incorrect login to prevent brute force attacks
	const PASSWORD_WAIT_FOR_WRONG 	= 2;
	
	// Constants for the User table in the database
	const SESSION_LOGIN_USER_ID 	= "sessionUserId";
	
	public function __construct($brdb) {
		parent::__construct("login");
		$this->brdb = $brdb;
		$this->registerPostSessionVariable(self::FORM_LOGIN_ACTION);
		$this->registerPostSessionVariable(self::FORM_LOGIN_EMAIL);
	}
	
	public function processPost() {
        #die(eval($_POST) ."--");
        #die(print_r("das ". $this->issetPostVariable(self::FORM_LOGIN_ACTION) ."-". self::FORM_LOGIN_ACTION_LOGIN ."?=". strval(trim($this->getPostVariable(self::FORM_LOGIN_ACTION)))));
		if ($this->issetPostVariable(self::FORM_LOGIN_ACTION)) {
			$loginAction = strval(trim($this->getPostVariable(self::FORM_LOGIN_ACTION)));
			if ($loginAction === self::FORM_LOGIN_ACTION_LOGIN) {
				$this->processPostLogin();
			} else if ($loginAction == self::FORM_LOGIN_ACTION_LOGOUT) {
				$this->processPostLogout();
			}
		}
	}
	
	public function processGet() {
		$this->isUserLoggedIn();
	}
	
	private function processPostLogin() {
		// In case of a post check if the entered information
		// is valid and does not inject wired stuff. (Avoid SQL injection here)
		if (	$this->issetPostVariable(self::FORM_LOGIN_EMAIL) && 
				$this->issetPostVariable(self::FORM_LOGIN_PASSWORD)) {
			$email 		= strval(trim($this->getPostVariable(self::FORM_LOGIN_EMAIL)));
			$pass 		= strval(trim($this->getPostVariable(self::FORM_LOGIN_PASSWORD)));
			$email 		= strtolower($email);
			
			// filter the email to avoid having other wired stuff being
			// injected to the php code or the database maybe
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				// Now see if there is a user in the data base with correct
				// email and hashed password. Passwords are hashed with php hash functionality
				$res = $this->brdb->selectUserByEmail($email);
				if ($this->brdb->hasError()) {
					$this->setFailedMessage($this->brdb->getError());
					return;
				}
				if ($res->num_rows == 1) {
					// fetch the dataset there is only one and try to verify the passowrd
					$dataSet = $res->fetch_assoc();
					$loadedUser = new User($dataSet);
					if (password_verify($pass, $loadedUser->passHash)) {
						$this->setSessionVariable(self::SESSION_LOGIN_USER_ID, intval($dataSet[User::USER_CLM_ID]));
						$this->unsetPostVariable(self::FORM_LOGIN_PASSWORD);
						$this->setSuccessMessage(self::SUCCESS_LOGIN);
						return;
					}
					// Make an potential attacker wait for us
					error_log("BrankDB: Invalid Login Attempt for User: " . $email);
					sleep(self::PASSWORD_WAIT_FOR_WRONG);
				}
			} else {
				$this->setFailedMessage(self::ERROR_LOGIN_INVALID_EMAIL);
			}
			$this->setFailedMessage(self::ERROR_LOGIN_UNKNOWN_EMAIL_PASSWORD);
		}
		return; 
	}
	
	private function processPostLogout() {
		// For a logout we just dump the session and forget about all the
		// things we knew so far such as the correctly logged in user ID
		// and all the other infos
		session_destroy();
		unset($this->loggedInUser);
		$_SESSION = array();
		session_start();
		$this->setSuccessMessage(self::SUCCESS_LOGOUT);
	}
	
	/**
	 * This method checks for correct login. It either checks if there was a post statement.
	 * If there wasn't it tries to check if there is a user set by the session.
	 * @return boolean true in case the current user has a valid login
	 */
	public function isUserLoggedIn() {
		// first unsset the current user to basically clear it
		// and remove all pending informations in case of a logout
		unset($this->loggedInUser);
		
		// If there is no post we directly get here and we try to set the class
		// information directly from the stored information in the session
		if ($this->issetSessionVariable(self::SESSION_LOGIN_USER_ID)) {
			// Try to get the user by the ID stored in the session
			$userId = intval($this->getSessionVariable(self::SESSION_LOGIN_USER_ID));
			$res = $this->brdb->selectUserById($userId);
			if ($this->brdb->hasError()) {
				$this->setFailedMessage($this->brdb->getError());
				return false;
			}
			// if the query was succesful try to use the data to init the User object
			if ($res->num_rows == 1) {
				$dataSet = $res->fetch_assoc();
				$this->loggedInUser = new User($dataSet);
				return true;
			} else {
				$this->setFailedMessage(self::ERROR_LOGIN_INVALID_ID);
				return false;
			}
		}
		
		return false;
	}
	
	public function getLoggedInUser() {
		return $this->loggedInUser;
	}
}
?>