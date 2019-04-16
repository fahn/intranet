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


class Club {
    private $clubId;
    private $clubName;
    private $clubNumber;


    public function __construct($dataSet = null) {
        if ($dataSet == null) {
            return;
        }

        try {
            extract($row);
            $this->clubId = $clubId;
            $this->clubName = $clubName;
            $this->clubNumber = $clubNumber;
        } Exception ($e) {
            return;
        }

    }

    /**
     * Print Club 
     */
    public function __toString() {
        return sprintf("%s => %s [ID: %i]", $this->clubNumber, $this->clubName, $this->clubId);
    }
