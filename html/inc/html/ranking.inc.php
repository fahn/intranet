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
require_once('brdbHtmlPage.inc.php');
require_once(BASE_DIR .'/inc/logic/prgRanking.inc.php');
require_once(BASE_DIR .'/vendor/autoload.php');


class Ranking extends BrdbHtmlPage 
{
    private PrgPatternElementRanking $prgElementRanking;

    protected Smarty $smarty;

    private string $cssPrint;


    /**
     * Undocumented function
     */
    public function __construct() 
    {
        parent::__construct();

        $this->cssPrint = BASE_DIR .'/static/css/print.css';

        $this->prgElementRanking = new PrgPatternElementRanking($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementRanking);
    }

    /**
     * process Page
     *
     * @return void
     */
    public function processPage() 
    {
        parent::processPage();
    }

    /**
     * Decider
     *
     * @return void
     */
    protected function htmlBody(): void
    {
        switch ($this->action) 
        {
            case "add_game":
                $content = $this->TMPL_addGame();
                break;

            case "download":
                $content = $this->downloadPDF();
                break;

            case "delete":
                $content = $this->TMPL_deleteGame();
                break;

            default:
                $content = $this->TMPL_showRanking();
                break;
        }


        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }

    /**
     * Template: add game
     *
     * @return string
     */
    private function TMPL_addGame(): string
    {
        $this->smarty->assign(array(
            'task' => "add",
        ));

        return $this->smarty->fetch('ranking/insertMatch.tpl');
    }

    /**
     * add quote
     *
     * @param string $str
     * @return string
     */
    private function add_quotes(string $str): string
    {
        return sprintf("'%s'", $str);
    }

    
    /**
     * Template: show Ranking
     *
     * @param boolean $print
     * @return string
     */
    private function TMPL_showRanking(bool $print=false): string
    {
        $stats  = $this->getRankingGroupedByDate();
        $labels = implode(",", array_map(array($this, 'add_quotes'), $stats[0]));

        $this->smarty->assign(array(
            'ranking'     => $this->getRanking(),
            'games'       => $this->getGames(),
            'labels'      => $labels,
            'options'     => $stats[1],
            'print'       => $print,
            'stats'       => $this->prgElementRanking->getSettingBool('RANKING_STATS_ENABLE'),
        ));

        return $this->smarty->fetch('ranking/list.tpl');
    }


    /**
     * Template: Delete a game
     *
     * @return string
     */
    private function TMPL_deleteGame(): string
    {
        if (!$this->id) {
            $this->prgElementRanking->customRedirectArray(array('page' => 'ranking.php'));
        }
        // get Game Data
        $gameData  = $this->brdb->getGameById($this->id);
        
        $gameData['sets'] = $this->SetUnSerialize($gameData['sets']);
        $this->smarty->assign(array(
            'game'     => $gameData,
            'linkBack' => $this->prgElementRanking->linkTo(array('page' => __FILE__)),
        ));

        return $this->smarty->fetch('ranking/delete.tpl');
    }

    /**
     * get Ranking
     *
     * @return array
     */
    private function getRanking(): array
    {
        $rankingList  = $this->brdb->statementGetRanking();
        $data = array();
        if (isset($rankingList) && !empty($rankingList) ) {
            $rank = 1;
            foreach ($rankingList as $dataSet) {
                $dataSet['playerLink'] = $this->prgElementRanking->linkTo(array('page' => 'player.php', 'id' => $dataSet['playerId']));
                $data[$rank++] = $dataSet;
            }
        }

      return $data;
      unset($rankingList, $data, $dataSet, $rank);
    }


    private function getGames(): array
    {
        $gamesList  = $this->brdb->getMatches();
        $data = array();
        if (isset($gamesList) && !empty($gamesList) ) {
            foreach ($gamesList as $dataSet) {
                // sets
                $dataSet['sets'] = $this->SetUnSerialize($dataSet['sets']);
                // delete link
                $dataSet['deleteLink'] = $this->prgElementRanking->linkTo(array('page' => 'ranking.php', 'action' => 'delete', 'id' => $dataSet['gameId']));
                // link user
                $dataSet['playerLink'] = $this->prgElementRanking->linkTo(array('page' => 'user.php', 'id' => $dataSet['playerId']));
                // link oppenent
                $dataSet['opponentLink'] = $this->prgElementRanking->linkTo(array('page' => 'user.php', 'id' => $dataSet['opponentId']));

                $data[] = $dataSet;
            }
        }

        return $data;
        unset($gamesList, $data, $dataSet);
    }

    /**
     * get Ranking
     *
     * @return array
     */
    private function getRankingGroupedByDate(): array
    {
        $data   = $this->brdb->getMatchesGroupedByDate();
        $dates = array();
        $games = array();

        if (isset($data) && !empty($data) ) {
            foreach ($data as $dataSet) {
                $dates[] = $dataSet['gamedate'];
                $games[] = $dataSet['games'];
            }
        }

        return array($dates, $games);
        unset($dates, $data, $games, $dataSet);
    }


    private function downloadPDF():void
    {
        try {
            ob_start();

            // load Options
            $options = new Dompdf\Options();
            $options->set('defaultFont', 'Helvetica');
            $dompdf = new Dompdf\Dompdf($options);
            // get css
            $css     = file_get_contents($this->cssPrint);

            // get content
            $content = $this->TMPL_showRanking(true);
            $content = sprintf('<html><head><style><!-- %s --></style></head><body>%s</body></html>', $css, $content);

            $dompdf->loadHtml($content);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->getCanvas()->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", 10, array(0,0,0));
            $dompdf->render();

            // set name & download file
            $filename = sprintf("%s_%s.pdf", "ranking", date("d.m.y (H:i)"));
            $dompdf->stream($filename, array("Attachment" => false));
        } 
        catch (Exception $e) 
        {
            $this->PrgPatternElementRanking->log("Ranking", );
        }
    }

    private function SetUnSerialize(array $sets): string
    {
        return implode(" - ", unserialize($sets));
        unset($sets);
    }
}
?>
