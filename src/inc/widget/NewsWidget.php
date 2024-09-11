<?php
/*******************************************************************************
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
namespace Badtra\Intranet\Widget;

use \Smarty;
use Badtra\Intranet\DB\BrankDB;
use \Badtra\Intranet\Widget\DefaultWidget;

class NewsWidget extends DefaultWidget
{

    // protected $smarty;

    // protected $brdb;


    public function __construct(Smarty $smarty, BrankDB $brdb)
    {
        // $this->smarty = $smarty;
        // $this->brdb = $brdb;
        parent::__construct($smarty, $brdb);
        //$aPrgPatternElement = APrgPatternElement::getInstance();
    }//end __construct()


    public function showWidget(?string $name)
    {
        switch ($name) {
            case "latestNews":
                return $this->TPML_latestNews();

            default:
                return "no name / or not exists";
        }
    }//end showWidget()


    private function TPML_latestNews(): string
    {
        $this->smarty->assign("data", $this->getLatestNews());

        return $this->smarty->fetch("news/widget/latest.tpl");
    }//end TPML_latestNews()


    private function getLatestNews():array
    {
        $dataSet = array();
        $data = $this->brdb->selectLatestNews(5);
        if (isset($data) && !empty($data)) {
            foreach ($data as $item) {
                $item["linkTo"] = "/news/". $item["newsId"]; //$APrgPatternElement->linkTo(["page" => "news.php", "action" => "details", "id" => $dataSet["tournamentId"]]);
                $dataSet[] = $item;
            }
        }

        return $dataSet;
    }//end getLatestNews()
}//end class
