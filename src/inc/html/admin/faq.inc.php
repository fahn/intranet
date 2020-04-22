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
require_once $path .'/brdbHtmlPage.inc.php';

require_once BASE_DIR .'/inc/logic/prgFaq.inc.php';
require_once BASE_DIR .'/inc/html/admin/category.inc.php';

class BrdbHtmlAdminFaqPage extends BrdbHtmlPage
{
    private PrgPatternElementFaq $prgPatternElementFaq;
    private BrdbHtmlAdminCategoryPage $category;

    private string $_page = "";


    public function __construct(?string $page = null)
    {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }

        $this->prgPatternElementFaq = new PrgPatternElementFaq($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementFaq);

        # load links
        $links = array(
            'add' => $this->prgPatternElementFaq->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->prgPatternElementFaq->linkTo(array('page' => $this->_page, 'action' => 'add')),
        );

        $this->smarty->assign('links', $links);

        // load categories
        $this->category = new BrdbHtmlAdminCategoryPage();
    }


    public function htmlBody() {
        switch ($this->action) {
            case 'add':
                $content = $this->loadContentAddEdit();
                break;

            case 'edit':
                $content = $this->loadContentAddEdit();
                break;

            case 'delete':
                $content = $this->TMPL_delete();
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
        $this->smarty->assign(array(
            'FaqList'      => $this->loadList(),
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
                $dataSet['editLink']   = $this->prgPatternElementFaq->linkTo(array('page' => $this->_page, 'action' => 'edit', 'id' => $dataSet['faqId']));
                $dataSet['deleteLink'] = $this->prgPatternElementFaq->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => $dataSet['faqId']));

                $data[] = $dataSet; //new User($dataSet);
            }
        }
        return $data;
        unset($data, $dataSet, $faqList);
    }

  private function loadContentAddEdit(): string
  {
    $this->smarty->assign(array(
        'action'                 => $this->action,
        'FaqCategoryHtmlOptions' => $this->category->getCategories(),
        'item'                   => $this->getFaqById($this->id),
    ));
    return $this->smarty->fetch('faq/adminUpdate.tpl');
  }

    /**
     * get Faq by ID
     *
     * @param integer $id
     * @return array
     */
    private function getFaqById(int $id): array
    {
        return $id > 0 ? $this->brdb->statementGetFAQById($id) : array();
    }


    /**
     * Delete FAQ by ID
     *
     * @return string
     */
    private function TMPL_delete(): string
    {
        $this->smarty->assign(array(
            'item' => $this->getFaqById($this->id),
        ));

        return $this->smarty->fetch('faq/adminDelete.tpl');

    }
}

