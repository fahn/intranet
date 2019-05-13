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

include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementSync extends APrgPatternElement {
    private $brdb;
    protected $prgElementLogin;
    
    // links
    private const API_LIST_CLUB   = "https://api.badtra.de/club/list.php";
    private const API_LIST_PLAYER = "https://api.badtra.de/player/list.php";


    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("sync");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;
    }

    public function processPost() {
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        $isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if (!$this->prgElementLogin->isUserLoggedIn()) {
            return;
        }

        if (!$isUserReporter) {
            return;
        }
    }
    
    
    /**
     *  sync clubs
     */
    private function syncClubs() {
        $statistics = array('new' => 0, 'updated' => 0, 'failed' => 0);

        $file = file_get_contents(self::API_LIST_CLUB, false, $this->arrContextOptions());
        $data = json_decode($file);
        #print_r($data);
        unset($file);//prevent memory leaks for large json.

        $records = $data->clubs->records;
        foreach($records as $item) {
            try {
                $club = new Club($item);
                if (! $this->prgPatternElementClub->find($club)) {
                    $this->prgPatternElementClub->insert($item);
                    $statistics['new']++;
                } else {
                    $this->prgPatternElementClub->update($club);
                    $statistics['updated']++;
                }
            } catch (Exception $e) {
                $statistics['failed']++;
            }
        }

        return $statistics;
    }
    
    
    private function syncPlayer() {
        $statistics = array('new' => 0, 'updated' => 0, 'failed' => 0);

        $file = file_get_contents(self::API_LIST_PLAYER, false, $this->arrContextOptions());
        $data = json_decode($file);
        unset($file);//prevent memory leaks for large json.

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
                    $this->prgPatternElementPlayer->insert($player);
                    $statistics['new']++;
                } else {
                    echo $this->prgPatternElementPlayer->update($player) ? '#T#' : '#F#';
                    $statistics['updated']++;
                }
            } catch (Exception $e) {
                $statistics['failed']++;
            }
        }
        return $statistics;
    }
    
    private function arrContextOptions() {
        return  stream_context_create(array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                ));
    }


    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin     = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if ( !$this->prgElementLogin->isUserLoggedIn() || !$isUserAdmin ) {
            return;
        }
        
        #die("12345");
        
        $action = strval(trim($this->getGetVariable('action')));

        switch ($action) {                
            default:
                break;
        }
        return;
    }

}
?>
