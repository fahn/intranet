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

include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgFaq.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminFaqPage extends BrdbHtmlPage {
  private $prgPatternElementFaq;
  private $variable;
  private $countRows;

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
        );

        $this->smarty->assign('links', $links);

        $this->prgPatternElementFaq = new PrgPatternElementFaq($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementFaq);
    }


    public function htmlBody() {
        $action = $this->tools->get("action");
        $id     = $this->tools->get("id");

        switch ($action) {
            case 'add':
                $content = $this->loadContentAddEdit($action, $id);
                break;

            case 'edit':
                $content = $this->loadContentAddEdit($action, $id);
                break;

            case 'delete':
                $content = $this->TMPL_delete($id);
                break;

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
        #$page = $this->tools->get("page");
        #$page = isset($page) && is_numeric($page) && $page > 0 ? $page-1 : 0;
        $this->smarty->assign(array(
            'FaqList'      => $this->loadList(),
            #'pagination' => $this->getPageination(),
        ));
        return $this->smarty->fetch('faq/adminList.tpl');
    }


    private function loadList() {
        #$this->countRows = $this->brdb->statementGetAllFaq()->num_rows;
        #$max = self::MAX_ENTRIES*(1+$page);
        #$min = $max - self::MAX_ENTRIES;

        $data = array();
        $res = $this->brdb->statementGetAllFaq(); #($min, $max);
        if (!$this->brdb->hasError()) {
          while ($dataSet = $res->fetch_assoc()) {
            // links
            $dataSet['editLink']   = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $dataSet['faqId']));
            $dataSet['deleteLink'] = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['faqId']));

            $data[] = $dataSet; //new User($dataSet);


          }
        }
        return $data;
    }

  private function loadContentAddEdit($action, $id) {
    $this->smarty->assign(array(
        'action'                 => $action,
        'FaqCategoryHtmlOptions' => $this->getCategories(),
        'item'                   => $this->getFaqById($id),
    ));
    return $this->smarty->fetch('faq/adminUpdate.tpl');
  }

  /** GET CLUB BY ID
    *
    */
    private function getFaqById($id) {
      if(!is_numeric($id)) {
        return;
      }

        return $this->brdb->statementGetFAQById($id)->fetch_assoc();
    }

    private function getCategories() {
        $data = array();
        $res = $this->brdb->statementGetAllCategories();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
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
            'item' => $this->getFaqById($id),
        ));

        return $this->smarty->fetch('faq/adminDelete.tpl');

    }
}
?>
