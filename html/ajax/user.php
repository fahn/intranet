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
header("Content-Type: application/json;"); // charset=UTF-8", false);

$path=dirname(dirname(__FILE__));
define("BASE_DIR", $path);

require_once $path ."/inc/db/brdb.inc.php";
require_once $path ."/inc/logic/tools.inc.php";

try {
    if (isset($_POST["userSearch"])) {
        $brdb = new BrankDB();
        $term = strval(trim(strip_tags($_POST['userSearch'])));
        $res = $brdb->getUserByTerm($term);
        $data = array();
        if (!$brdb->hasError() || $res->num_rows > 0) {

            while ($row = $res->fetch_assoc()) {
                $data['results'][] = array(
                    'id'   => $userId,
                    'text' => sprintf("%s, %s", $row['lastName'], $row['firstName'])
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
