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
$stage = getenv('stage', true) ?: getenv('stage');

if ($stage == "development") {
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$path=dirname(dirname(__FILE__));
define("BASE_DIR", $path);

require_once $path ."/inc/db/brdb.inc.php";


if (isset($_POST) && is_array($_POST) && isset($_POST['playerSearch'])) 
{
    try 
    {
        $brdb = new BrankDB();
        $term = strval(trim(stripslashes(strip_tags($_POST['playerSearch']))));
        $playerList = $brdb->getPlayerByTerm($term);

        if (!isset($playerList) ||empty($playerList)) {
            throw new Exception("No matches found");
        }

        $data = array();
        foreach ($playerList as $row) 
        {
            $data['results'][] = array(
                'id'   => $row['playerId'],
                'text' => sprintf("%s (SpNr.: %s; Verein: %s)", $row['playerName'], $row['playerNr'], $row['clubName'])
            );

        }

        echo json_encode($data);
        unset($brdb, $term, $data, $playerList, $row);

    }
    catch(Exception $e)
    {
        print_r("ERROR: Could not able to execute" . $e->getMessage());
    }
} 

?>