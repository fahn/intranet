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

use DateTime;
use \Badtra\Intranet\Widget\DefaultWidget;

class BdayWidget extends DefaultWidget
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

            default:
                return "no name / or not exists";
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

    private function calculate_age2($date): string
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

    private function calculate_age(string $date): int
    {
        // Datum validieren
        if (empty($date) || $date == "0000-00-00") {
            return 0; // Oder eine andere Fehlerrückgabe, falls das Datum ungültig ist
        }

        // Datum korrekt formatieren
        $date = str_replace("/", "-", substr(trim($date), 0, 10));
        
        // Erstellen eines DateTime-Objekts
        $birthdate = DateTime::createFromFormat('Y-m-d', $date);

        // Falls das Datum ungültig ist, z.B. 31. Februar
        if (!$birthdate) {
            return 0;
        }

        // Berechnung des Alters
        $today = new DateTime();
        $age = $today->diff($birthdate)->y;

        return $age;
    }

}
