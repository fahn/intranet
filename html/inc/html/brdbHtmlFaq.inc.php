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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlFaq extends BrdbHtmlPage {
    private $lcontent;

    public function __construct() {
        parent::__construct();

        $this->tools->secure_array($_GET);
        
        // list
        $links = array(
            'list'     => $this->tools->linkTo(array('page' => 'faq.php', 'action' => 'list')),
        );
        $this->smarty->assign('links', $links);
    }

    public function processPage() {
        parent::processPage();
    }

    protected function htmlBody() {
        $this->TMPL_showFAQ();
        
        $this->smarty->assign(array(
            'content' => $this->lcontent,
        ));
        $this->smarty->display('index.tpl');
    }
    
    /*
    private function TMPL_showCategory() {
        $this->lcontent = "DAS";
    }
    */

    private function TMPL_showFAQ() {
        $this->smarty->assign(array(
            'FaqGroupedByCategory' => $this->getFaqGroupedByCategory(),
            'FaqList'              => $this->getFaqGroupedByCategory(),
            'Categories'             => $this->getCategory(),
            //'CategoryHtmlOptions'  => $this->getCategoryHtmlOptions(),
        ));


        $this->lcontent = $this->smarty->fetch('faq/list.tpl');
    }

    private function getFaqGroupedByCategory() {
        $data = array();
        $faqList = $this->brdb->statementGetFAQs();
        
        if (isset($faqList) && !empy($faqList))) {    
            foreach ($faqList as $dataSet) {
                if (isset($dataSet['categoryId']) && !array_key_exists($dataSet['categoryId'], $data)) {
                    $data[$dataSet['categoryId']] = array('title' => $dataSet['categoryTitle'], 'rows' => array());
                }
                $data[$dataSet['categoryId']]['rows'][] = $dataSet;
            }
        }

        return $data;
        unset($data, $faqList, $dataSet);
    }

    private function getFaqList() {
        $data = array();
        $faqList = $this->brdb->statementGetFAQs();
        
        if (isset($faqList) && !empy($faqList))) {    
            foreach ($faqList as $dataSet) {
                // edit Link
                $dataSet['editLink'] = "#". $dataSet['faqId'];
                $dataSet['deleteLink'] = "#". $dataSet['faqId'];
                $data[] = $dataSet;
            }
        }

        return $data;
        unset($data, $dataSet, $faqList);
    }

    private function getCategory() {
        return $this->brdb->statementGetCategoryAndCountItems();
    }

    private function getCategoryHtmlOptions() {
        $data = array();
        $categoryList = $this->getCategory;
        if (isset($categoryList) && !empty($categoryList)) {
            foreach ($categoryList as $dataSet) {
                $data[$dataSet['categoryId']] = $dataSet['title'];
            }
        }

        return $data;
        unset($data, $categoryList);
    }
    
    private function getFaqByCategoryId($id) {
        return $this->brdb->statementGetFaqByCategoryId($id);
    }
}
?>
