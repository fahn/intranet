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
require_once "/brdbHtmlPage.inc.php";
require_once BASE_DIR."/inc/logic/prgCategory.inc.php";

class BrdbHtmlAdminCategoryPage extends BrdbHtmlPage
{

    // pattern
    private $prgPatternElementCategory;

    private $_page = "";

    const MAX_ENTRIES = 50;


    public function __construct($page = null)
    {
        parent::__construct();

        $this->prgPatternElementCategory = new prgPatternElementCategory($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementCategory);

        if ($page != null) {
             $this->_page = $page;
        }


        // load links
        $links = [
            "add"  => $this->prgPatternElementCategory->linkTo(["page" => $this->_page, "action" => "add"]),
            "list" => $this->prgPatternElementCategory->linkTo(["page" => $this->_page, "action" => "add"]),
            "home" => $this->prgPatternElementCategory->linkTo(["page" => $this->_page]),
        ];

        $this->smarty->assign("links", $links);

    }//end __construct()


    public function htmlBody(): void
    {
        switch ($this->action) {
        case "add":
        case "edit":
            $content = $this->TMPL_update($this->action);
                break;

        case "delete":
            $content = $this->TMPL_delete();
                break;

        default:
            $content = $this->TMPL_list();
                break;
        }

        $this->smarty->assign(
            ["content" => $content]
        );

        $this->smarty->display("index.tpl");

    }//end htmlBody()


    /*******************
     * VIEWS
     */


    private function TMPL_list()
    {
        $this->smarty->assign(
            [
                "list" => $this->loadCategoryList(),
            ]
        );
        return $this->smarty->fetch("category/adminList.tpl");

    }//end TMPL_list()


    private function TMPL_update(String $action)
    {
        $this->smarty->assign(
            ["action" => $action]
        );

        return $this->smarty->fetch("category/adminUpdate.tpl");

    }//end TMPL_update()


    /**
     * Delete Category
     *
     * @return string
     */
    private function TMPL_delete(): string
    {
        $this->smarty->assign(
            [
                "item" => $this->getCategoryById($this->id),
            ]
        );

        return $this->smarty->fetch("category/adminDelete.tpl");

    }//end TMPL_delete()


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


    private function getCategoryById(int $id)
    {
        return $id > 0 ? $this->brdb->statementGetCategoryById($id) : [];

    }//end getCategoryById()
}//end class
