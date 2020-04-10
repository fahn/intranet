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
include_once 'prgPattern.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementClub extends APrgPatternElement 
{
    const __TABLE__              = "club";
    const FORM_FIELD_ID          = "clubId";
    const FORM_FIELD_NAME        = "name";
    const FORM_FIELD_NUMBER      = "clubNr";
    const FORM_FIELD_ASSOCIATION = "association";

    const FORM_CLUB_ACTION             = "formAction";
    const FORM_CLUB_ACTION_INSERT_GAME = "Insert Club";
    const FORM_CLUB_ACTION_UPDATE_GAME = "Update Club";
    const FORM_CLUB_ACTION_DELETE_GAME = "Delete Club";


    // Errors that can be set by methods of this class
    const SUCCESS_CLUB_INSERT            = "Succesfully inserted club!";
    const SUCCESS_CLUB_UPDATED           = "Succesfully updated club!";
    const SUCCESS_CLUB_DELETE            = "Succesfully deleted club!";

    const ERROR_CLUB_MISSING_INFORMATION = "Please provide all required information!";
    const ERROR_CLUB_FAILED              = "Could not identify user!";
    const ERROR_NO_IMPLEMENTATION        = "Noch nicht implementiert";

    protected $prgElementLogin;

    

    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("club");

        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FIELD_NAME);
        $this->registerPostSessionVariable(self::FORM_FIELD_NUMBER);
        $this->registerPostSessionVariable(self::FORM_FIELD_ASSOCIATION);
    }

    public function processPost(): void
    {
        // check rights
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfNonReporter();
        #$this->prgElementLogin->redirectUserIfNonAdmin();

        if (! $this->issetPostVariable(self::FORM_CLUB_ACTION)) 
        {
            $this->setFailedMessage("Kein Formular gewählt");
            return;
        }

        $loginAction = strval(trim($this->getPostVariable(self::FORM_CLUB_ACTION)));

        switch ($loginAction) 
        {
            case self::FORM_CLUB_ACTION_INSERT_GAME:
                $this->processPostInsertClub();
                break;

            case self::FORM_CLUB_ACTION_DELETE_GAME:
                $this->processPostDeleteClub();
                break;

            case self::FORM_CLUB_ACTION_UPDATE_GAME:
                $this->processPostUpdateClub();
                break;

            default:
                return;
                break;
        }
    }

    public function processPostDeleteClub(): void
    {
            $this->setFailedMessage(self::ERROR_NO_IMPLEMENTATION);
    }

    public function processPostInsertClub(): bool
    {
        $requireFields = array(self::FORM_FIELD_NAME, self::FORM_FIELD_NUMBER, self::FORM_FIELD_ASSOCIATION);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) 
        {
            $this->setFailedMessage(self::ERROR_CLUB_MISSING_INFORMATION);
            return false;
        }

        try {
            $club = new Club();
            $club->setClubNr($this->getPostVariableString(self::FORM_FIELD_NUMBER));
            $club->setClubName($this->getPostVariableString(self::FORM_FIELD_NAME));
            $club->setAssociation($this->getPostVariableString(self::FORM_FIELD_ASSOCIATION));

            $this->brdb->insertClub($club);
            $this->setSuccessMessage(self::SUCCESS_CLUB_INSERT);
            return true;

        } 
        catch (Exception $e) 
        {
            $this->log($this->__TABLE__, sprintf("Cannot insert Club. ID: %s. Details %s", $club, $e->getMessage()), "", "POST", "");
            $this->setFailedMessage("Der Club konnte nicht eingetragen werden.");
            return false;
        }
    }



    /**
     * This post method just rpocesses if the admin match id is set.
     * If it is the emthod asks the DB for a given game and reads it.
     * It also stores the game information into the session, hence the
     * insert game page will show the details.
     */
    public function processPostUpdateClub(): bool
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_FIELD_ID, self::FORM_FIELD_NAME, self::FORM_FIELD_NUMBER, self::FORM_FIELD_ASSOCIATION);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) 
        {
            $this->setFailedMessage(self::ERROR_CLUB_MISSING_INFORMATION);
            return false;
        }

        try {
            $club = new Club();
            $club->setClubId($this->getPostVariableInt(self::FORM_FIELD_ID));
            $club->setClubNr($this->getPostVariableString(self::FORM_FIELD_NUMBER));
            $club->setClubName($this->getPostVariableString(self::FORM_FIELD_NAME));
            $club->setAssociation($this->getPostVariableString(self::FORM_FIELD_ASSOCIATION));

            $this->brdb->updateClubById($club);

            $this->setSuccessMessage(self::SUCCESS_CLUB_UPDATED);
            return true;
        } 
        catch (Exception $e) 
        {
            $this->log($this->__TABLE__, sprintf("Canno insert Club ID: %i. Details %s", $club->getClubId(), $e->getMessage()), "", "POST", "");
            $this->setFailedMessage("Der Club konnte nicht geupdatet werden.");
            return false;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet():void 
    {
    }

    public function getClubs(): ?array
    {
        return $this->brdb->selectAllClubs();
    }

    public function find(Club $club): bool
    {
        try {
            return count($this->brdb->selectClubByClubNr($club->getClubNr())) > 0 ? true : false ;
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * Insert club Data
     *
     * @param Club $club
     * @return void
     */
    public function insert(Club $club) 
    {
        return $this->brdb->insertClub($club);
    }

    /**
     * Update Club by Id
     *
     * @param Club $club
     * @return boolean
     */
    public function update(Club $club): bool
    {
        return $this->brdb->updateClubByClubNr($club);
    }
}
?>
