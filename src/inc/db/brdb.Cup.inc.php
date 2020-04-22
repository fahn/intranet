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
trait CupDB
{
    /**
     * Get all cups
     *
     * @return array|null
     */
    public function getAllCups(): ?array
    {
        $query     = "SELECT * FROM `Cup` ORDER by startdate ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();
       
        return $statement->fetch();
    }
}
