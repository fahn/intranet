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

Class Tournament extends \Badtra\Intranet\Model\DefaultModel
{
    // tournament Settings
    private int    $tournamentId;
    private string $name;
    private string $tournamentType;
    private string $place;
    private string $startdate;
    private string $enddate;
    private string $deadline;
    private string $link;
    private string $classification;
    private string $additionalClassification;
    private string $discipline;
    private string $latitude;
    private string $longitude;
    private string $description;

    // settings
    private bool $openSubscription;
    private bool $visible;

    // reporter
    private int $reporterId;

    //
    private array $tournamentTypeEnum = array('NBV', 'FUN', 'OTHER');

    public function __construct() {}

    /**
     * set TournamentId
     */
    public function setTournamentId(int $tournamentId): void
    {
        if ($tournamentId < 0) {
            throw new \Exception("ID < 0");
        }
        $this->tournamentId = $tournamentId;
    }

    /**
     * get Tournament Id
     *
     * @return integer
     */
    public function getTournamentId(): int
    {
        return $this->tournamentId;
    }

    /**
     * get Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set Name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        unset($name);
    }

    /**
     * get tournamentType
     *
     * @return string
     */
    public function getTournamentType(): string
    {
        return $this->tournamentType;
    }

    /**
     * set tournamentType
     *
     * @param string $tournamentType
     * @return void
     */
    public function setTournamentType(string $tournamentType): void
    {
        if (!in_array($tournamentType, $this->tournamentTypeEnum))
        {
            throw new \Exception("TournamentType not in Range");
           
        }
        $this->tournamentType = $tournamentType;
        unset($tournamentType);
    }

    /**
     * set Place
     *
     * @param string $place
     * @return void
     */
    public function setPlace(string $place): void
    {
        $this->place = $place;
        unset($place);
    }

    /**
     * get Place
     *
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }
   
    /**
     * get StartDate
     *
     * @param string $startdate
     * @return void
     */
    public function setStartdate(string $startdate): void
    {
        $this->startdate = $startdate;
        unset($startdate);
    }

    /**
     * get StartDate
     *
     * @return string
     */
    public function getStartdate(): string
    {
        return $this->startdate;
    }

    /**
     * set enddate
     *
     * @param string $enddate
     * @return void
     */
    public function setEndDate(string $enddate): void
    {
        $this->enddate = $enddate;
        unset($enddate);
    }

    /**
     * get enddate
     *
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->enddate;
    }
   
    /**
     * set deadline
     *
     * @param string $deadline
     * @return void
     */
    public function setDeadline(string $deadline): void
    {
        $this->deadline = $deadline;
        unset($deadline);
    }

    /**
     * get deadline
     *
     * @return string
     */
    public function getDeadline(): string
    {
        return $this->deadline;
    }
   
    /**
     * set link
     *
     * @param string $line
     * @return void
     */
    public function setLink(string $link): void
    {
        if (!filter_var($link, FILTER_VALIDATE_URL))
        {
            throw new \Exception(sprintf("Link isnt valid: %s", $link));
        }
        $this->link = $link;
        unset($link);
    }

    /**
     * get link
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }
   
    /**
     * set classification
     *
     * @param string $classification
     * @return void
     */
    public function setClassification(string $classification): void
    {
        $this->classification = $classification;
        unset($classification);
    }

    /**
     * get classification
     *
     * @return string
     */
    public function getClassification(): string
    {
        return $this->classification;
    }
   
    /**
     * set additionalClassification
     *
     * @param string $additionalClassification
     * @return void
     */
    public function setAdditionalClassification(string $additionalClassification): void
    {
        $this->additionalClassification = $additionalClassification;
        unset($additionalClassification);
    }

    /**
     * set additionalClassification
     *
     * @return string
     */
    public function getAdditionalClassification(): string
    {
        return $this->additionalClassification;
    }

    /**
     * get discipline
     *
     * @param string $discipline
     * @return void
     */
    public function setDiscipline(string $discipline): void {
        $this->discipline = $discipline;
        unset($discipline);
    }

    /**
     * set discipline
     *
     * @return string
     */
    public function getDiscipline(): string {
        return $this->discipline;
    }

    /**
     * set latitude
     *
     * @param string $latitude
     * @return void
     */
    public function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
        unset($latitude);
    }

    /**
     * get latitude
     *
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }
   
    /**
     * set longitude
     *
     * @param string $longitude
     * @return void
     */
    public function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
        unset($longitude);
    }

    /**
     * set longitude
     *
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * set description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void {
        $this->description = $description;
        unset($description);
    }

    /**
     * get description
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * set reporter Id
     *
     * @param integer $reporterId
     * @return void
     */
    public function setReporterId(int $reporterId):void
    {
        $this->reporterId = $reporterId;
        unset($reporterId);
    }

    /**
     * get reporter Id
     * @Task Generate User instead ID
     * @return integer
     */
    public function getReporterId(): int
    {
        return $this->reporterId;
    }

    /**
     * set openSubscription
     *
     * @param boolean $openSubscription
     * @return void
     */
    public function setOpenSubscription(bool $openSubscription):void
    {
        $this->openSubscription = $openSubscription;
        unset($openSubscription);
    }

    /**
     * return openSubscription
     *
     * @return boolean
     */
    public function getOpenSubscription(): bool
    {
        return $this->openSubscription;
    }

    public function __toString(): string
    {
        return sprintf(
            "ID: %d\nName: %s\nOrt: %s\nLink: %s",
            $this->tournamentId,
            $this->name,
            $this->place,
            $this->link,

        );
    }
}

