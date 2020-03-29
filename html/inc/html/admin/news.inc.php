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
declare(strict_types=1);
$path=dirname(dirname(__FILE__));
require_once($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgNews.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

include_once BASE_DIR .'/inc/html/admin/category.inc.php';

class BrdbHtmlAdminNewsPage extends BrdbHtmlPage {
  private $prgPatternElementNews;

  //
  private $_page = "";

  const MAX_ENTRIES = 50;

    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }

        # load links
        $links = array(
            'add' => $this->tools->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->tools->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'home' => $this->tools->linkTo(array('page' => $this->_page)),
        );

        $this->smarty->assign('links', $links);

        $this->prgPatternElementNews = new prgPatternElementNews($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementNews);
    }


    public function htmlBody() {
        $action = $this->prgPatternElementNews->issetGetVariable("action") ? $this->prgPatternElementNews->getGetVariable("action") : "";
        $id     = $this->prgPatternElementNews->issetGetVariable("id") ? $this->prgPatternElementNews->getGetVariable("id") : 0;

        switch ($action) {
            case 'add':
                $content = $this->TMPL_update($action, $id);
                break;

            case 'edit':
                $content = $this->TMPL_update($action, $id);
                break;

            case 'delete':
                $content = $this->TMPL_delete($id);
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

    private function TMPL_update(String $action, int $id) {
        // load categories
        $cats = new BrdbHtmlAdminCategoryPage();

        $this->smarty->assign(array(
            'action'                 => $action,
            'categoryHtmlOptions'    => $cats->getCategories(),
            'item'                   => $this->getNewsById($id),
        ));
        return $this->smarty->fetch('news/adminUpdate.tpl');
    }

    private function loadList() {
        $data = array();
        $newsList = $this->brdb->statementGetAllNews(); #($min, $max);
        if (isset($newsList) && !empty($newsList)) {
            foreach ($newsList as $dataSet) {
                // links
                $dataSet['editLink']   = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $dataSet['newsId']));
                $dataSet['deleteLink'] = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['newsId']));

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
    private function TMPL_delete(int $id) {

        $this->smarty->assign(array(
            'item' => $this->getNewsById($id),
        ));

        return $this->smarty->fetch('news/adminDelete.tpl');

    }
}

?>
