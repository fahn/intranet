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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgTournament.inc.php';
include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

# libary
require_once BASE_DIR .'/vendor/autoload.php';

// Routing
use Symfony\Component\Routing\Annotation\Route;



# diff
require_once BASE_DIR .'/inc/class.Diff.php';


class BrdbHtmlTournamentPage extends BrdbHtmlPage {
    private $prgElementTournament;

    private $tournamentType;

    private $tournamenIniValues;

    public function __construct() {
        parent::__construct();

        $this->tools = new Tools();
        $this->tools->secure_array($_GET);

        $this->tournamenIniValues = $this->tools->getIniValue('tournament'); # array('NBV', 'FUN', 'OTHER');

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

        $content  = "";
        $action   = $this->tools->get("action");
        $actionId = $this->tools->get("id");

        switch ($action) {
            case 'add_tournament':
                $content = $this->updateTournamentTMPL($actionId);
                break;

            case 'edit_tournament':
                $content = $this->updateTournamentTMPL($actionId, 'edit');
                break;

            case 'calendar':
                $content = $this->calendar($actionId);
                break;

            case 'details':
                $content = $this->TMPL_showDetails($actionId);
                break;

            case 'add_player':
                $content = $this->addPlayerToTournamentTMPL($actionId);
                break;

            case 'backup':
                $content = $this->TMPL_backup($actionId);
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

    /**
     * @Route("/tournament/")
     *
     * @return void
     */
    private function showTournamentsTMPL() {
        $this->smarty->assign(array(
            'tournamentList'       => $this->getAllTournamentDataList(),
            'oldTournamentList'    => $this->getOldTournamentDataList(),
            'calendar'             => $this->getAllTournamentDataList(),
            'googleMaps'           => $this->tools->getIniValue('Maps'),
        ));

        return $this->smarty->fetch('tournament/TournamentList.tpl');
    }


    /**
     * @Route("/tournamen/update/{id})
     *
     * @param [type] $actionId
     * @param string $action
     * @return void
     */
    private function updateTournamentTMPL($actionId, $action = 'add') {
        $classificationArr = $this->valueIsKey($this->getTournamentAgeClass());
        $disciplineArr     = $this->valueIsKey($this->getTournamentModeArr());
        $reportArr         = $this->getAllUser();
        $rows              = array();


        if ($action == 'edit') {
            $actionId                     = $this->tools->get("id");
            $res                          = $this->brdb->getTournamentData($actionId);
            $tournament                   = $res->fetch_assoc();
            $tournament['classification'] = unserialize($tournament['classification']);
            $tournament['discipline']     = unserialize($tournament['discipline']);

            $tournament['additionalClassification']     = unserialize($tournament['additionalClassification']);
            if (isset($tournament['additionalClassification'] ) && is_array($tournament['additionalClassification'])) {
              $tournament['additionalClassification'] = implode(",", $tournament['additionalClassification']);
            }

            if (!$this->brdb->hasError()) {
                $res = $this->brdb->getDisciplinesByTournamentId($actionId);
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
            'vars'              => $action == 'add' ? $this->getPostData() : $tournament,
            'disc'              => $rows,
            'players'           => $this->getAllPlayerDataList(),
            'reporterArr'       => $reportArr,
            'classificationArr' => $classificationArr,
            'disciplineArr'     => $disciplineArr,
            'tournamentType'    => $this->tournamentType,
        ));
        return $this->smarty->fetch('tournament/TournamentUpdate.tpl');
    }

    private function getPostData() {
        if (isset($_POST)) {
            $data = array();
            foreach ($_POST as $key => $value) {
                // @TASK
                $data[$key] = $value;
            }

            return $data;
        }
        return "";

    }

    private function valueIsKey($arr) {
        if (is_array($arr)) {
            foreach($arr as $item) {
                $tmp[$item] = $item;
            }
            return $tmp;
        }

        return array();
    }

    /**
     * Show calendar for tournament
     *
     * @param int $actionId
     * @return void
     */
    private function calendar(int $actionId) {
        if(!isset($actionId) or !is_numeric($actionId)) {
            return "";

        }

        // get ressource
        $tournament = $this->brdb->getTournamentData($actionId)->fetch_assoc();

        // load cal
        $vCalendar = new \Eluceo\iCal\Component\Calendar('Badminton');
        $vEvent    = new \Eluceo\iCal\Component\Event();
        $vEvent
            ->setDtStart(new \DateTime($tournament['startdate']))
            ->setDtEnd(new \DateTime($tournament['enddate']))
            ->setNoTime(true)
            ->setLocation($tournament['place'])
            ->setSummary($tournament['name']);
        $vCalendar->addComponent($vEvent);

        // set header
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');

        // render object and print it
        echo $vCalendar->render();
    }

    /**
        details of a tournament
    */
    private function TMPL_showDetails($actionId) {
        if(!isset($actionId) or !is_numeric($actionId)) {
            return "";
        }
        $tournament                   = $this->brdb->getTournamentData($actionId)->fetch_assoc();
        $tournament['classification'] = $this->tools->formatClassification($tournament['classification']);
        $tournament['discipline']     = $this->tools->formatDiscipline($tournament['discipline']);
        if(isset($tournament['additionalClassification'])) {
          $tournament['additionalClassification'] = unserialize($tournament['additionalClassification']);
        }

        $this->smarty->assign(array(
            'tournament'   => $tournament,
            'players'      => $this->getPlayersByTournamentId($actionId),
            'disciplines'  => $this->getDisciplinesByTournamentId($actionId),
            'userPlayerId' => $this->prgPatternElementLogin->getLoggedInUser()->getPlayerId(),
        ));

        return $this->smarty->fetch('tournament/TournamentDetails.tpl');
    }

    /**
        add player to tournament
    */
    private function addPlayerToTournamentTMPL($actionId) {
        // load data
        $tournament = $this->brdb->getTournamentData($actionId)->fetch_assoc();
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
     * @param unknown $actionId
     * @return unknown[]|string
     */
    private function getPlayersByTournamentId($actionId) {
        $res = $this->brdb->getPlayersByTournamentId($actionId);
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                // @TODO Change

                if($this->isDouble($dataSet['classification'])) {
                    if($dataSet['partnerId'] > 0) {
                        $dataSet['partnerNr']   = $dataSet['partnerNr'];
                        $dataSet['partnerLink'] =  $this->tools->linkTo(array('page' => 'player.php', 'id' => $dataSet['partnerId']));
                    } else {
                      $dataSet['partnerId']   = 0;
                      $dataSet['partnerName'] = 'FREI';

                    }
                } else {
                    unset($dataSet['partnerId']);
                    unset($dataSet['partnerNr']);
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

    private function getDisciplinesByTournamentId($actionId) {
        $res = $this->brdb->getDisciplinesByTournamentId($actionId);

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
        substr($arr[0], -1);
        if (substr($arr[0], -1) == 'D') {
            return true;
        }
        return false;
    }

}
?>
