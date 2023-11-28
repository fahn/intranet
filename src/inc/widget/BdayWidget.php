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
namespace Badtra\Intranet\Widget;

class BdayWidget extends \Badtra\Intranet\Widget\DefaultWidget
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function showWidget(?string $name): string
    {
        switch ($name) {
            case "nextBdays":
                return $this->TPML_NextBdays();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TPML_NextBdays():string
    {
        $data = $this->getNextBdays();


        $this->smarty->assign(array(
          "bdays" => $data,
        ));

        return $this->smarty->fetch("user/widget/bday.tpl");
    }

    private function getNextBdays():array
    {
        $data = array();
        $res  = $this->brdb->getUpcomingBirthdays();
        if (!isset($res) || !is_array($res))
        {
            while ($dataSet = $res)
            {
                $dataSet["linkToUser"] = $this->linkTo(array("page" => "user.php", "id" => $dataSet["userId"]));
                $dataSet["years"]      = $this->calculate_age($dataSet["bday"]);

                $data[] = $dataSet;
            }
        }
        return $data;
    }

    private function calculate_age($date): string
    {
       $year_diff = 0;

       $date = str_replace("/", "-", substr(trim($date), 0, 10));

       if ($date != "" && $date != "0000-00-00") {
            list($year, $month, $day) = explode("-", $date);
            $year_diff = intval(date("Y")) - intval($year);
            $month_diff = intval(date("m")) - intval($month);
            $day_diff = intval(date("d")) - intval(substr($day, 0, 2));
            if ($month_diff < 0) {
                $year_diff--;
            } else {
                if (($month_diff == 0) && ($day_diff < 0)) {
                    $year_diff--;
                }
            }
       }

       return 1+$year_diff;
    }
}
