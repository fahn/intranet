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

define("rn","\r\n");

/**
 * Anti SQL Injection method. All use rinput should pass this method
 * @param String $data the inupt data as String
 * @return string the returned and filtered string
 */
class Tools {
	public static function escapeInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	const PHP_SELF = "PHP_SELF";
}

?>