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

include_once BASE_DIR .'/inc/logic/prgSettings.inc.php';

class BrdbHtmlAdminSettings extends BrdbHtmlPage 
{
    private $_page = "adminSettings.php";

    public function __construct(?string $page) 
    {
        parent::__construct();

        // set page
        $page = $page != null ?: $page;

        $this->PrgPatternElemenSettings = new PrgPatternElemenSettings($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementFaq);
    }

    public function processPage() 
    {
        parent::processPage();
    }


    protected function htmlBody() 
    {
        switch ($this->action) {
            case 'add':
                $content = $this->TMPL_update();
                break;

            case 'edit':
                $content = $this->TMPL_update();
                break;

            case 'delete':
                $content = $this->TMPL_delete();
                break;

            default:
                $content = $this->TMPL_list();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function TMPL_list(): string
    {
        $this->smarty->assign(array(
            'list'      => $this->loadList(),
        ));
        return $this->smarty->fetch('settings/Adminlist.tpl');
    }

    private function loadList() {
        $data = array();
        $list = $this->brdb->loadAllSettings();
        if (isset($list) && !empty($list)) {
            foreach ($list as $item) {
                // links
                $item['editLink']   = $this->PrgPatternElemenSettings->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $item['id']));
                $item['deleteLink'] = $this->PrgPatternElemenSettings->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $item['id']));
                try 
                {
                    if ($item['dataType'] == "array" && isset($item['value']) && strlen($item['value']) > 0) 
                    {
                        $item['value'] = implode(", ", unserialize($item['value']));
                    }
                } 
                catch (Exception $e) 
                {
                    $this->PrgPatternElemenSettings->log($this->__TABLE__, sprintf("Cannot transform settings array: %s, %s", $item['value'], $e->getMessage()), "", "");
                    $item['value'] = "-";
                }
                
                $data[] = $item;
            }
        }

        return $data;
    }

    private function TMPL_update(): void
    {

    }

    private function TMPL_delete(): void 
    {

    }
}
?>
