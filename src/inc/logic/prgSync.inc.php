<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
require_once "prgPattern.inc.php";

class PrgPatternElementSync extends APrgPatternElement
{
    private PrgPatternElementClub $prgPatternElementClub;
    private PrgPatternElementPlayer $prgPatternElementPlayer;

    private $page = "adminSync.php";

    // links
    private const API_HOST            = "https://api.badtra.de/";
    private const API_LIST_CLUB       = "https://api.badtra.de/club/list";
    private const API_LIST_PLAYER     = "https://api.badtra.de/player/list";
    private const API_LIST_TOURNAMENT = "https://api.badtra.de/tournament/list";

    // statistics
    private $statistics = array("clubs" => "", "player" => "", "tournaments" => "");


    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("sync");

        $this->prgElementLogin = $prgElementLogin;

        // Club
        $this->prgPatternElementClub = new PrgPatternElementClub($this->prgElementLogin);

        // Player
        $this->prgPatternElementPlayer = new PrgPatternElementPlayer($this->prgElementLogin);


    }

    public function processPost(): void
    {
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();

        if (!$isUserLoggedIn || !$isUserAdmin) {
            return;
        }
    }

    private function getBaseStats(): array
    {
        return array("new" => 0, "updated" => 0, "failed" => 0);
    }

    private function getContentFromRemote($link) {
        $file = file_get_contents($link, false, $this->arrContextOptions());
        return json_decode($file);
        unset($file);
    }
    /**
     * Get Clubs from remote API
     *
     * @return void
     */
    private function syncClubs(): void
    {
        $statistics = $this->getBaseStats();

        $data = $this->getContentFromRemote(self::API_LIST_CLUB);
        if (isset($data) && isset($data->records)) {
            $records = $data->records;
            foreach($records as $item) {
                try {
                    $club = new Club($item);
                    if (! $this->prgPatternElementClub->find($club)) {
                        $this->prgPatternElementClub->insert($club);
                        $statistics["new"]++;
                    } else {
                        $this->prgPatternElementClub->update($club);
                        $statistics["updated"]++;
                    }
                } catch (Exception $e) {
                    $statistics["failed"]++;
                }
            }
            $this->statistics["clubs"] = $statistics;
        }
    }


    private function syncPlayer() {
        $statistics = $this->getBaseStats();

        $data = $this->getContentFromRemote(self::API_LIST_PLAYER);
        $records = $data->records;
        if ($records) {
            foreach($records as $item) {
                if (empty($item->playerNr)) {
                    continue;
                }

                try {
                    $clubData = $this->brdb->selectClubByClubNr($item->clubNr);
                    $item->clubId = $clubData["clubId"];

                    $player = new Player($item);

                    if (! $this->prgPatternElementPlayer->find($player)) {
                        $this->prgPatternElementPlayer->insert($player);
                        $statistics["new"]++;
                    } else {
                        $this->prgPatternElementPlayer->update($player);
                        $statistics["updated"]++;
                    }
                } catch (Exception $e) {
                    $statistics["failed"]++;
                }
            }
            $this->statistics["player"] = $statistics;
        }
        return;
    }

    private function syncTournament() {
        $statistics = $this->getBaseStats();

        $data = $this->getContentFromRemote(self::API_LIST_TOURNAMENT);
        $records = $data->records;
        if ($records) {
            foreach($records as $item) {
                if (empty($item->playerNr)) {
                    continue;
                }
                try {
                    $clubNr = $item->getClubNr();
                    $clubData = $this->brdb->selectClubByClubNr($clubNr);
                    $item->clubId = $clubData["clubId"];

                    $player = new Player($item);

                    if (! $this->prgPatternElementPlayer->find($player)) {
                        $this->prgPatternElementPlayer->insert($player);
                        $statistics["new"]++;
                    } else {
                        $this->prgPatternElementPlayer->update($player);
                        $statistics["updated"]++;
                    }
                } catch (Exception $e) {
                    $statistics["failed"]++;
                }
            }
            $this->statistics["tournament"] = $statistics;
        }
        return;
    }

    private function arrContextOptions()
    {
        return  stream_context_create(array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                    "http"=>array(
                        "method"=>"GET",
                        "header"=>"Content-Type: application/json"
                      )
                ));
    }



    public function processGet():void
    {
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();

        if (!$isUserLoggedIn && !$isUserAdmin )
        {
            return;
        }

        $action = strval(trim($this->getGetVariable("action")));

        switch ($action)
        {
            case "sync":
                $this->startSyncMode();
                break;

            default:
                break;
        }
        return;
    }

    private function startSyncMode(): void
    {
        // check if api is available
        stream_context_set_default( [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
            "http" => [
                "timeout" => 5
            ]
        ]);

        $file_headers = @get_headers(self::API_HOST, 1);
        if (!$file_headers || $file_headers[0] == "HTTP/1.1 404 Not Found") {
            $this->setFailedMessage("REMOTE HOST not available");
            return;
        }

        // sync Clubs
        $this->syncClubs();

        // sync Player
        $this->syncPlayer();

        // sync Tournmanet
        $this->syncTournament();

        // save statistics
        $this->setSuccessMessage($this->statistics);

        // redirect
        $this->customRedirectArray(array("page" => $this->page));
    }

    public function getStatistics(): array
    {
        return $this->statistics;
    }

}

