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
namespace Badtra\Intranet\Html\Admin;

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementNews;

class AdminNewsPage extends BrdbHtmlPage 
{
  private prgPatternElementNews $prgPatternElementNews;

  //
  private $_page = "";

  const MAX_ENTRIES = 50;

    public function __construct($page = null) {
        parent::__construct();

        $this->_page = $page != null ?: $page;

        $this->prgPatternElementNews = new prgPatternElementNews($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementNews);

        # load links
        $links = array(
            'add' => $this->prgPatternElementNews->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->prgPatternElementNews->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'home' => $this->prgPatternElementNews->linkTo(array('page' => $this->_page)),
        );

        $this->smarty->assign('links', $links);
    }


    public function htmlBody()
    {
        switch ($this->action)
        {
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


    public function listView() {
        $this->smarty->assign(array(
            'newsList'      => $this->loadList(),
        ));

        return $this->smarty->fetch('news/admin/list.tpl');
    }

    /**
     * update News
     *
     * @return string
     */
    public function updateView(): string
    {
        // load categories
        $cats = new CategoryAdminPage();

        $this->smarty->assign(array(
            'action'                 => 'update',
            'item'                   => $this->getNewsById($this->id),
        ));
        return $this->smarty->fetch('news/admin/update.tpl');
    }


    public function addView(): string {
        $this->smarty->assign(array(
            'action'                 => 'add'
        ));
        return $this->smarty->fetch('news/admin/update.tpl');
    }

    private function loadList() {
        $data = array();
        $newsList = $this->brdb->statementGetAllNews(); #($min, $max);
        if (isset($newsList) && !empty($newsList)) {
            foreach ($newsList as $dataSet) {
                // links
                $dataSet['editLink']   = $this->prgPatternElementNews->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $dataSet['newsId']));
                $dataSet['deleteLink'] = $this->prgPatternElementNews->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['newsId']));

                $data[] = $dataSet;
            }
        }

        return $data;
        unset($data, $dataSet, $newsList);
    }

    private function getNewsById(int $id) {
        return $id > 0 ? $this->brdb->statementGetNewsById($id) : array();
    }

    /**
     * Template: Delete view
     *
     * @param int $id
     * @return void
     */
    private function TMPL_delete() {

        $this->smarty->assign(array(
            'item' => $this->getNewsById($this->id),
        ));

        return $this->smarty->fetch('news/adminDelete.tpl');

    }
}


