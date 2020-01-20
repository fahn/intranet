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
require($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgFaq.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';
include_once BASE_DIR .'/inc/html/category.inc.php';

class BrdbHtmlAdminFaqPage extends BrdbHtmlPage {
  private $prgPatternElementFaq;

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

        $this->category = new BrdbHtmlAdminCategoryPage();
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
        $faqList = $this->brdb->statementGetAllFaq(); #($min, $max);
        if (isset($faqList) && !empty($faqList)) {
            foreach ($faqList as $dataSet) {
                // links
                $dataSet['editLink']   = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $dataSet['faqId']));
                $dataSet['deleteLink'] = $this->tools->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['faqId']));

                $data[] = $dataSet; //new User($dataSet);
            }
        }
        return $data;
        unset($data, $dataSet, $faqList);
    }

  private function loadContentAddEdit($action, $id) {
    $this->smarty->assign(array(
        'action'                 => $action,
        'FaqCategoryHtmlOptions' => $this->category->getCategories(),
        'item'                   => $this->getFaqById($id),
    ));
    return $this->smarty->fetch('faq/adminUpdate.tpl');
  }

  /** GET CLUB BY ID
    *
    */
    private function getFaqById(int $id) {
        return $id > 0 ? $this->brdb->statementGetFAQById($id) : array();
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
