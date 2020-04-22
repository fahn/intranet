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
require_once "brdbHtmlPage.inc.php";

class BrdbHtmlAdminShowLog extends BrdbHtmlPage
{
    //
    private $_page = "";

    public function __construct(string $page = null)
    {
        parent::__construct();
        $this->_page = $page != null ?: $page;
    }


    public function htmlBody(): void
    {
        switch ($this->action)
        {
            default:
                $content = $this->_loadContent();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }

    private function _loadContent(): string
    {
        $this->smarty->assign('logList', $this->_getLogs());

        return $this->smarty->fetch('log/list.tpl');
    }

    private function _getLogs(): array
    {
        $data = array();
        $logList = $this->brdb->statementGetAllLogs();
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
