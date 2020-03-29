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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlTeam extends BrdbHtmlPage {


    public function __construct():void
    {
        parent::__construct();

        $this->tools->secure_array($_GET);
    }

    public function processPage():void
    {
        parent::processPage();
    }

    protected function htmlBody(): void
    {
        $content = $this->TMPL_showTeam();

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
        unset($content);
    }

    private function TMPL_showTeam()
    {
        $this->smarty->assign(array(
            'row'    => $this->getTeam(),
        ));

        return $this->smarty->fetch('team/list.tpl');
    }

    private function getTeam():array
    {
        $teamList = $this->brdb->getStaffList();
        $data = array();

        if (isset($teamList) && !empty($teamList)) 
        {
            foreach ($teamList as $dataSet) 
            {
                if(isset($dataSet['row']) && $dataSet['row'] > 0) 
                {
                    $data[$dataSet['row']][] = $dataSet;
                }
            }
        }

        return $data;
        unset($data, $dataSet, $teamList);
    }

}
?>
