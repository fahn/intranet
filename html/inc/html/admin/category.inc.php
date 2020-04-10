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
require_once($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgCategory.inc.php';

class BrdbHtmlAdminCategoryPage extends BrdbHtmlPage {
    // pattern
    private $prgPatternElementCategory;

    private $_page  = "";
    private $_table = "Category";

    const MAX_ENTRIES = 50;

    public function __construct($page = null) {
        parent::__construct();

        $this->prgPatternElementCategory = new prgPatternElementCategory($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementCategory);

        if ($page != null) {
             $this->_page = $page;
        }


        # load links
        $links = array(
            'add'  => $this->prgPatternElementCategory->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->prgPatternElementCategory->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'home' => $this->prgPatternElementCategory->linkTo(array('page' => $this->_page)),
        );

        $this->smarty->assign('links', $links);
    }


    public function htmlBody(): void
    {
        switch ($this->action) 
        {
            case 'add':
            case 'edit':
                $content = $this->TMPL_update($this->action);
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

    /******************* VIEWS */

    private function TMPL_list() {
        $this->smarty->assign(array(
            'list'      => $this->loadCategoryList(),
        ));
        return $this->smarty->fetch('category/adminList.tpl');
    }

    private function TMPL_update(String $action) {
        $this->smarty->assign(array(
            'action'                 => $action,
        ));

        return $this->smarty->fetch('category/adminUpdate.tpl');
    }

    /**
     * Delete Category
     *
     * @return string
     */
    private function TMPL_delete(): string
    {
        $this->smarty->assign(array(
            'item' => $this->getCategoryById($this->id),
        ));

        return $this->smarty->fetch('category/adminDelete.tpl');
    }

    /***************** FUNCTIONS */

    private function loadCategoryList() {
        $data = array();
        $categoryList = $this->brdb->adminStatementGetAllCategories(); #($min, $max);
        if (isset($categoryList) && !empty($categoryList)) {
            foreach ($categoryList as $dataSet) {
                // links
                $dataSet['editLink']   = $this->prgPatternElementCategory->linkTo(array('page' => $this->_page, 'action' => 'edit',   'id' => $dataSet['categoryId']));
                $dataSet['deleteLink'] = $this->prgPatternElementCategory->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['categoryId']));

                $data[] = $dataSet;
            }
        }
        return $data;
        unset($data, $categoryList, $dataSet);
    }

    private function getCategoryById(int $id) {
        return $id > 0  ? $this->brdb->statementGetCategoryById($id) : array();
    }
}
?>
