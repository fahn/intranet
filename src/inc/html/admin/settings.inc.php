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
require_once "/brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgSettings.inc.php";

class BrdbHtmlAdminSettings extends BrdbHtmlPage
{
    private $_page = "adminSettings.php";
    private PrgPatternElemenSettings $prgPatternElementSettings;

    public function __construct(?string $page)
    {
        parent::__construct();

        // set page
        $page = $page != null ?: $page;

        $this->prgPatternElementSettings = new PrgPatternElemenSettings($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSettings);
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
        return $this->smarty->fetch('settings/AdminList.tpl');
    }

    private function TMPL_update(): string
    {
        $item = $this->brdb->getSettingById($this->id);
        $this->smarty->assign(array(
            'dataTypeOptions' => array('bool' => "Boolean (Y/N)", "string" => "string", "array" => "Datenfeld"),
            'item'            => $item,
        ));
        return $this->smarty->fetch('settings/AdminUpdate.tpl');
    }

    private function TMPL_delete(): void
    {

    }

    private function loadList() {
        $data = array();
        $list = $this->brdb->loadAllSettings();
        if (isset($list) && !empty($list))
        {
            foreach ($list as $item)
            {
                // links
                $item['editLink']   = $this->prgPatternElementSettings->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $item['id']));
                $item['deleteLink'] = $this->prgPatternElementSettings->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $item['id']));
                try
                {
                    if (isset($item['dataType']) && $item['dataType'] == "array" && isset($item['value']) && strlen($item['value']) > 0)
                    {
                        $item['value'] = implode(", ", unserialize($item['value']));
                    }
                }
                catch (Exception $e)
                {
                    $this->prgPatternElementSettings->log($this->__TABLE__, sprintf("Cannot transform settings array: %s, %s", $item['value'], $e->getMessage()), "", "DB-QUERY");
                    $item['value'] = "-";
                }
               
                $data[] = $item;
            }
        }

        return $data;
    }
}

