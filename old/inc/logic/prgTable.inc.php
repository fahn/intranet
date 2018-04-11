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

/**
 * This pattern handles the interaction with one of the result tables.
 * This pattern can register the columns to be displayed and it is possible to
 * decide which column of the tables should be used for sorting and if it is
 * ascending or descending order
 * @author philipp
 *
 */
abstract class APrgSqlTablePattern extends APrgPatternElement {
	
	private $brdb;
	private $columnArray;
	private $viewName;
	
	protected $queryResult;
	
	const PRG_VARIABLE_NAME_COLUMN = "column";
	const PRG_VARIABLE_NAME_ORDER = "order";
	const PRG_VARIABLE_VALUE_ORDER_ASC = "ASC";
	const PRG_VARIABLE_VALUE_ORDER_DESC = "DESC";
	
	public function __construct($viewName, BrankDB $brdb) {
		parent::__construct($viewName);
		$this->brdb = $brdb;
		$this->columnArray = array();
		$this->viewName = $viewName;
	}
	
	public function processPost() {
	}
	
	public function buildLinkVariables($sortColumn, $sortOrder) {
		$prefixedVariableNameColumn = $this->getPrefixedName(self::PRG_VARIABLE_NAME_COLUMN);
		$prefixedVariableNameOrder = $this->getPrefixedName(self::PRG_VARIABLE_NAME_ORDER);
		
		$linkVariables = $prefixedVariableNameColumn . "=" . $sortColumn . "&" . $prefixedVariableNameOrder . "=" . $sortOrder;
		return $linkVariables;
	}
	
	public function processGet() {
		// Clear the query rsult
		unset($this->queryResult);
		
		// Get the column to be sorted and the direction from the get statement
		// store the results in the session
		$prefixedVariableNameColumn = $this->getPrefixedName(self::PRG_VARIABLE_NAME_COLUMN);
		$prefixedVariableNameOrder = $this->getPrefixedName(self::PRG_VARIABLE_NAME_ORDER);
		if (isset($_GET[$prefixedVariableNameColumn])) {
			$_SESSION[$prefixedVariableNameColumn] = $_GET[$prefixedVariableNameColumn];
		}
		if (isset($_GET[$prefixedVariableNameOrder])) {
			$_SESSION[$prefixedVariableNameOrder] = $_GET[$prefixedVariableNameOrder];
		}
		
		// Already query the view for results
		$this->queryResultView();
	} 
	
	/**
	 * Call this method to issue the query to the data base.
	 * this emthod first checks the settings from the GET call and builds up
	 * the correct sql statement from it. If entries are incorrect the query will fall
	 * back to question the first row rather than a random one.
	 */
	public function queryResultView() {
		// Clear the query rsult
		unset($this->queryResult);
		
		// get the current settings out of the session
		$isDescending = $this->getCurrentSortedOrder() == self::PRG_VARIABLE_VALUE_ORDER_DESC;
		$currentSortedColumn = $this->getCurrentSortedColumn();
			
		// Now call the BRDB sql statement
		$this->queryResult = $this->brdb->getUserStats($this->viewName, $currentSortedColumn, !$isDescending);
		if ($this->brdb->hasError()) {
			$this->setFailedMessage($this->brdb->getError());
		} 
	}
	
	public function getCurrentSortedOrder() {
		$prefixedVariableNameOrder = $this->getPrefixedName(self::PRG_VARIABLE_NAME_ORDER);
		if (isset($_SESSION[$prefixedVariableNameOrder]) && ($_SESSION[$prefixedVariableNameOrder] == SELF::PRG_VARIABLE_VALUE_ORDER_DESC)) {
			return self::PRG_VARIABLE_VALUE_ORDER_DESC;
		} else {
			return self::PRG_VARIABLE_VALUE_ORDER_ASC;
		}
	}
	
	public function getCurrentSortedColumn() {
		$prefixedVariableNameColumn = $this->getPrefixedName(self::PRG_VARIABLE_NAME_COLUMN);
		$retCurrentSortedColumn = $this->columnArray[0];
		
		// Check if the selected coulmn from the get statement is one of the registered
		// columns otherwise select another one (standard one) for the select query
		if (isset($_SESSION[$prefixedVariableNameColumn]) && in_array($_SESSION[$prefixedVariableNameColumn], $this->columnArray)) {
			$retCurrentSortedColumn= $_SESSION[$prefixedVariableNameColumn];
		}
		
		return $retCurrentSortedColumn;
	}
	
	/**
	 * Call this method to get the next row as an associative array
	 * @return Column of information as an associative array
	 */
	public function fetchResultViewRow() {
		return $this->queryResult->fetch_assoc();
	}
	
	/**
	 * use this method to register a column to the PRG pattern
	 * @param String $sqlColumnName the coliumn anme to be registered
	 */
	protected function registerColumn($sqlColumnName) {
		array_push($this->columnArray, $sqlColumnName);
	}
}
?>