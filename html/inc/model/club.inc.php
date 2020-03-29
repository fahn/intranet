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
    private $clubId = 0;
    private $clubName;
    private $clubNr;
    private $association;


    public function __construct($dataSet = null) {
        if ($dataSet) {
            foreach($dataSet as $key => $value) {
                if ( property_exists($this,$key) ) {
                    $this->$key = $value;
                }
            }
        }

    }

    public function getClubNr():string 
    {
        return $this->clubNr;
    }

    public function getClubName():string
    {
        return $this->clubName;
    }

    public function getAssociation():string
    {
        return $this->association;
    }

    public function getClubArray():array 
    {
        return array(
            'clubNr'      => $this->getClubNr(),
            'clubName'    => $this->getClubName(),
            'association' => $this->getAssociation(),
        );
    }

    /**
     * Print Club
     */
    public function __toString():string
    {
        return sprintf("%s => %s [ID: %i]\n", $this->clubNr, $this->clubName, $this->clubId);
    }
}
?>
