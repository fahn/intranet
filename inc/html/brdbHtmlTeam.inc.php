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

include_once __PFAD__ .'/inc/html/brdbHtmlPage.inc.php';
include_once __PFAD__ .'/inc/logic/prgTournament.inc.php';
include_once __PFAD__ .'/inc/logic/prgPattern.inc.php';
include_once __PFAD__ .'/inc/logic/tools.inc.php';

class BrdbHtmlTeam extends BrdbHtmlPage {
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
        $content = $this->TMPL_showTeam();

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function TMPL_showTeam() {
        $this->smarty->assign(array(
            'row'    => $this->getTeam(),
        ));

        return $this->smarty->fetch('team/list.tpl');
    }



    private function getTeam() {
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

}
?>
