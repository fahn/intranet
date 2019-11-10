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
    private const API_HOST            = "https://api.badtra.de/";
    private const API_LIST_CLUB       = "https://api.badtra.de/club/list";
    private const API_LIST_PLAYER     = "https://api.badtra.de/player/list.php";
    private const API_LIST_TOURNAMENT = "https://api.badtra.de/tournament/list.php";

    // statistics
    private $statistics = array('clubs' => '', 'player' => '', 'tournaments' => '');


    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("sync");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;

        #$this->statistics
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

    private function getBaseStats() {
        return array('new' => 0, 'updated' => 0, 'failed' => 0);
    }
    /**
     *  sync clubs
     */
    private function syncClubs() {
        $statistics = $this->getBaseStats();

        $file = file_get_contents(self::API_LIST_CLUB, false, $this->arrContextOptions());
        $data = json_decode($file);
        unset($file);//prevent memory leaks for large json.

        $records = $data->clubs->records;
        if ($records) {
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
            $this->statistics['clubs'] = $statistics;
        }
        return;
    }


    private function syncPlayer() {
        $statistics = $this->getBaseStats();

        $file = file_get_contents(self::API_LIST_PLAYER, false, $this->arrContextOptions());
        $data = json_decode($file);
        unset($file);//prevent memory leaks for large json.

        $records = $data->player->records;
        if ($records) {
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
                        $this->prgPatternElementPlayer->update($player);
                        $statistics['updated']++;
                    }
                } catch (Exception $e) {
                    $statistics['failed']++;
                }
            }
            $this->statistics['player'] = $statistics;
        }
        return;
    }

    private function syncTournament() {
        $statistics = $this->getBaseStats();

        $file = file_get_contents(self::API_LIST_TOURNAMENT, false, $this->arrContextOptions());
        $data = json_decode($file);
        unset($file);//prevent memory leaks for large json.

        $records = $data->player->records;
        if ($records) {
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
            $this->statistics['tournament'] = $statistics;
        }
        return;
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

        if ($this->prgElementLogin->getLoggedInUser()->getUserId() != 1) {
            return;
        }

        $action = strval(trim($this->getGetVariable('action')));

        switch ($action) {
            case 'sync':
                $this->startSyncMode();
                break;

            default:
                break;
        }
        return;
    }

    private function startSyncMode() {
        // check if api is available
        stream_context_set_default( [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $file_headers = @get_headers(self::API_HOST, 1);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $this->setFailedMessage("REMOTE HOST not available");
            return;
        }

        // sync Clubs
        $this->syncClubs();

        // sync Player
        $this->syncPlayer();

        // sync Tournmanet
        // $this->syncTournament();
    }

    public function getStatistics() {
        return $this->statistics;
    }

}
?>
