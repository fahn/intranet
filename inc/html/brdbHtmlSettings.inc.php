<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.    *
 *                                                        *
 * Copyright 2017                                         *
 *                                                        *
 * All Rights Reserved                                    *
 *                                                        *
 * Copying, distribution, usage in any form is not        *
 * allowed without  written permit.                       *
 *                                                        *
 * Stefan Metzner (stefan@weinekind.de)                   *
 *                                                        *
 ********************************************************/

include_once '../inc/html/brdbHtmlPage.inc.php';
include_once '../inc/logic/prgTournament.inc.php';
include_once '../inc/logic/prgPattern.inc.php';
include_once '../inc/logic/tools.inc.php';

class BrdbHtmlSettings extends BrdbHtmlPage {
    private $prgElementTournament;
    private $vars;

    private $tools;

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
        $content = $this->smarty->fetch("settings.tpl");

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function showTournamentsTMPL() {
        $this->smarty->assign(array(
            'list'    => $this->getAllTournamentDataList(),
        ));

        return $this->smarty->fetch('tournament/TournamentList.tpl');
    }


    private function addTournamentTMPL() {
        $classificationArr = $this->tools->getAgeClassArray();
        $disciplineArr     = $this->tools->getModeArr();

        $this->smarty->assign(array(
            'task'              => 'add',
            'hidden'            => 'Insert Tournament',
            'vars'              => $_POST,
            'players'           => $this->getAllPlayerDataList(),
            'classificationArr' => $classificationArr,
            'disciplineArr'     => $disciplineArr,
            'tournamentType'    => $this->tournamentType,
        ));
        return $this->smarty->fetch('tournament/TournamentAdd.tpl');
    }


    private function editTournamentTMPL() {
        // generell
        $classificationArr = $this->tools->getAgeClassArray();
        $disciplineArr     = $this->tools->getModeArr();

        $id                           = $this->tools->get("id");
        $res                          = $this->brdb->getTournamentData($id);
        $tournament                   = $res->fetch_assoc();
        $tournament['classification'] = unserialize($tournament['classification']);
        $tournament['discipline']     = unserialize($tournament['discipline']);
        $tournament['additionalClassification']     = unserialize($tournament['additionalClassification']);
        if(isset($tournament['additionalClassification'] ) && is_array($tournament['additionalClassification'])) {
          $tournament['additionalClassification'] = implode(",", $tournament['additionalClassification']);
        }
        #die($tournament['additionalClassification'] );

        if(!$this->brdb->hasError()) {
          $res = $this->brdb->getDisciplinesByTournamentId($id);
          if($this->brdb->hasError()) {
            return "Fehler";
          }
          $rows = array();
          while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
          }

          $this->smarty->assign(array(
            'task'   => 'edit',
            'hidden' => 'Edit Tournament',
            'vars'   => $tournament,
            'disc'   => $rows,
            'players'           => $this->getAllPlayerDataList(),
            'classificationArr' => $classificationArr,
            'disciplineArr'     => $disciplineArr,
            'tournamentType'    => $this->tournamentType,
          ));
        }

        return $this->smarty->fetch('tournament/TournamentAdd.tpl');
    }

    /**
        details of a tournament
    */
    private function showDetailsTMPL($id) {
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
        $this->smarty->assign(array(
            'tournament'  => $tournament,
            'clubs'     => $this->getAllPlayerDataListAndSortByClub(),
            'disciplines' => $disciplines,
        ));
        return $this->smarty->fetch('tournament/PlayerAdd.tpl');
    }

    private function getAllPlayerDataList() {
        $data = array();
        $res = $this->brdb->selectAllUser(); //SortBy('lastName', 'ASC');
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[]         = array(
                    'clubId'   => $dataSet['clubId'],
                    'userId'   => $dataSet['userId'],
                    'fullName' => sprintf("%s %s", $dataSet['firstName'], $dataSet['lastName']),
                );
            }
        }
        return $data;
    }

    private function getAllPlayerDataListAndSortByClub() {
        $players = $this->getAllPlayerDataList();
        $res     = $this->brdb->selectAllClubs(0,0);

        if (!$this->brdb->hasError()) {
            while ($club = $res->fetch_assoc()) {
              $club['players'] = array();
              $clubs[$club['clubId']] = $club;
            }
            foreach ($players as $player) {
              $clubs[$player['clubId']]['players'][] = $player;
            }
        }
        return $clubs;
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
                $data[] = $dataSet;
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
                $data[]         = $dataSet;
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
                #$data[]         = $dataSet;
                $data[$dataSet['classID']] = $dataSet['name'] .' '. $dataSet['modus'];
            }

            return $data;
        }

        return "";
    }

    /** DELETE player
      *
      */
    private function deletePlayerFromTorunament($tournamentId, $playerId) {
        #$this->prgElementTournament->deletePlayersFromTournamentId($tournamentId, $playerId);
    }

    /** Export tool
      *
      */
    private function export($id) {
        if (
          $this->prgPatternElementLogin->getLoggedInUser()->isAdmin() ||
          $this->prgPatternElementLogin->getLoggedInUser()->isReporter()
        ) {
            #session_write_close();
            ob_end_flush();
            if(isset($this->smarty)) {
              unset($this->smarty);
            }
            header_remove();

            // get tournament data
            $tournament = $this->brdb->getTournamentData($id)->fetch_assoc();

            switch ($tournament['tournamentType']) {
              case 'NBV':
                $this->reportExcelNBV($tournament, $id);
                break;

              default:
                $this->reportDefault($tournament, $id);
                break;
            }

            // kill it
            exit(0);


        }
      }

        private function reportExcelNBV($tournament, $id) {
            if(isset($tournament['name']) && $tournament['deadline']) {
                $fileName   = sprintf("%s_%d.xlsx", addslashes($tournament['name']), date("d.m.Y", strtotime($tournament['deadline'])));
            } else {
                $fileName = "random.xlsx";
            }

            $writer     = WriterFactory::create(Type::XLSX); // for XLSX files

            //$writer->openToFile($filePath); // write data to a file or to a PHP stream
            $writer->openToBrowser($fileName); // stream data directly to the browser



            // create sheets
            $einzel = $writer->getCurrentSheet();
            $einzel->setName('Einzel');
            $singleDataRow = array(
                'Vorname',
                'Name',
                'm/w',
                'Verein',
                'Verb',
                'Einzel',
                'Doppel',
                'Mixed',
                'ERP',
                'DRP',
                'MRP',
                'SBNr',
                'GebDat',
                'VereinsNr',
                'Rangfolge',
                'Quote',
                'gemeldet von',
            );
            $writer->addRow($singleDataRow);

            // create double sheets
            $doppel = $writer->addNewSheetAndMakeItCurrent();
            $doppel->setName('Doppel');
            $writer->addRow($singleDataRow);
        // create mx sheets
            $mixed = $writer->addNewSheetAndMakeItCurrent();
            $mixed->setName('Mixed');
            $writer->addRow($singleDataRow);

            $einzelCount = 0;
            $mixedCount  = 0;
            $doppelCount = 0;


            $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
            while($row = $players->fetch_assoc()) {
                if(strpos($row['classification'], 'GD') !== false) {
                    $writer->setCurrentSheet($mixed);
                    $add       = True;
                    $rowEinzel = '';
                    $rowDoppel = '';
                    $rowMixed  = $row['classification'];
                    $counter = ++$mixedCount;
                } else if(strpos($row['classification'], 'DD') !== false || strpos($row['classification'], 'HD') !== false) {
                    $writer->setCurrentSheet($doppel);
                    $add       = True;
                    $rowEinzel = '';
                    $rowDoppel = $row['classification'];
                    $rowMixed  = '';
                    $counter   = ++$doppelCount;
                } else {
                    $writer->setCurrentSheet($einzel);
                    $add       = False;
                    $rowEinzel = $row['classification'];
                    $rowDoppel = '';
                    $rowMixed  = '';
                    $counter   = ++$einzelCount;
                }
                // add Player 1
                $bday   = date("d.m.Y", strtotime($row['p1Bday']));
                $gender = $row['p1Gender'] == 'Male' ? 'm' : 'w';
                $singleRow = array(
                    $row['p1FirstName'],
                    $row['p1LastName'],
                    $gender,
                    $row['p1ClubName'],
                    $row['p1ClubAssociation'],
                    $rowEinzel,
                    $rowDoppel,
                    $rowMixed,
                    '',
                    '',
                    '',
                    $row['p1PlayerNumber'],
                    $bday,
                    $row['p1ClubNumber'],
                    $counter,
                    '',
                    '',
                );
                $writer->addRow($singleRow); // add a row at a time


                if($add == True) {
                    if($row['p2FirstName'] != NULL AND $row['p2LastName'] != NULL) {
                        $firstName = $row['p2FirstName'];
                        $lastName  = $row['p2LastName'];
                        $gender    = $row['p2Gender'] == 'Male' ? 'm' : 'w';

                        if(!(int) $row['p2Bday'] != 0) {
                            $bday = '';
                        } else {
                            $due       = strtotime($row['p2Bday']);
                            $bday      = date("d.m.Y", $due);
                        }
                    } else {
                        $firstName = "FREIMELDUNG";
                        $lastName  = "";
                        $gender    = "";
                        $bday      = "";
                    }

                    $verein      = isset($row['p2ClubName'])     ? $row['p2ClubName']     : '';
                    $association = isset($row['p2ClubAssociation'])  ? $row['p2ClubAssociation']  : '';
                    $sbnr        = isset($row['p2PlayerNumber']) ? $row['p2PlayerNumber'] : '';

                    $singleRow = array(
                        $firstName,
                        $lastName,
                        $gender,
                        $verein,
                        $association,
                        $rowEinzel,
                        $rowDoppel,
                        $rowMixed,
                        '',
                        '',
                        '',
                        $sbnr,
                        $bday,
                        $row['p2ClubNumber'],
                        $counter,
                        '',
                        '',
                    );
                    $writer->addRow($singleRow); // add a row at a time
                }
            }
            //$writer->addRows($multipleRows); // add multiple rows at a time
            $writer->close();
    }


    private function reportDefault($tournament, $id) {
      if(isset($tournament['name']) && $tournament['deadline']) {
          $fileName   = sprintf("%s_%d.xlsx", addslashes($tournament['name']), date("d.m.Y", strtotime($tournament['deadline'])));
      } else {
          $fileName = "random.xlsx";
      }

      $writer     = WriterFactory::create(Type::XLSX); // for XLSX files

      //$writer->openToFile($filePath); // write data to a file or to a PHP stream
      $writer->openToBrowser($fileName); // stream data directly to the browser



      // create sheets
      $einzel = $writer->getCurrentSheet();
      $einzel->setName('Spieler');

      $players    = $this->brdb->getPlayersByTournamentIdToExport($id);
      while($row = $players->fetch_assoc()) {
        $singleRow = array(
          $row['p1FirstName'],
          $row['p1LastName'],
          $gender,
          $row['p1ClubName'],
          $row['p1ClubAssociation'],
          $row['p1PlayerNumber'],
          $bday,
          $row['p1ClubNumber'],
          $counter,
        );
        $writer->addRow($singleRow);
      }


      $writer->close();


    }
}
?>
