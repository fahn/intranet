<?php
/**
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
namespace Badtra\Intranet\Ajax;

use Badtra\Intranet\Logic\APrgPatternElement;

class AjaxQuery extends APrgPatternElement
{
    private bool $status = false;
    private string $data;
    private string $term;

    /**
     * Default constructor
     */
    public function __construct()
    {
        // init        
        $this->term = $this->getPostVariableString("playerSearch");

        try
        {
            switch($this->term)
            {
                case "player":
                    $result = $this->brdb->getPlayerByTerm($this->term);
                    break;
                case "user":
                    $result = $this->brdb->getUserByTerm($this->term);
                    break;
                default:
                    throw new \Exception("Unkown param");
                    break;
            }

            $data = $this->interpreteResult($result, "userId", "fullname");
            $this->data = json_encode($data);
            unset($brdb, $_term, $data, $row, $userList);

        } catch (\Exception $e)
        {
            $this->data = sprintf("ERROR: Could not able to execute: %s", $e->getMessage());
            return false;
        }

    }

    /**
     * Destruct constructor
     */
    public function __destruct()
    {
        if ($this->status)
        {
            exit(0);
        }
        exit(99);
    }

    /**
     * Interprete Result
     *
     * @param array $resultData
     * @param string $index
     * @param string $text
     * @return array
     */
    private function interpreteResult(array $resultData, string $index, string $text): array
    {
        $data = array();
        foreach ($resultData as $row)
        {
            $data['results'][] = array(
                'id'   => $row[$index],
                'text' => $row[$text],
            );
        }

        return $data;
        unset($data, $resultData, $index, $text);

    }

    /**
     * Return Data
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->data;
    }


    public function processPost() {}

    public function processGet() {}
}

// Clean
ob_clean();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$ajaxQuery = new AjaxQuery();
echo $ajaxQuery;
exit(0);