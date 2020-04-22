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
$path=dirname(dirname(__FILE__));
require_once $path ."/brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgNews.inc.php";
require_once BASE_DIR ."/inc/html/admin/category.inc.php";

class BrdbHtmlAdminNewsPage extends BrdbHtmlPage {
  private $prgPatternElementNews;

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


    private function TMPL_list() {
        $this->smarty->assign(array(
            'newsList'      => $this->loadList(),
        ));

        return $this->smarty->fetch('news/adminList.tpl');
    }

    /**
     * update News
     *
     * @return string
     */
    private function TMPL_update(): string
    {
        // load categories
        $cats = new BrdbHtmlAdminCategoryPage();

        $this->smarty->assign(array(
            'action'                 => $this->action,
            'item'                   => $this->getNewsById($this->id),
        ));
        return $this->smarty->fetch('news/adminUpdate.tpl');
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


