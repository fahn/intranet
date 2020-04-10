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
$path=dirname(dirname(__FILE__));
require($path .'/brdbHtmlPage.inc.php');

class BrdbHtmlAdminShowLog extends BrdbHtmlPage {
  //
  private $_page = "";

    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }
    }


    public function htmlBody(): void
    {
        switch ($this->action) {
            default:
                $content = $this->loadContent();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }

    private function loadContent(): string
    {
        $this->smarty->assign('logList', $this->getLogs());

        return $this->smarty->fetch('log/list.tpl');
    }

    private function getLogs(): array
    {
        $data = array();
        $logList = $this->brdb->statementGetAllLogs(); #($min, $max);
        if (isset($logList) && !empty($logList)) 
        {
            foreach ($logList as $dataSet) 
            {
                $dataSet['logdata'] = unserialize($dataSet['logdata']);
                $data[] = $dataSet;
            }
        }

        return $data;
    }

}
