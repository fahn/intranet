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
    private $vars;

    public function __construct() {
        parent::__construct();


        $this->tools->secure_array($_GET);

        // list
        $links = array(
            'list'     => $this->tools->linkTo(array('page' => 'faq.php', 'action' => 'list')),
            'category' => '#',
        );
        $this->smarty->assign('links', $links);
    }

    public function processPage() {
        parent::processPage();
    }

    protected function htmlBody() {
        $content = $this->TMPL_showFAQ();

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function TMPL_showFAQ() {
        $this->smarty->assign(array(
            'FaqGroupedByCategory' => $this->getFaqGroupedByCategory(),
            'FaqList'              => $this->getFaqList(),
            'Category'             => $this->getCategory(),
            'CategoryHtmlOptions'  => $this->getCategoryHtmlOptions(),
        ));

        return $this->smarty->fetch('faq/list.tpl');
    }

    private function getFaqGroupedByCategory() { 
        return array(); 
        $res = $this->brdb->statementGetAllFaq();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                if (isset($dataSet['categoryId']) && !array_key_exists($dataSet['categoryId'], $data)) {
                    $data[$dataSet['categoryId']] = array('title' => $dataSet['categoryTitle'], 'rows' => array());
                }
                $data[$dataSet['categoryId']]['rows'][] = $dataSet;
            }

            return $data;
        }

        return "";
    }

    private function getFaqList() {
        $res = $this->brdb->statementGetFAQs();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                // edit Link
                $dataSet['editLink'] = "#". $dataSet['faqId'];
                $dataSet['deleteLink'] = "#". $dataSet['faqId'];
                $data[] = $dataSet;
            }

            return $data;
        }

        return "";
    }

    private function getCategory() {
        $res = $this->brdb->statementGetAllCategories();
        if (!$this->brdb->hasError()) {
            $data = array();
            while ($dataSet = $res->fetch_assoc()) {
                #$data[$dataSet['categoryId']] = $dataSet['title'];
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

}
?>
