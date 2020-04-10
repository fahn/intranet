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
include_once('brdbHtmlPage.inc.php');
include_once(BASE_DIR .'/inc/logic/prgFaq.inc.php');

class BrdbHtmlFaq extends BrdbHtmlPage 
{
    private PrgPatternElementFaq $prgPatternElementFaq;

    public function __construct()
    {
        parent::__construct();

        $this->prgPatternElementFaq = new PrgPatternElementFaq($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementFaq);
       
        // list
        $links = array(
            'list'     => $this->prgPatternElementFaq->linkTo(array('page' => 'faq.php', 'action' => 'list')),
        );

        $this->smarty->assign('links', $links);
    }

    public function processPage(): void
    {
        parent::processPage();
    }

    protected function htmlBody(): void
    {
        $this->smarty->assign(array(
            'content' => $this->TMPL_showFAQ(),
        ));

        $this->smarty->display('index.tpl');
    }

    private function TMPL_showFAQ(): string
    {
        $this->smarty->assign(array(
            'FaqGroupedByCategory' => $this->getFaqGroupedByCategory(),
            'FaqList'              => $this->getFaqGroupedByCategory(),
            'Categories'           => $this->getCategories(),
            //'CategoryHtmlOptions'  => $this->getCategoryHtmlOptions(),
        ));


        return $this->smarty->fetch('faq/list.tpl');
    }

    private function getFaqGroupedByCategory(): array
    {
        $data = array();
        $faqList = $this->brdb->statementGetAllFaq();
        
        if (isset($faqList) && !empty($faqList) && !is_array($faqList)) 
        {   

            foreach ($faqList as $dataSet) 
            {
                if (isset($dataSet['categoryId']) && !array_key_exists($dataSet['categoryId'], $data)) 
                {
                    $data[$dataSet['categoryId']] = array('title' => $dataSet['categoryTitle'], 'rows' => array());
                }
                $data[$dataSet['categoryId']]['rows'][] = $dataSet;
            }
        }

        return $data;
        unset($data, $faqList, $dataSet);
    }

    private function getFaqList(): array
    {
        $data = array();
        $faqList = $this->brdb->statementGetAllFaq();
        
        if (isset($faqList) && !empty($faqList) && !is_array($faqList)) {
            foreach ($faqList as $dataSet) {
                // edit Link
                $dataSet['editLink']   = "#". $dataSet['faqId'];
                $dataSet['deleteLink'] = "#". $dataSet['faqId'];
                $data[] = $dataSet;
            }
        }

        return $data;
        unset($data, $dataSet, $faqList);
    }

    private function getCategories(): array
    {
        return $this->brdb->statementGetCategoryAndCountItems();
    }

    private function getCategoryHtmlOptions(): array
    {
        $data = array();
        $categoryList = $this->getCategories;
        if (isset($categoryList) && !empty($categoryList)) 
        {
            foreach ($categoryList as $dataSet) 
            {
                $data[$dataSet['categoryId']] = $dataSet['title'];
            }
        }

        return $data;
        unset($data, $categoryList);
    }
    
    private function getFaqByCategoryId(int $id): array
    {
        return $this->brdb->statementGetFaqByCategoryId($id);
    }
}
?>
