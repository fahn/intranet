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

include_once '../inc/html/brdbHtmlPage.inc.php';
include_once '../inc/logic/prgGame.inc.php';
include_once '../inc/logic/tools.inc.php';

include_once '../inc/src/Spout/Autoloader/autoload.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class BrdbHtmlTournamentPage extends BrdbHtmlPage {

	public function __construct() {
		parent::__construct();
	}

  public function processPage() {
		parent::processPage();

  }
	/**
	*/
	protected function htmlBody() {
		$content = "";
		if(isset($_GET['details']) AND is_numeric($_GET['details'])) {
			$content = $this->loadDetailsContent($_GET['details']);
		} else if(isset($_GET['add']) AND is_numeric($_GET['add'])) {
			$content = $this->loadAddContent($_GET['add']);
		}else if(isset($_GET['export']) AND is_numeric($_GET['export'])) {
			$content = $this->export($_GET['export']);
		} else {
			$content = $this->loadListContent();
		}

    $this->smarty->assign(array(
			'content' => $content,
		));
		$this->smarty->display('index.tpl');
	}

	private function loadListContent() {
		$this->smarty->assign(array(
			'list'    => $this->getAllTournamentDataList(),
		));

		return $this->smarty->fetch('tournament/list.tpl');
	}

	/**
		details of a tournament
	*/
	private function loadDetailsContent($id) {
		if(!isset($id) or !is_numeric($id)) {
			return "";

		}
		$this->smarty->assign(array(
			'tournament'  => $this->brdb->getTournamentData($id)->fetch_assoc(),
			'players'     => $this->getPlayersByTournamentId($id),
			'disciplines' => $this->getDisciplinesByTournamentId($id),
			'userid'      => '',
		));

		return $this->smarty->fetch('tournament/details.tpl');
	}

  /**
		add player to tournament
	*/
	private function loadAddContent($id) {
		if(isset($_POST)) {
			// check
			$data = array(
				'tournamentID' => $id,
				'playerID'     => $_POST['playerID'],
				'partnerID'    => isset($_POST['partnerID']) ? $_POST['partnerID'] : NULL,
				'classID'      => $this->getClassID($_POST['classID']),
				'reporterID'   => $this->prgPatternElementLogin->getLoggedInUser()->getID(),
			);
			if($this->brdb->insetPlayerToTournament($data)) {
				$this->message['info'] = 'Erfolgreich hinzugefügt';
			}
		}

		$this->smarty->assign(array(
			'tournament'  => $this->brdb->getTournamentData($id)->fetch_assoc(),
			'players'     => $this->brdb->selectAllPlayer()->fetch_assoc(),
			'disciplines' => $this->getDisciplinesByTournamentId($id),
		));
		return $this->smarty->fetch('tournament/add.tpl');
	}

	private function getClassID($id) {
		$id = explode(" ", $id);
		if(count($id) == 2) {
			return $this->brdb->getClassIdByNameAndModus($name, $modus);
		}

		return null;
	}

	private function export($id) {
		if ($this->prgPatternElementLogin->getLoggedInUser()->isAdmin()) {
			ob_end_flush();
			unset($this->smarty);


			$players    = $this->brdb->getPlayersByTournamentId($id)->fetch_arroy();
			$tournament = $this->brdb->getTournamentData($id)->fetch_assoc();
			$fileName   = sprintf("%s_%s.xls", $tournament['name'], ""+ date("d.m.Y", $tournament['deadline']));
			$writer     = WriterFactory::create(Type::XLSX); // for XLSX files

			//$writer->openToFile($filePath); // write data to a file or to a PHP stream
			$writer->openToBrowser($fileName); // stream data directly to the browser
			$singleRow = array('das', 'das');
			$writer->addRow($singleRow); // add a row at a time
			//$writer->addRows($multipleRows); // add multiple rows at a time

			$writer->close();

		}
	}

	private function getAllTournamentDataList() {
		$res = $this->brdb->selectTournamentList();
		if (!$this->brdb->hasError()) {
			$data = array();
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= $dataSet;
			}

			return $data;
		}

		return "";
	}

	private function getPlayersByTournamentId($id) {
		$res = $this->brdb->getPlayersByTournamentId($id);
		if (!$this->brdb->hasError()) {
			$data = array();
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= $dataSet;
			}

			return $data;
		}

		return "";
	}

	private function getDisciplinesByTournamentId($id) {
		$res = $this->brdb->getDisciplinesByTournamentId($id);
		if (!$this->brdb->hasError()) {
			$data = array();
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= $dataSet;
			}

			return $data;
		}

		return "";
	}
}
?>
