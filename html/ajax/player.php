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
$stage = getenv('stage', true) ?: getenv('stage');

if ($stage == "development") {
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$path=dirname(dirname(__FILE__));
define("BASE_DIR", $path);

require_once $path ."/inc/db/brdb.inc.php";
require_once $path ."/inc/logic/tools.inc.php";

try {
    if (isset($_POST["playerSearch"])) {
        $brdb = new BrankDB();
        $_term = strval(trim(strip_tags($_POST['playerSearch'])));
        $res = $brdb->getPlayerByTerm($_term);

        if (!$brdb->hasError() || $res->num_rows > 0) {
            $data = array();
            while ($row = $res->fetch_assoc()) {
                $data['results'][] = array(
                    'id'   => $playerId,
                    'text' => sprintf("%s (SpNr.: %s; Verein: %s)", $row['playerName'], $row['playerNr'], $row['clubName'])
                );

            }
        } else {
            $data['message'] = "No matches found";
        }
        echo json_encode($data);

    }
} catch(Exception $e){
    die("ERROR: Could not able to execute" . $e->getMessage());
}

?>
