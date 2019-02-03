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

    private $cssPrint = $_SERVER['BASE_DIR'] .'/static/css/print.css';


    public function __construct() {
        parent::__construct();

        $this->tools->secure_array($_GET);

        $this->prgElementEloRanking = new PrgPatternElementEloRanking();
        $this->prgElementEloRanking->__loadPattern($this->prgPatternElementLogin);
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

          case "renewRanking":
          // @TODO: Security
            $this->newRanking();
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

    /**
     * Get Ranking
     */
    private function TMPL_showRanking($print=false) {
        $this->smarty->assign(array(
            'ranking'    => $this->getRanking(),
            'print'      => $print,
        ));

        return $this->smarty->fetch('elo/list.tpl');
    }

    private function getRanking() {
        $res  = $this->brdb->statementGetEloRanking();
        $data = array();
        if (! $this->brdb->hasError() ) {
            $rank = 1;
            while ($dataSet = $res->fetch_assoc()) {
              $data[$rank++] = $dataSet;
            }
          }

        return $data;
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
      $dompdf->render();

      // set name & download file
      $filename = sprintf("%s_%s.pdf", "ranking", date("d.m.y (H:i)"));
      $dompdf->stream($filename, array("Attachment" => false));
    }

    private function newRanking() {
      $this->prgElementEloRanking->newRanking();

      $this->tools->customRedirect(array('page' => 'eloRanking.php'));
    }
}
?>
