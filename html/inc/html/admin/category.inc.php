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
$path=dirname(dirname(__FILE__));
require_once($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgCategory.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminCategoryPage extends BrdbHtmlPage {
  private $prgPatternElementCategory;

  //
  private $_page  = "";
  private $_table = "Category";

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

        $this->prgPatternElementCategory = new prgPatternElementCategory($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementCategory);
    }


    public function htmlBody() {
        $action = $this->tools->get("action");
        $id     = $this->tools->get("id");

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
            'list'      => $this->loadCategoryList(),
        ));
        return $this->smarty->fetch('category/adminList.tpl');
    }


    private function loadCategoryList() {
        $data = array();
        $categoryList = $this->brdb->adminStatementGetAllCategories(); #($min, $max);
        if (isset($categoryList) && !empty($categoryList)) {
            foreach ($categoryList as $dataSet) {
                // links
                $dataSet['editLink']   = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'edit',   'id' => $dataSet['categoryId']));
                $dataSet['deleteLink'] = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['categoryId']));

                $data[] = $dataSet; //new User($dataSet);
            }
        }
        return $data;
        unset($data, $categoryList, $dataSet);
    }

    private function TMPL_update($action, $id) {
        $this->smarty->assign(array(
            'action'                 => $action,
            'categoryHtmlOptions'    => $this->getCategories(),
            'item'                   => $this->getNewsById($id),
        ));
        return $this->smarty->fetch('category/adminUpdate.tpl');
    }

    private function getNewsById(int $id) {
        return $id > 0  ? $this->brdb->statementGetNewsById($id) : array();
    }

    public function getCategories() {
        $data = array();
        $categoryList = $this->brdb->statementGetAllCategories();
        if (!isset(categoryList) && !empty(categoryList) {
            foreach ($categoryList as $dataSet) {
                if ($dataSet['pid'] > 0) {
                    if (!array_key_exists($dataSet['pid'], $data)) {
                        $data[$dataSet['pid']]['records'] = array();
                    }
                    $data[$dataSet['pid']]['records'][$dataSet['categoryId']] =  array('title' => $dataSet['title'], 'records' => array());
                } else {
                    $data[$dataSet['categoryId']] = array('title' =>$dataSet['title']);
                }
            }
        }

        return $this->reformHtmlOptions($data);
    }

    private function reformHtmlOptions($dataArr, $rec = 0) {
        if (!is_array($dataArr) || count($dataArr) == 0 || $rec >= 99) {
            return;
        }
        $data = array();

        foreach ($dataArr as $key => $value) {
            $pre = str_repeat("-", $rec);
            $pre .= strlen($pre) > 0 ? ">" : "";
            $title = $pre . $value['title'];
            $data[$key] = $title;
            if (is_array($value['records']) && count($value['records']) > 0) {
                $tmp = $this->reformHtmlOptions($value['records'], $rec+1);
                if(is_array($tmp)) {
                    $data += $tmp;
                }
            }
        }

        return $data;
    }

    /* DELETE */
    private function TMPL_delete($id) {

        $this->smarty->assign(array(
            'item' => $this->getNewsById($id),
        ));

        return $this->smarty->fetch('category/adminDelete.tpl');

    }
}
?>
