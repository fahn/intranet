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
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

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
    
    private function TMPL_showCategory($id) {
        $this->lcontent = "DAS";
    }

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
        $res = $this->brdb->statementGetAllFaq();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                if (isset($dataSet['categoryId']) && !array_key_exists($dataSet['categoryId'], $data) ) {
                    $data[$dataSet['categoryId']] = array('title' => $dataSet['categoryTitle'], 'rows' => array());
                }
                $data[$dataSet['categoryId']]['rows'][] = $dataSet;
            }

            
        }
        #echo "<pre>";
        #die(print_r($data));

        return $data;
    }

    private function getFaqList() {
        $res = $this->brdb->statementGetAllFaq();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                // edit Link
                $data[] = $dataSet;
            }

            return $data;
        }

        return "";
    }

    private function getCategory() {
        $res = $this->brdb->statementGetCategoryAndCountItems();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                $data[] = $dataSet;
            }

            return $data;
        }

        return "";
    }

    private function getCategoryHtmlOptions() {
        $data = array();
        $tmp = $this->getCategory;
        while($dataSet = $tmp) {
            $data[$dataSet['categoryId']] = $dataSet['title'];
        }

        return $data;
    }
    
    private function getFaqByCategoryId($id) {
        $res = $this->brdb->statementGetFaqByCategoryId($id);
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                $data[] = $dataSet;
            }
        }
        return $data;
    }

}
?>
