<?php
#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$path=dirname(dirname(__FILE__));
$_SERVER['BASE_DIR'] = $path;

require_once $_SERVER['BASE_DIR'] ."/inc/db/brdb.inc.php";
require_once $_SERVER['BASE_DIR'] ."/inc/logic/tools.inc.php";

try {
    if (isset($_POST["playerSearch"])) {
        $brdb = new BrankDB();
        $_term = $_POST['playerSearch']; //mysqli_real_escape_string($brdb, );
        $res = $brdb->getPlayerByTerm($_term);
        
        if (!$brdb->hasError() || $res->num_rows > 0) {
            $data = array();
            while ($row = $res->fetch_assoc()) {
                extract($row);
                $data['results'][] = array(
                    'id'   => $playerId, 
                    'text' => sprintf("%s (SpNr.: %s; Verein: %s)", $playerName, $playerNr, $clubName)
                );
                
            }
            echo json_encode($data);
        } else {
            echo json_encode("No matches found");
        }
        
    }
} catch(Exception $e){
    die("ERROR: Could not able to execute" . $e->getMessage());
}

?>
