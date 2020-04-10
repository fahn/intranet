<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

require_once('_base.model.php');

class Club extends BaseModel 
{
    private int    $clubId;
    private string $clubName;
    private string $clubNr;
    private string $association;


    public function __construct($dataSet = null) 
    {
        if ($dataSet) 
        {
            foreach ($dataSet as $key => $value) 
            {
                if ( property_exists($this,$key) ) 
                {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * get Club Id
     *
     * @return integer
     */
    public function getClubId(): int
    {
        return $this->clubId;
    }

    public function setClubId(int $id): void 
    {
        if ($id < 0) {
            throw new Exception("ID < 0");
        }

        $this->clubId = $id; 
    }

    public function getClubNr(): string 
    {
        return $this->clubNr;
    }

    public function setClubNr(string $clubNr): void 
    {
        if (strlen($clubNr) == 0) {
            throw new Exception("strlen(clubNr) == 0");
        }

        $this->clubNr = $clubNr; 
    }

    public function getClubName(): string
    {
        return $this->clubName;
    }

    public function setClubName(string $name): void 
    {
        if (strlen($name) == 0) {
            throw new Exception("strlen(name) == 0");
        }

        $this->clubName = $name; 
    }

    public function getAssociation(): string
    {
        return $this->association;
    }

    public function setAssociation(string $association): void 
    {
        if (strlen($association) == 0) {
            throw new Exception("strlen(association) == 0");
        }

        $this->association = $association; 
    }

    public function getClubArray(): array 
    {
        return array(
            'clubNr'      => $this->getClubNr(),
            'clubName'    => $this->getClubName(),
            'association' => $this->getAssociation(),
        );
    }

    /**
     * Print Club-Infomration
     */
    public function __toString(): string
    {
        return sprintf("%s => %s [ID: %i]\n", $this->clubNr, $this->clubName, $this->clubId);
    }
}
?>
