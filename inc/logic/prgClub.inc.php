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

include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementClub extends APrgPatternElement {

    private $brdb;
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

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("club");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;
        $this->registerPostSessionVariable(self::FORM_FIELD_NAME);
        $this->registerPostSessionVariable(self::FORM_FIELD_NUMBER);
        $this->registerPostSessionVariable(self::FORM_FIELD_ASSOCIATION);
    }

    public function processPost() {
        // check rights
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfNonReporter();
        #$this->prgElementLogin->redirectUserIfNonAdmin();

        if (! $this->issetPostVariable(self::FORM_CLUB_ACTION)) {
            $this->setFailedMessage("Kein Formular gewählt");
            return;
        }

        $loginAction = strval(trim($this->getPostVariable(self::FORM_CLUB_ACTION)));
        switch ($loginAction) {
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

    public function processPostDeleteClub() {
            $this->setFailedMessage(self::ERROR_NO_IMPLEMENTATION);
    }

    public function processPostInsertClub() {
        #echo $this->getPostVariable(self::FORM_FIELD_NAME), $this->getPostVariable(self::FORM_FIELD_NUMBER),  $this->getPostVariable(self::FORM_FIELD_ASSOCIATION);
        #die();

        // Check that all information has been posted
        if ($this->issetPostVariable(self::FORM_FIELD_NAME) &&
            $this->issetPostVariable(self::FORM_FIELD_NUMBER) &&
            $this->issetPostVariable(self::FORM_FIELD_ASSOCIATION)) {

            $name              = strval(trim($this->getPostVariable(self::FORM_FIELD_NAME)));
            $number          = strval(trim($this->getPostVariable(self::FORM_FIELD_NUMBER)));
            $assosiation     = strval(trim($this->getPostVariable(self::FORM_FIELD_ASSOCIATION)));

            $this->brdb->insertClub($name, $number, $assosiation);

            if ($this->brdb->hasError()) {
                $this->setFailedMessage($this->brdb->getError());
                return;
            }

            $this->setSuccessMessage(self::SUCCESS_CLUB_INSERT);
            return;

        } else {
            $this->setFailedMessage(self::ERROR_CLUB_MISSING_INFORMATION);
        }
    }



    /**
     * This post method just rpocesses if the admin match id is set.
     * If it is the emthod asks the DB for a given game and reads it.
     * It also stores the game information into the session, hence the
     * insert game page will show the details.
     */
    public function processPostUpdateClub() {
        // Check that all information has been posted
        if ($this->issetPostVariable(self::FORM_FIELD_ID) &&
            $this->issetPostVariable(self::FORM_FIELD_NAME) &&
                $this->issetPostVariable(self::FORM_FIELD_NUMBER) &&
                $this->issetPostVariable(self::FORM_FIELD_ASSOCIATION)) {

            $id           = intval(trim($this->getPostVariable(self::FORM_FIELD_ID)));
            $name              = strval(trim($this->getPostVariable(self::FORM_FIELD_NAME)));
            $number          = strval(trim($this->getPostVariable(self::FORM_FIELD_NUMBER)));
            $association     = strval(trim($this->getPostVariable(self::FORM_FIELD_ASSOCIATION)));

            // get the admin ID and try to read the corresponding game from the
            // data base, process the rror in case of
            $res = $this->brdb->updateClubById($id, $name, $number, $association);
            if ($this->brdb->hasError()) {
                $this->setFailedMessage($this->brdb->getError());
                return;
            }

            // if no error occurred than read the game and write the
            // results to the session of the server

            //$this->setSessionVariable(self::FORM_GAME_WINNER    , $game->winner);

            $this->setSuccessMessage(self::SUCCESS_CLUB_UPDATED);
            return;
        } else {
            $this->setFailedMessage(self::ERROR_CLUB_MISSING_INFORMATION);
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        return;
        // Check that all information has been posted
        if (isset($_GET[self::FORM_GAME_ACTION])) {
            $formAction = strVal(Tools::escapeInput($_GET[self::FORM_GAME_ACTION]));
            if ($formAction == self::FORM_GAME_ACTION_NEW_GAME) {
                $this->setSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID, self::FORM_GAME_ACTION_NEW_GAME);
                $this->clearSessionVariables();
            }
        }
    }

    public function list() {
        $this->countRows = $this->brdb->selectAllClubs()->num_rows;

        $res = $this->brdb->selectAllClubs();
        $loop = array();
        if (!$this->brdb->hasError()) {
          while ($dataSet = $res->fetch_assoc()) {
            $loop[] = $dataSet; //new User($dataSet);

          }
        }
        return $loop;
    }

    public function find($item) {
        if ($item instanceof Club) {
            echo "**";
            $res = $this->brdb->selectClubByClubNr($item->getClubNr());
            $tmp = array();
            if ($this->brdb->hasError()) {
                return false;
            }

            return $res->num_rows == 1 ? true : false;
        }
        return false;
    }

    public function insert($item) {
        if ($item instanceof Club) {
            $res = $this->brdb->insertClubByModel($item);
            if ($this->brdb->hasError()) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function update($item) {
        if ($item instanceof Club) {
            $res = $this->brdb->updateClubByClubNr($item->getClubNr(), $item->getClubName(), $item->getAssociation());
            if ($this->brdb->hasError()) {
                return false;
            }
            return true;
        }
        return false;
    }
}
?>
