<?php

$path=dirname(dirname(__FILE__));
$_SERVER['BASE_DIR'] = $path;

require_once $_SERVER['BASE_DIR'] ."/inc/db/brdb.inc.php";
require_once $_SERVER['BASE_DIR'] ."/inc/logic/tools.inc.php";


try {
    if(isset($_POST["playerSearch"])){
        $term = $_POST['playerSearch'];
        #die($term);
        $brdb = new BrankDB();
        $res = $brdb->getPlayerByTerm($term);

        if (!$brdb->hasError()) {

            while ($row = $res->fetch_assoc()) {
                $line = sprintf("<p>%s %s (SpNr.:%s, Verein: %s)</p>", $row['firstName'],$row["lastName"], $row['playerId'], $row['clubName']);
                echo $line;
            }
        } else {
            echo "<p>No matches found</p>";
        }
    }
} catch(Exception $e){
    die("ERROR: Could not able to execute" . $e->getMessage());
}
?>
