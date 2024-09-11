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
namespace Badtra\Intranet\Html\Admin;

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementCategory;

class AdminCategoryPage extends BrdbHtmlPage
{

    // pattern
    private $prgPatternElementCategory;

    private $_page = "";

    const MAX_ENTRIES = 50;


    public function __construct($page = null)
    {
        parent::__construct();

        $this->prgPatternElementCategory = new PrgPatternElementCategory($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementCategory);

        $this->_page = $page != null ?: $page;

        // load links
        $links = [
            "add"  => $this->prgPatternElementCategory->linkTo(["page" => $this->_page, "action" => "add"]),
            "list" => $this->prgPatternElementCategory->linkTo(["page" => $this->_page, "action" => "add"]),
            "home" => $this->prgPatternElementCategory->linkTo(["page" => $this->_page]),
        ];

        $this->smarty->assign("links", $links);

    }//end __construct()

    /**
     * List Category
     *
     * @return string
     */
    public function listView()
    {
        $this->smarty->assign(
            [
                "list" => $this->loadCategoryList(),
            ]
        );
        return $this->smarty->fetch("category/admin/list.tpl");

    }//end TMPL_list()


    /**
     * Add Category
     *
     * @return string
     */
    public function addView()
    {
        $this->smarty->assign([
            "action" => "add"
        ]);

        return $this->smarty->fetch("category/admin/update.tpl");

    }


    /**
     * Update Category
     *
     * @return string
     */
    public function updateView(int $id): string
    {
        $this->smarty->assign([
            "item" => $this->getCategoryById($id),
        ]);

        return $this->smarty->fetch("category/admin/update.tpl");

    }//end TMPL_update()


    /**
     * Delete Category
     *
     * @return string
     */
    public function deleteView(int $id): string
    {
        $this->smarty->assign(
            [
                "item" => $this->getCategoryById($id),
            ]
        );

        return $this->smarty->fetch("category/admin/delete.tpl");

    }


    /*****************
     * FUNCTIONS
     */


    private function loadCategoryList()
    {
        $data         = [];
        $categoryList = $this->brdb->adminStatementGetAllCategories();
        // ($min, $max);
        if (isset($categoryList) && !empty($categoryList)) {
            foreach ($categoryList as $dataSet) {
                // links
                $dataSet["editLink"]   = $this->prgPatternElementCategory->linkTo(["page" => $this->_page, "action" => "edit", "id" => $dataSet["categoryId"]]);
                $dataSet["deleteLink"] = $this->prgPatternElementCategory->linkTo(["page" => $this->_page, "action" => "delete", "id" => $dataSet["categoryId"]]);

                $data[] = $dataSet;
            }
        }
        return $data;
        unset($data, $categoryList, $dataSet);

    }//end loadCategoryList()

    public function getCategories()
    {
        return $this->brdb->adminStatementGetAllCategories();
    }

    private function getCategoryById(int $id)
    {
        return $id > 0 ? $this->brdb->statementGetCategoryById($id) : [];

    }//end getCategoryById()
}//end class
