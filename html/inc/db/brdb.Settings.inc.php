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
trait SettingsDB
{

    /**
     * loadSettings @TODO: upcoming feature
     */
    public function loadSettings()
    {
        $cmd = $this->db->prepare("SELECT * FROM Settings");

        return $this->executeStatement($cmd);
    }
}
?>
