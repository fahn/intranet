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
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

// libary: dompdf
require_once $_SERVER['BASE_DIR'] .'/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;


class EloRanking extends BrdbHtmlPage {
    private $vars;
    protected $smarty;

    private $cssPrint = __PFAD__ .'/static/css/print.css';


    public function __construct() {
        parent::__construct();

        $this->tools->secure_array($_GET);
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
        $arr = array('Hubert', 'Theo', 'Spargel', 'Lauch', 'Bomba');
        $rank = 1;
        $points = 1000;
        foreach ( $arr as &$value) {
          $data[] = array(
            'rank'   => $rank++,
            'name'   => $value,
            'points' => $points,
            'time'   => date("d.m.Y H:i"),
          );
          $points -= 2;
        }

        return $data;

        $res = $this->brdb->selectStaffList();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
              #error_log(print_r($dataSet));
                if(isset($dataSet['row']) && $dataSet['row'] > 0) {
                    $data[$dataSet['row']][] = $dataSet;
                }
            }

            return $data;
        }

        return "";
    }

    private function downloadPDF() {
      ob_start();


      $options = new Options();
      $options->set('defaultFont', 'Helvetica');
      $dompdf = new Dompdf($options);
      // instantiate and use the dompdf class
      $dompdf = new Dompdf();
      // get css
      $css     = file_get_contents($this->cssPrint);

      // get content
      $content = $this->TMPL_showRanking(true);
      $content = sprintf('<html><head><style><!-- %s --></style></head><body>%s</body></html>', $css, $content);

      $dompdf->loadHtml($content);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $filename = sprintf("%s_%s.pdf", "ranking", date("d.m.y (H:i)"));
      $dompdf->stream($filename, array("Attachment" => false));
    }
}
?>
