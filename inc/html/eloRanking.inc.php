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
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgEloRanking.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

// libary: dompdf
require_once $_SERVER['BASE_DIR'] .'/vendor/autoload.php';


use Dompdf\Dompdf;
use Dompdf\Options;


class EloRanking extends BrdbHtmlPage {
    private $prgElementEloRanking;

    private $vars;

    protected $smarty;

    private $cssPrint;


    public function __construct() {
        parent::__construct();

        $this->tools->secure_array($_GET);

        $this->cssPrint = $_SERVER['BASE_DIR'] .'/static/css/print.css';

        $this->prgElementEloRanking = new PrgPatternElementEloRanking();
        $this->prgElementEloRanking->__loadPattern($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementEloRanking);
    }

    public function processPage() {
        parent::processPage();
    }

    protected function htmlBody() {
        switch($this->tools->get("action")) {
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

    private function TMPL_addGame() {
      return $this->smarty->fetch('elo/add.tpl');
    }

    private function add_quotes($str) {
        return sprintf("'%s'", $str);
    }

    /**
     * Get Ranking
     */
    private function TMPL_showRanking($print=false) {
        $stats  = $this->getRankingGroupedByDate();
        $labels = implode(",", array_map(array($this, 'add_quotes'), $stats[0]));

        $this->smarty->assign(array(
            'ranking'     => $this->getRanking(),
            'games'       => $this->getGames(),
            'labels'      => $labels,
            'options'     => $stats[1],
            'print'       => $print,
            'stats'       => $this->tools->getIniValue('eloRanking')['stats'],
        ));

        return $this->smarty->fetch('elo/list.tpl');
    }

    /**
     *  delete a game
     */
    private function TMPL_deleteGame() {
        $id = $this->tools->get('id') > 0 ? $this->tools->get('id') : '';
        if (! $id) {
            $this->tools->customRedirect(array('page' => 'eloRanking.php'));
        }

        $res  = $this->brdb->statementGetEloGameById($id);
        if (! $this->brdb->hasError() ) {
            $data = $res->fetch_assoc();
            $data['sets'] = $this->convertSets($data['sets']);
            $this->smarty->assign(array(
                'game'     => $data,
                'linkBack' => $this->tools->link(array('page' => __FILE__)),
            ));
        }

        return $this->smarty->fetch('elo/delete.tpl');
    }

    /**
     * get Ranking
     */
    private function getRanking() {
      $res  = $this->brdb->statementGetEloRanking();
      $data = array();
      if (! $this->brdb->hasError() ) {
            $rank = 1;
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['playerLink'] = $this->tools->linkTo(array('page' => 'user.php', 'id' => $dataSet['userId']));
                $data[$rank++] = $dataSet;
            }
        }

      return $data;
    }

    /**
     *  get Games
     */
    private function getGames() {
        $res  = $this->brdb->statementGetEloMatches();
        $data = array();
        if (! $this->brdb->hasError() ) {
            $rank = 1;
            while ($dataSet = $res->fetch_assoc()) {
                // sets
                $dataSet['sets'] = $this->convertSets($dataSet['sets']);
                // delete link
                $dataSet['deleteLink'] = $this->tools->linkTo(array('page' => 'eloRanking.php', 'action' => 'delete', 'id' => $dataSet['gameId']));
                // link user
                $dataSet['playerLink'] = $this->tools->linkTo(array('page' => 'user.php', 'id' => $dataSet['playerId']));
                // link oppenent
                $dataSet['opponentLink'] = $this->tools->linkTo(array('page' => 'user.php', 'id' => $dataSet['opponentId']));

                $data[] = $dataSet;
            }
        }

        return $data;
    }

    private function getRankingGroupedByDate() {
      $res   = $this->brdb->statementGetEloMatchesGroupedByDate();
      $dates = array();
      $games = array();

      if (! $this->brdb->hasError() ) {
          while ($dataSet = $res->fetch_assoc()) {
            $dates[] = $dataSet['gamedate'];
            $games[] = $dataSet['games'];
          }
        }

      return array($dates, $games);
    }


    private function downloadPDF() {
      ob_start();

      // load Options
      $options = new Options();
      $options->set('defaultFont', 'Helvetica');
      $dompdf = new Dompdf($options);
      // get css
      $css     = file_get_contents($this->cssPrint);

      // get content
      $content = $this->TMPL_showRanking(true);
      $content = sprintf('<html><head><style><!-- %s --></style></head><body>%s</body></html>', $css, $content);

      $dompdf->loadHtml($content);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->getCanvas()->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0,0,0));
      $dompdf->render();

      // set name & download file
      $filename = sprintf("%s_%s.pdf", "ranking", date("d.m.y (H:i)"));
      $dompdf->stream($filename, array("Attachment" => false));
    }

    private function convertSets($sets) {
        return implode(" - ", unserialize($sets));

    }




}
?>
