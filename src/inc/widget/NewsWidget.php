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

class NewsWidget extends \Badtra\Intranet\Widget\DefaultWidget
{


    public function __construct()
    {
        parent::__construct();
    }//end __construct()


    public function showWidget(?string $name)
    {
        switch ($name) {
            case "latestNews":
                return $this->TPML_latestNews();
                break;

            default:
                return "no name / or not exists";
                break;
        }

        return;
        unset($name);

    }//end showWidget()


    private function TPML_latestNews(): string
    {
        $this->smarty->assign("data", $this->getLatestNews());

        return $this->smarty->fetch("news/widget/latest.tpl");
    }//end TPML_latestNews()


    private function getLatestNews():array
    {
        $tmp      = [];
        $newsList = $this->brdb->selectLatestNews(5);
        if (isset($newsList) && !empty($newsList)) {
            foreach ($newsList as $dataSet) {
                $dataSet["linkTo"] = $this->linkTo(["page" => "news.php", "action" => "details", "id" => $dataSet["tournamentId"]]);
                $tmp[] = $dataSet;
            }
        }

        return $tmp;
        unset($newsList, $tmp, $dataSet);
    }//end getLatestNews()
}//end class
