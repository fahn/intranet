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
include_once 'prgPattern.inc.php';

include_once BASE_DIR .'/inc/db/brdb.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementFaq extends APrgPatternElement {

    private $brdb;

    // FORMS
    const FORM_FIELD_ID         = "faqId";
    const FORM_FIELD_TITLE      = "title";
    const FORM_FIELD_CATEGORYID = "categoryId";
    const FORM_FIELD_TEXT       = "text";

    // FIELDS
    const FORM_ACTION = "formAction";
    const FORM_INSERT = "Insert";
    const FORM_UPDATE = "Update";
    const FORM_DELETE = "Delete";


    protected $prgElementLogin;

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("faq");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;
        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
        $this->registerPostSessionVariable(self::FORM_FIELD_CATEGORYID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TEXT);
    }

    public function processPost() {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array('reporter', 'admin'), 'or');

        if (!$this->issetPostVariable(self::FORM_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }

        $action = strval(trim($this->getPostVariable(self::FORM_ACTION)));

        switch ($action) {
            case self::FORM_INSERT:
                $this->processPostInsertFaq();
                break;

            case self::FORM_DELETE:
                $this->processPostDeleteFaq();
                break;

            case self::FORM_UPDATE:
                $this->processPostUpdateFaq();
                break;

            default:
                return;
                break;
        }

    }

    private function processPostDeleteFaq() {
        if (! $this->issetPostVariable(self::FORM_FIELD_ID)) {
            $this->setFailedMessage("keine ID übergeben");
            return;
        }

        $id = strval(trim($this->getPostVariable(self::FORM_FIELD_ID)));

        $this->brdb->deleteFaq($id);

        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        $this->setSuccessMessage("FAQ wurde gelöscht");
        return;
    }

    public function processPostInsertFaq() {
        // Check that all information has been posted
        if (! $this->issetPostVariable(self::FORM_FIELD_TITLE) ||
            ! $this->issetPostVariable(self::FORM_FIELD_CATEGORYID) ||
            ! $this->issetPostVariable(self::FORM_FIELD_TEXT) ) {
                $this->setFailedMessage("FAQ konnte nicht eingetragen werden");
                return;
        }

        $title      = strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE)));
        $categoryId = strval(trim($this->getPostVariable(self::FORM_FIELD_CATEGORYID)));
        $text       = strval(trim($this->getPostVariable(self::FORM_FIELD_TEXT)));


        $this->brdb->insertFaq($title, $categoryId, $text);

        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        $this->setSuccessMessage("FAQ wurde eingetragen");
        return;
    }



    /**
     * This post method just rpocesses if the admin match id is set.
     * If it is the emthod asks the DB for a given game and reads it.
     * It also stores the game information into the session, hence the
     * insert game page will show the details.
     */
    public function processPostUpdateFaq() {
        // Check that all information has been posted
        if (! $this->issetPostVariable(self::FORM_FIELD_TITLE) ||
            ! $this->issetPostVariable(self::FORM_FIELD_CATEGORYID) ||
            ! $this->issetPostVariable(self::FORM_FIELD_TEXT) ) {
                $this->setFailedMessage("FAQ konnte nicht aktualisiert werden.");
                return;
        }

        if (! $this->issetPostVariable(self::FORM_FIELD_ID)) {
            $this->setFailedMessage("keine ID übergeben");
            return;
        }

        $id         = strval(trim($this->getPostVariable(self::FORM_FIELD_ID)));
        $title      = strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE)));
        $categoryId = strval(trim($this->getPostVariable(self::FORM_FIELD_CATEGORYID)));
        $text       = strval(trim($this->getPostVariable(self::FORM_FIELD_TEXT)));


        $this->brdb->updateFaqById($id, $title, $categoryId, $text);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }
        
        $this->setSuccessMessage("FAQ wurde erfolgreich geändert.");
        return;
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() { }
}
?>
