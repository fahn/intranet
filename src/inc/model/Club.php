<?php
/*******************************************************************************
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
namespace Badtra\Intranet\Model;

class Club extends \Badtra\Intranet\Model\BaseModel
{

    private int    $clubId;

    private string $clubName;

    private string $clubNr;

    private string $association;


    public function __construct($dataSet = null)
    {
        if ($dataSet) {
            foreach ($dataSet as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }//end __construct()


    /**
     * get Club Id
     *
     * @return integer
     */
    public function getClubId(): int
    {
        return $this->clubId;
    }//end getClubId()


    public function setClubId(int $id): void
    {
        if ($id < 0) {
            throw new \Exception("ID < 0");
        }

        $this->clubId = $id;
    }//end setClubId()


    public function getClubNr(): string
    {
        return $this->clubNr;
    }//end getClubNr()


    public function setClubNr(string $clubNr): void
    {
        if (strlen($clubNr) == 0) {
            throw new \Exception("strlen(clubNr) == 0");
        }

        $this->clubNr = $clubNr;
    }//end setClubNr()


    public function getClubName(): string
    {
        return $this->clubName;
    }//end getClubName()


    public function setClubName(string $name): void
    {
        if (strlen($name) == 0) {
            throw new \Exception("strlen(name) == 0");
        }

        $this->clubName = $name;
    }//end setClubName()


    public function getAssociation(): string
    {
        return $this->association;
    }//end getAssociation()


    public function setAssociation(string $association): void
    {
        if (strlen($association) == 0) {
            throw new \Exception("strlen(association) == 0");
        }

        $this->association = $association;
    }//end setAssociation()


    public function getClubArray(): array
    {
        return [
            'clubNr'      => $this->getClubNr(),
            'clubName'    => $this->getClubName(),
            'association' => $this->getAssociation(),
        ];
    }//end getClubArray()


    /**
     * Print Club-Infomration
     */
    public function __toString(): string
    {
        return sprintf("%s => %s [ID: %i]\n", $this->clubNr, $this->clubName, $this->clubId);
    }//end __toString()
}//end class
