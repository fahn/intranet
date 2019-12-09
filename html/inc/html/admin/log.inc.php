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

include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminShowLog extends BrdbHtmlPage {
  //
  private $_page = "";

    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }
    }


    public function htmlBody() {
        $action = $this->tools->get("action");

        switch ($action) {
            default:
                $content = $this->loadContent();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }

    private function loadContent() {
        $this->smarty->assign('logList', $this->getLogs());

        return $this->smarty->fetch('log/list.tpl');
    }

    private function getLogs() {
        $data = array();
        $res = $this->brdb->statementGetAllLogs(); #($min, $max);
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['logdata'] = unserialize($dataSet['logdata']);
                $data[] = $dataSet;
            }
        }

        return $data;
    }

}
