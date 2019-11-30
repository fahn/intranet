<?php
#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$path=dirname(dirname(__FILE__));
$_SERVER['BASE_DIR'] = $path;

require_once $_SERVER['BASE_DIR'] ."/inc/db/brdb.inc.php";
require_once $_SERVER['BASE_DIR'] ."/inc/logic/tools.inc.php";

try {
    if (isset($_POST["userSearch"])) {
        $brdb = new BrankDB();
        $term = strval(trim(strip_tags($_POST['userSearch'])));
        $res = $brdb->getUserByTerm($term);
        $data = array();
        if (!$brdb->hasError() || $res->num_rows > 0) {

            while ($row = $res->fetch_assoc()) {
                extract($row);
                $data['results'][] = array(
                    'id'   => $userId,
                    'text' => sprintf("%s, %s", $lastName, $firstName)
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
