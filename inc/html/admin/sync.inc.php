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
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgSync.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

// load models
include_once $_SERVER['BASE_DIR'] .'/inc/model/club.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/model/player.inc.php';

// load logic
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgClub.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPlayer.inc.php';

class BrdbHtmlAdminSyncPage extends BrdbHtmlPage {
    private $prgPatternElementSync;
    private $prgPatternElementClub;

    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }

        # load links
        $links = array(
            'add' => $this->tools->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->tools->linkTo(array('page' => $this->_page, 'action' => 'add')),
        );

        $this->smarty->assign('links', $links);

        $this->prgPatternElementSync = new PrgPatternElementSync($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSync);


        $this->prgPatternElementClub = new PrgPatternElementClub($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);

        $this->prgPatternElementPlayer = new PrgPatternElementPlayer($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementPlayer);
    }


    public function htmlBody() {
        $action = $this->tools->get("action");
        $id     = $this->tools->get("id");

        //$this->syncClubs();
        $this->syncPlayer();

        switch ($action) {
          default:
            $content = $this->loadContent();
            break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent() {
        $this->smarty->assign(array(

        ));
        return $this->smarty->fetch('sync/status.tpl');
    }


    private function syncPlayer() {
        $statistics = array('new' => 0, 'updated' => 0, 'failed' => 0);
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $file = file_get_contents('https://service.badtra.de/player', false, stream_context_create($arrContextOptions));
        $data = json_decode($file);
        unset($file);//prevent memory leaks for large json.
        //insert data here
        echo "<pre>";

        $records = $data->player->records;
        foreach($records as $item) {
            if (empty($item->playerNr)) {
                continue;
            }
            try {
                $clubData = $this->brdb->selectClubByClubNr($item->clubNr)->fetch_assoc();
                $item->clubId = $clubData['clubId'];

                $player = new Player($item);

                if (! $this->prgPatternElementPlayer->find($player)) {
                    echo "NOT FOUND: ";
                    $this->prgPatternElementPlayer->insert($player);
                    $statistics['new']++;
                } else {
                    echo "FOUND ";
                    echo $this->prgPatternElementPlayer->update($player) ? '#T#' : '#F#';
                    $statistics['updated']++;
                }
                echo $player;
            } catch (Exception $e) {
                $statistics['failed']++;
            }
        }
        var_dump($statistics);

        return $statistics;
    }


    private function syncClubs() {
        $statistics = array('new' => 0, 'updated' => 0, 'failed' => 0);
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $file = file_get_contents('https://service.badtra.de/clubs', false, stream_context_create($arrContextOptions));
        $data = json_decode($file);
        #print_r($data);
        unset($file);//prevent memory leaks for large json.
        //insert data here
        echo "<pre>";
        $records = $data->clubs->records;
        foreach($records as $item) {
            try {
                $club = new Club($item);
                if (! $this->prgPatternElementClub->find($club)) {
                    echo "NOT FOUND:";
                    echo $club;
                    #$this->prgPatternElementClub->insert($item);
                    $statistics['new']++;
                } else {
                    echo "FOUND:";
                    echo $club;
                    echo "Status: ". ($this->prgPatternElementClub->update($club) ? 'updated' : 'failed') ."<br>";
                    $statistics['updated']++;
                }
            } catch (Exception $e) {
                $statistics['failed']++;
            }
        }
        die();

        return $statistics;
    }


}

?>
