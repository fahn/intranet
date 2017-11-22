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
include_once '../inc/logic/prgTournament.inc.php';
include_once '../inc/logic/prgPattern.inc.php';
include_once '../inc/logic/tools.inc.php';

include_once '../inc/src/Spout/Autoloader/autoload.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class BrdbHtmlTournamentPage extends BrdbHtmlPage {
	private $prgElementTournament;
	private $vars;

	public function __construct() {
		parent::__construct();
		$this->prgElementTournament = new PrgPatternElementTournament($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgElementTournament);

		$this->variable['playerId'] 	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_PLAYER);
		$this->variable['partnerId'] 	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_PARTNER);
		$this->variable['disziplin']	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_DISCIPLIN);
	}

  public function processPage() {
		parent::processPage();
  }
	/**
	*/
	protected function htmlBody() {
		$this->getMessages();

		$content = "";
		$action = isset($_GET['action']) ? $_GET['action'] : "";
		$id     = isset($_GET['id']) ? $_GET['id'] : "";

		if($action == "add_torunament") {
			$content = $this->loadContentAddTournament($id);
		} elseif($action == "details" && is_numeric($id)) {
			$content = $this->loadDetailsContent($id);
		} else if($action == "add" AND is_numeric($id)) {
			$content = $this->loadAddContent($id);
		} else if($action == "export" AND is_numeric($id)) {
			$content = $this->export($id);
		} else if($action == "deletePlayer" AND is_numeric($id) AND is_numeric($_GET['tournamentPlayerId'])) {
			$content = $this->deletePlayerFromTorunament($id, $_GET['tournamentPlayerId']);
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

		return $this->smarty->fetch('tournament/TournamentList.tpl');
	}


	private function loadContentAddTournament() {
		$this->variable['playerId'] 	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_TOURNAMENT_NAME);
		$this->variable['partnerId'] 	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_TOURNAMENT_PLACE);
		$this->variable['disziplin']	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_TOURNAMENT_STARTDATE);
		$this->variable['disziplin']	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_TOURNAMENT_ENDDATE);
		$this->variable['disziplin']	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_TOURNAMENT_STARTDATE);
		$this->variable['disziplin']	= $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_TOURNAMENT_LINK);

		$this->smarty->assign(array(
			'vars' => $this->variable,
		));
		return $this->smarty->fetch('tournament/TournamentAdd.tpl');
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

		return $this->smarty->fetch('tournament/TournamentDetails.tpl');
	}

  /**
		add player to tournament
	*/
	private function loadAddContent($id) {
		$this->smarty->assign(array(
			'tournament'  => $this->brdb->getTournamentData($id)->fetch_assoc(),
			'players'     => $this->getAllPlayerDataList(),
			'disciplines' => $this->getDisciplinesByTournamentId($id),
		));
		return $this->smarty->fetch('tournament/PlayerAdd.tpl');
	}

	private function getAllPlayerDataList() {
		$data = array();
		$res = $this->brdb->selectAllPlayer();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= array(
					'userId'   => $dataSet['userId'],
					'fullName' => $dataSet['fullName'],
				);
			}
		}
		return $data;
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

	private function deletePlayerFromTorunament($tournamentId, $playerId) {
		$this->prgElementTournament->deletePlayersFromTournamentId($tournamentId, $playerId);
	}
}
?>
