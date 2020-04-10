<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
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

# libary
require_once BASE_DIR .'/vendor/autoload.php';

class BrdbHtmlTournamentPage extends BrdbHtmlPage 
{
    private PrgPatternElementTournament $prgElementTournament;

    public function __construct() 
    {
        parent::__construct();

        // load pattern
        $this->prgElementTournament = new PrgPatternElementTournament($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementTournament);
    }

    protected function htmlBody(): void
    {
        $this->getMessages();

        $content  = "";

        switch ($this->action) {
            case 'add_tournament':
                $content = $this->updateTournamentTMPL();
                break;

            case 'edit_tournament':
                $content = $this->updateTournamentTMPL('edit');
                break;

            case 'calendar':
                $content = $this->calendar();
                break;

            case 'details':
                $content = $this->TMPL_showDetails();
                break;

            case 'add_player':
                $content = $this->addPlayerToTournamentTMPL();
                break;

            case 'backup':
                $content = $this->TMPL_backup();
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
    private function showTournamentsTMPL(): string
    {
        $this->smarty->assign(array(
            'tournamentList'       => $this->getAllTournamentDataList(),
            'oldTournamentList'    => $this->getOldTournamentDataList(),
            'calendar'             => $this->getAllTournamentDataList(),
            'googleMaps'           => $this->prgElementTournament->getSettingString('GOOGLE_MAPS_KEY'),
        ));

        return $this->smarty->fetch('tournament/TournamentList.tpl');
    }


    /**
     * @Route("/tournament/update/{id})
     *
     * @param string $action
     * @return void
     */
    private function updateTournamentTMPL(string $action = 'add'):string
    {
        $classificationArr = $this->valueIsKey($this->prgElementTournament->getTournamentAgeClass());
        $disciplineArr     = $this->valueIsKey($this->prgElementTournament->getTournamentModeArr());
        $reportArr         = $this->getAllUser();
        $rows              = array();

        $disciplinesByTournament = "";
        if ($action == 'edit') {
            $tournament                   = $this->brdb->getTournamentData($this->id);
            $tournament['classification'] = unserialize($tournament['classification']);
            $tournament['discipline']     = unserialize($tournament['discipline']);

            $tournament['additionalClassification']     = unserialize($tournament['additionalClassification']);
            if (isset($tournament['additionalClassification'] ) && is_array($tournament['additionalClassification'])) 
            {
                $tournament['additionalClassification'] = implode(",", $tournament['additionalClassification']);
            }

            $disciplinesByTournament = $this->brdb->getDisciplinesByTournamentId($this->id);
        }


        $this->smarty->assign(array(
            'task'              => $action,
            'hidden'            => $action == 'add' ? 'Insert Tournament' : 'Edit Tournament',
            'vars'              => $action == 'add' ? $this->getPostData() : $tournament,
            'disc'              => $disciplinesByTournament,
            'reporterArr'       => $reportArr,
            'classificationArr' => $classificationArr,
            'disciplineArr'     => $disciplineArr,
            'tournamentType'    => $this->prgElementTournament->getTournamentType(),
        ));
        return $this->smarty->fetch('tournament/TournamentUpdate.tpl');
    }

    //@TODO ?
    private function getPostData(): ?array
    {
        $data = array();
        if (isset($_POST)) {
            foreach ($_POST as $key => $value) {
                // @TASK
                $data[$key] = $value;
            }
        }
        return $data;

    }

    private function valueIsKey(array $arr): ?array
    {
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
     * @return void
     */
    private function calendar(): void
    {
        // get ressource
        $tournament = $this->brdb->getTournamentData($this->id);

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
     * details of a tournament
     *
     * @return string
     */
    private function TMPL_showDetails(): string
    {

        $tournament                   = $this->brdb->getTournamentData($this->id);
        $tournament['classification'] = $this->prgElementTournament->formatClassification($tournament['classification']);
        $tournament['discipline']     = $this->prgElementTournament->formatDiscipline($tournament['discipline']);
        if (isset($tournament['additionalClassification'])) {
          $tournament['additionalClassification'] = unserialize($tournament['additionalClassification']);
        }

        $this->smarty->assign(array(
            'tournament'   => $tournament,
            'players'      => $this->getPlayersByTournamentId($this->id),
            'disciplines'  => $this->getDisciplinesByTournamentId($this->id),
            'userPlayerId' => $this->prgPatternElementLogin->getLoggedInUser()->getPlayerId(),
        ));

        return $this->smarty->fetch('tournament/TournamentDetails.tpl');
    }

    /**
     * add player to tournament
     *
     * @return string
     */
    private function addPlayerToTournamentTMPL(): string
    {
        // load data
        $tournament = $this->brdb->getTournamentData($this->id);
        $disciplines = "";

        if (isset($tournament['classification']) && isset($tournament['discipline'])) {
          $classifications = unserialize($tournament['classification']);
          $disciplines     = unserialize($tournament['discipline']);
          $additionalClassification     = unserialize($tournament['additionalClassification']);
          $tmp = array();
          if (isset($classifications) && count($classifications) == 1 && $classifications[0] == 'O19' && isset($additionalClassification) && is_array($additionalClassification)) 
          {
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
        $linkToSupport = $this->prgElementTournament->linkTo(array(
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
     * Backup Tournament Data
     *
     * @return string
     */
    private function TMPL_backup():string 
    {
      $diff    = "";
      $backup = $this->brdb->getTournamentBackup($this->id);

        $this->smarty->assign(array(
            'backup' => $backup,
            'diff'   => $diff,
        ));

        if (isset($id) && count($backup) > 1) {
            $first  = $backup[0]['backupId'];
            $second = $backup[1]['backupId'];
            $rows = $this->brdb->getTournamentBackupDiff($first, $second);
            if (isset($backup) && is_array($backup)) {
                $rows = array();
                foreach ($backup as $row) {
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


    private function arrayRecursiveDiff(array $aArray1, array $aArray2): array
    {
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

        /*
            use SebastianBergmann\Diff\Differ;
            use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;

            $builder = new DiffOnlyOutputBuilder(
                "--- Original\n+++ New\n"
            );

            $differ = new Differ($builder);
            print $differ->diff('foo', 'bar');
                 */
    }

    private function getAllUser():array
    {
        $data = array();
        $res = $this->brdb->selectAllUser(); //SortBy('lastName', 'ASC');
        foreach ($res as $item) {
            $data[$item['userId']] = $item['fullName'];
        }
        
        return $data;
    }

     private function getAllTournamentDataList(): array
     {
        $tournamentList = $this->brdb->selectTournamentList();
        $data = array();
        
        if (isset($tournamentList) && !empty($tournamentList)) 
        {
            foreach ($tournamentList as $dataSet) 
            {
                $dataSet['classification'] = $this->prgElementTournament->formatClassification($dataSet['classification']);
                $dataSet['calLink']        = $this->linkToCalander($dataSet['tournamentId']);

                $data[] = $dataSet;
            }
        }
        unset($tournamentList, $dataSet);
        

        return $data;
        unset($data);
    }

    private function linkToCalander(int $tournamentId): string
    {
        return $this->prgElementTournament->linkTo(array(
            'page'   => 'tournament.php',
            'action' => 'calendar',
            'id'     => $tournamentId
        ));
    }


    private function getOldTournamentDataList(): array
    {
        $data = array();
        $tournamentList = $this->brdb->selectOldTournamentList();

        if (isset($tournamentList) && !empty($tournamentList)) 
        {
            $data = array();
            foreach ($tournamentList as $dataSet)
            {
                $dataSet['classification'] = $this->prgElementTournament->formatClassification($dataSet['classification']);

                $data[] = $dataSet;
            }
        }

        return $data;
    }

    /**
     * Get players from tournament
     *
     * @return array
     */
    private function getPlayersByTournamentId(): array
    {
        $playerList = $this->brdb->getPlayersByTournamentId($this->id);
        if (isset($playerList) && !empty($playerList)) 
        {
            $data = array();
            foreach ($playerList as $dataSet) 
            {
                // @TODO Change

                if ($this->isDouble($dataSet['classification'])) 
                {
                    if ($dataSet['partnerId'] > 0) {
                        $dataSet['partnerNr']   = $dataSet['partnerNr'];
                        $dataSet['partnerLink'] =  $this->prgElementTournament->linkTo(array('page' => 'player.php', 'id' => $dataSet['partnerId']));
                    } else {
                      $dataSet['partnerId']   = 0;
                      $dataSet['partnerName'] = 'FREI';

                    }
                } else {
                    unset($dataSet['partnerId']);
                    unset($dataSet['partnerNr']);
                }

                // Links
                $dataSet['linkPlayer']   = $this->prgElementTournament->linkTo(array('page' => 'player.php', 'id' => $dataSet['playerId']));
                $dataSet['linkReporter'] = $this->prgElementTournament->linkTo(array('page' => 'user.php', 'id' => $dataSet['reporterId']));
                $dataSet['linkDelete']   = $this->prgElementTournament->linkTo(array('page' => 'tournament.php', 'action' => 'deletePlayer', 'id' => $dataSet['tournamentId'], 'tournamentPlayerId' => $dataSet['tournamentPlayerId']));
                $dataSet['linkUnlock']   = $this->prgElementTournament->linkTo(array('page' => 'tournament.php', 'action' => 'unlock', 'id' => $dataSet['tournamentId'], 'tournamentPlayerId' => $dataSet['tournamentPlayerId']));
                $dataSet['linkLock']     = $this->prgElementTournament->linkTo(array('page' => 'tournament.php', 'action' => 'lock', 'id' => $dataSet['tournamentId'], 'tournamentPlayerId' => $dataSet['tournamentPlayerId']));

                $data[]                  = $dataSet;
            }

            return $data;
        }

        return array();
        unset($playerList, $this->id, $dataSet, $data);
    }

    private function getDisciplinesByTournamentId(): array
    {
        $data = array();
        $disciplinesList = $this->brdb->getDisciplinesByTournamentId($this->id);
        if (isset($disciplinesList) && !empty($disciplinesList)) {
            
            foreach ($disciplinesList as $dataSet) {
                $data[$dataSet['classId']] = $dataSet['name'] .' '. $dataSet['modus'];
            }
        }

        return $data;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return boolean
     */
    private function isDouble(string $value): bool
    {
        try {
            $arr = explode(" ", $value);
        } catch (Exception $e) 
        {
            $arr = $value;
        }
        substr($arr[0], -1);
        if (substr($arr[0], -1) == 'D') 
        {
            return true;
        }
        return false;
    }

}
?>
