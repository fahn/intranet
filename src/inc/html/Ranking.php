<?php
/**
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
namespace Badtra\Intranet\Html;

require_once BASE_DIR ."/vendor/autoload.php";

class Ranking extends BrdbHtmlPage
{
    private \Badtra\Intranet\Logic\PrgPatternElementRanking $prgElementRanking;

    

    private string $cssPrint;


    /**
     * Undocumented function
     */
    public function __construct()
    {
        parent::__construct();

        $this->cssPrint = BASE_DIR ."/static/css/print.css";

        $this->prgElementRanking = new \Badtra\Intranet\Logic\PrgPatternElementRanking($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementRanking);
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


            case "download":
                $content = $this->downloadPDF();
                break;

            case "delete":
                $content = $this->TMPL_deleteGame();
                break;
        }


        $this->smarty->assign(array(
            "content" => $content,
        ));

        $this->smarty->display("index.tpl");
    }

    /**
     * Template: add game
     *
     * @return string
     */
    public function addView(): string
    {
        $this->smarty->assign(array(
            "task" => "add",
        ));

        return $this->smarty->fetch("ranking/insertMatch.tpl");
    }

    /**
     * Template: show Ranking
     *
     * @param boolean $print
     * @return string
     */
    public function listView(bool $print=false): string
    {
        $stats  = $this->getRankingGroupedByDate();
        $labels = implode(",", array_map(array($this, "add_quotes"), $stats[0]));

        $this->smarty->assign(array(
            "ranking"     => $this->getRanking(),
            "games"       => $this->getGames(),
            "labels"      => $labels,
            "options"     => $stats[1],
            "print"       => $print,
            "stats"       => $this->prgElementRanking->getSettingBool("RANKING_STATS_ENABLE"),
        ));

        if ($print == true) {
            return $this->smarty->fetch("ranking/list.tpl");
        }
        return $this->smartyFetchWrap("ranking/list.tpl");
    }

    public function generatePdfView(): void
    {
        try {
            ob_start();

            // load Options
            $options = new \Dompdf\Options();
            $options->set("defaultFont", "Helvetica");
            $dompdf = new \Dompdf\Dompdf($options);
            // get css
            $css     = file_get_contents($this->cssPrint);

            // get content
            $content = $this->listView(true);
            $content = sprintf("<html><head><style><!-- %s --></style></head><body>%s</body></html>", $css, $content);

            $dompdf->loadHtml($content);
            $dompdf->setPaper("A4", "portrait");
            $dompdf->getCanvas()->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", 10, array(0,0,0));
            $dompdf->render();

            // set name & download file
            $filename = sprintf("%s_%s.pdf", "ranking", date("d.m.y (H:i)"));
            $dompdf->stream($filename, array("Attachment" => false));
        }
        catch (\Exception $e)
        {
            $this->prgElementRanking->log("Ranking", "", "", "PDF-GEN");
        }
    }

    /**
     * Template: Delete a game
     *
     * @return string
     */
    private function TMPL_deleteGame(): string
    {
        if (!$this->id) {
            $this->prgElementRanking->customRedirectArray(array("page" => "ranking.php"));
        }
        // get Game Data
        $gameData  = $this->brdb->getGameById($this->id);
       
        $gameData["sets"] = $this->SetUnSerialize($gameData["sets"]);
        $this->smarty->assign(array(
            "game"     => $gameData,
            "linkBack" => $this->prgElementRanking->linkTo(array("page" => __FILE__)),
        ));

        return $this->smarty->fetch("ranking/delete.tpl");
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
                $dataSet["playerLink"] = $this->prgElementRanking->linkTo(array("page" => "player.php", "id" => $dataSet["playerId"]));
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
                $dataSet["sets"] = $this->SetUnSerialize($dataSet["sets"]);
                // delete link
                $dataSet["deleteLink"] = $this->prgElementRanking->linkTo(array("page" => "ranking.php", "action" => "delete", "id" => $dataSet["gameId"]));
                // link user
                $dataSet["playerLink"] = $this->prgElementRanking->linkTo(array("page" => "user.php", "id" => $dataSet["playerId"]));
                // link oppenent
                $dataSet["opponentLink"] = $this->prgElementRanking->linkTo(array("page" => "user.php", "id" => $dataSet["opponentId"]));

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
                $dates[] = $dataSet["gamedate"];
                $games[] = $dataSet["games"];
            }
        }

        return array($dates, $games);
        unset($dates, $data, $games, $dataSet);
    }


    

    private function SetUnSerialize(array $sets): string
    {
        return implode(" - ", unserialize($sets));
        unset($sets);
    }

    private function add_quotes($str) {
        return sprintf("\"%s\"", $str);
    }
}

