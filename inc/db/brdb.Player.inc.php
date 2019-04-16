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

trait PlayerDB {
    /**
     * get All Player
     * @return unknown
     */
    public function selectGetAllPlayer() {
        $sql = "SELECT Player.*, CONCAT_WS(' ', Player.firstName, Player.lastName) as fullName, Club.name AS clubName FROM Player
            LEFT JOIN `Club` ON Club.clubId = Player.clubId
            ORDER BY Player.lastName ASC";
        $cmd = $this->db->prepare($sql);

        return $this->executeStatement($cmd);
    }
}

?>
