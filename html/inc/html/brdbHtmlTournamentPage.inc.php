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

include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgTournament.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

# libary
require_once $_SERVER['BASE_DIR'] .'/vendor/autoload.php';
#use \Eluceo\iCal\Component\Calendar;
#use \Eluceo\iCal\Component\Event;



# diff
require_once $_SERVER['BASE_DIR'] .'/inc/class.Diff.php';


class BrdbHtmlTournamentPage extends BrdbHtmlPage {
    private $prgElementTournament;
    private $vars;

    private $tournamentType;

    public function __construct() {
        parent::__construct();

        $this->tools = new Tools();
        $this->tools->secure_array($_GET);

        $this->tournamentType = array('NBV', 'FUN', 'OTHER');

        $this->prgElementTournament = new PrgPatternElementTournament($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementTournament);

        $this->variable['playerId']  = $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_PLAYER);
        $this->variable['partnerId'] = $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_PARTNER);
        $this->variable['disziplin'] = $this->prgElementTournament->getPrefixedName(PrgPatternElementTournament::FORM_INPUT_DISCIPLIN);
    }

    public function processPage() {
        parent::processPage();
    }
    /**
    */
    protected function htmlBody() {
        $this->getMessages();

        $content = "";
        $action = $this->tools->get("action");
        $id     = $this->tools->get("id");

        switch ($action) {
            case 'add_tournament':
                $content = $this->updateTournamentTMPL($id);
                break;

            case 'edit_tournament':
                $content = $this->updateTournamentTMPL($id, 'edit');
                break;

            case 'calendar':
                $content = $this->calendar($id);
                break;

            case 'details':
                $content = $this->TMPL_showDetails($id);
                break;

            case 'add_player':
                $content = $this->addPlayerToTournamentTMPL($id);
                break;

            case 'backup':
                $content = $this->TMPL_backup($id);
                break;

            default:
                $content = $this->showTournamentsTMPL();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function showTournamentsTMPL() {
        $this->smarty->assign(array(
            'tournamentList'       => $this->getAllTournamentDataList(),
            'oldTournamentList'    => $this->getOldTournamentDataList(),
            'calendar'             => $this->getAllTournamentDataList(),
            'googleMaps'           => $this->tools->getIniValue('Maps'),
        ));

        return $this->smarty->fetch('tournament/TournamentList.tpl');
    }


    private function updateTournamentTMPL($id, $action = 'add') {

        $classificationArr = $this->valueIsKey($this->tools->getAgeClassArray());
        $disciplineArr     = $this->valueIsKey($this->tools->getModeArr());
        $reportArr         = $this->getAllUser();
        $rows              = array();


        if ($action == 'edit') {
            $id                           = $this->tools->get("id");
            $res                          = $this->brdb->getTournamentData($id);
            $tournament                   = $res->fetch_assoc();
            $tournament['classification'] = unserialize($tournament['classification']);
            $tournament['discipline']     = unserialize($tournament['discipline']);

            $tournament['additionalClassification']     = unserialize($tournament['additionalClassification']);
            if (isset($tournament['additionalClassification'] ) && is_array($tournament['additionalClassification'])) {
              $tournament['additionalClassification'] = implode(",", $tournament['additionalClassification']);
            }

            if (!$this->brdb->hasError()) {
                $res = $this->brdb->getDisciplinesByTournamentId($id);
                if($this->brdb->hasError()) {
                    return "Fehler";
                }
                $rows = array();
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
        }
        $this->smarty->assign(array(
            'task'              => $action,
            'hidden'            => $action == 'add' ? 'Insert Tournament' : 'Edit Tournament',
            'vars'              => $action == 'add' ? $_POST : $tournament,
            'disc'              => $rows,
            'players'           => $this->getAllPlayerDataList(),
            'reporterArr'       => $reportArr,
            'classificationArr' => $classificationArr,
            'disciplineArr'     => $disciplineArr,
            'tournamentType'    => $this->tournamentType,
        ));
        return $this->smarty->fetch('tournament/TournamentUpdate.tpl');
    }

    private function valueIsKey($arr) {
        if (is_array($arr)) {
            foreach($arr as $key => $item) {
                $tmp[$item] = $item;
            }
            return $tmp;
        }

        return array();
    }

    private function calendar($id) {
        if(!isset($id) or !is_numeric($id)) {
            return "";

        }
        $tournament                   = $this->brdb->getTournamentData($id)->fetch_assoc();
        extract($tournament);

        $vCalendar = new \Eluceo\iCal\Component\Calendar('Badminton');
        $vEvent    = new \Eluceo\iCal\Component\Event();
        $vEvent
            ->setDtStart(new \DateTime($startdate))
            ->setDtEnd(new \DateTime($enddate))
            ->setNoTime(true)
            ->setLocation($place)
            ->setSummary($name);
        $vCalendar->addComponent($vEvent);
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');
        echo $vCalendar->render();
    }

    /**
        details of a tournament
    */
    private function TMPL_showDetails($id) {
        if(!isset($id) or !is_numeric($id)) {
            return "";

        }
        $tournament                   = $this->brdb->getTournamentData($id)->fetch_assoc();
        $tournament['classification'] = $this->tools->formatClassification($tournament['classification']);
        $tournament['discipline']     = $this->tools->formatDiscipline($tournament['discipline']);
        if(isset($tournament['additionalClassification'])) {
          $tournament['additionalClassification'] = unserialize($tournament['additionalClassification']);
        }

        $this->smarty->assign(array(
            'tournament'  => $tournament,
            'players'     => $this->getPlayersByTournamentId($id),
            'disciplines' => $this->getDisciplinesByTournamentId($id),
            'userid'      => '',
        ));

        return $this->smarty->fetch('tournament/TournamentDetails.tpl');
    }

    /**
        add player to tournament
    */
    private function addPlayerToTournamentTMPL($id) {
        // load data
        $tournament = $this->brdb->getTournamentData($id)->fetch_assoc();
        $disciplines = "";

        if(isset($tournament['classification']) && isset($tournament['discipline'])) {
          $classifications = unserialize($tournament['classification']);
          $disciplines     = unserialize($tournament['discipline']);
          $additionalClassification     = unserialize($tournament['additionalClassification']);
          $tmp = array();
          if(isset($classifications) && count($classifications) == 1 && $classifications[0] == 'O19' && isset($additionalClassification) && is_array($additionalClassification)) {
            $classifications = $additionalClassification;
          }
          foreach ($classifications as $classification) {
              foreach ($disciplines as $discipline) {
                  $tmp[] = $discipline ." ". $classification;
              }
          }
          sort($tmp);
          $disciplines = $tmp;
        }
        $linkToSupport = $this->tools->linkTo(array(
          'page'   => 'support.php',
          'action' => 'new_player',
        ));
        $this->smarty->assign(array(
            'tournament'     => $tournament,
            'disciplines'    => $disciplines,
            'linkToSupport'  => $linkToSupport,
        ));
        return $this->smarty->fetch('tournament/PlayerAdd.tpl');
    }


    /**
     *
     */
    private function TMPL_backup() {
      $diff = "";

      // compare two strings line by line

      $res = $this->brdb->getTournamentBackup($_GET['id']);
      while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
      }
      $this->smarty->assign(array(
        'backup' => $rows,
        'diff'   => $diff,
      ));

      if(isset($_GET['id']) && count($rows) > 1) {
        $first  = $rows[0]['backupId'];
        $second = $rows[1]['backupId'];
        $res = $this->brdb->getTournamentBackupDiff($first, $second);
        if($res) {
            $rows = array();
            while ($row = $res->fetch_assoc()) {
              $rows[] = unserialize($row['data']);
            }
            $result = $this->arrayRecursiveDiff($rows[0], $rows[1]);
            $this->smarty->assign(array(
              'diffResult'   => $result,
              'diff'         => $rows,
            ));

            #$diff = Diff::toTable(Diff::compare($rows[0], $rows[1]));
        }
      }

      return $this->smarty->fetch('tournament/backup.tpl');
    }


    private function arrayRecursiveDiff($aArray1, $aArray2) {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
          if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
              $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
              if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
            } else {
              if ($mValue != $aArray2[$mKey]) {
                $aReturn[$mKey] = $mValue;
              }
            }
          } else {
            $aReturn[$mKey] = $mValue;
          }
        }
        return $aReturn;
    }

    private function getAllPlayerDataList() {
        $data = array();
        $res = $this->brdb->selectGetAllPlayer(); //SortBy('lastName', 'ASC');
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[]         = array(
                    'clubId'    => $dataSet['clubId'],
                    'playerId'  => $dataSet['playerId'],
                    'fullName'  => $dataSet['fullName'],
                );
            }
        }
        return $data;
    }

    private function getAllUser() {
        $data = array();
        $res = $this->brdb->selectAllUser(); //SortBy('lastName', 'ASC');
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[$dataSet['userId']] = $dataSet['fullName'];
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



    private function getAllTournamentDataList() {
        $res = $this->brdb->selectTournamentList();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['classification'] = $this->tools->formatClassification($dataSet['classification']);
                $dataSet['calLink'] = $this->tools->linkTo(array(
                  'page'   => 'tournament.php',
                  'action' => 'calendar',
                  'id'     => $dataSet['tournamentId']));

                $data[] = $dataSet;
            }

            return $data;
        }

        return "";
    }



    private function getOldTournamentDataList() {
        $res = $this->brdb->selectOldTournamentList();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['classification'] = $this->tools->formatClassification($dataSet['classification']);
                $data[] = $dataSet;
            }

            return $data;
        }

        return "";
    }

    /** Get players from tournament
     *
     * @param unknown $id
     * @return unknown[]|string
     */
    private function getPlayersByTournamentId($id) {
        $res = $this->brdb->getPlayersByTournamentId($id);
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                // @TODO Change

                if($this->isDouble($dataSet['classification'])) {
                    if($dataSet['partnerId'] > 0) {

                        $dataSet['partnerLink'] =  $this->tools->linkTo(array('page' => 'player.php', 'id' => $dataSet['partnerId']));
                    } else {
                      $dataSet['partnerId']   = 0;
                      $dataSet['partnerName'] = 'FREI';
                    }
                } else {
                    unset($dataSet['partnerId']);
                }

                // Links
                $dataSet['linkPlayer'] = $this->tools->linkTo(array('page' => 'player.php', 'id' => $dataSet['playerId']));
                $dataSet['linkReporter'] = $this->tools->linkTo(array('page' => 'user.php', 'id' => $dataSet['reporterId']));
                $dataSet['linkDelete'] = $this->tools->linkTo(array('page' => 'tournament.php', 'action' => 'deletePlayer', 'id' => $dataSet['tournamentId'], 'tournamentPlayerId' => $dataSet['tournamentPlayerId']));
                $dataSet['linkUnlock'] = $this->tools->linkTo(array('page' => 'tournament.php', 'action' => 'unlock', 'id' => $dataSet['tournamentId'], 'tournamentPlayerId' => $dataSet['tournamentPlayerId']));
                $dataSet['linkLock'] = $this->tools->linkTo(array('page' => 'tournament.php', 'action' => 'lock', 'id' => $dataSet['tournamentId'], 'tournamentPlayerId' => $dataSet['tournamentPlayerId']));

                $data[]         = $dataSet;
            }

            return $data;
        }

        return "";
    }

    private function strpos_arr($search, $array) {
      if(is_array($array) && count($array) > 0) {
        foreach ($array as $value) {
          $pos = strpos($search, $value);
          if($pos !== false) {
            return true;
          }
        }
      }

      return false;

    }

    private function getDisciplinesByTournamentId($id) {
        $res = $this->brdb->getDisciplinesByTournamentId($id);

        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                $data[$dataSet['classId']] = $dataSet['name'] .' '. $dataSet['modus'];
            }

            return $data;
        }

        return "";
    }

    private function isDouble($value) {
        try {
            $arr = explode(" ", $value);
        } catch (Exception $e) {
            $arr = $value;
        }
        echo substr($arr[0], -1);
        if (substr($arr[0], -1) == 'D') {
            return true;
        }
        return false;
    }

}
?>
