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
class PrgPatternElementCategory extends APrgPatternElement {

    private $brdb;

    // FORMS
    const FORM_FIELD_PID        = "pid";
    const FORM_FIELD_TITLE      = "title";

    // FIELDS
    const FORM_ACTION = "formAction";
    const FORM_INSERT = "Insert";
    const FORM_UPDATE = "Update";
    const FORM_DELETE = "Delete";


    protected $prgElementLogin;

    private $requiredFields = array('FORM_FIELD_PID', 'FORM_FIELD_CATEGORYID', 'FORM_FIELD_TITLE', 'FORM_FIELD_TEXT');

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("category");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;
        $this->registerPostSessionVariable(self::FORM_FIELD_PID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
    }

    public function processPost() {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array('admin'));

        if (!$this->issetPostVariable(self::FORM_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }

        $action = strval(trim($this->getPostVariable(self::FORM_ACTION)));

        switch ($action) {
            case self::FORM_INSERT:
                $this->processPostInsertCategory();
                break;
/*
            case self::FORM_DELETE:
                $this->processPostDeleteNews();
                break; */

            case self::FORM_UPDATE:
                $this->processPostUpdate();
                break;

            default:
                return;
                break;
        }
    }

    private function processPostInsertCategory() {
        // Check that all information has been posted
        
        if (! $this->checkRequiredFields(array('FORM_FIELD_PID', 'FORM_FIELD_CATEGORYID', 'FORM_FIELD_TITLE', 'FORM_FIELD_TEXT');)) {
            $this->setFailedMessage("Kategory konnte nicht eingetragen werden");
            return;
        }

        $title      = strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE)));
        $pid        = strval(trim($this->getPostVariable(self::FORM_FIELD_PID)));

        $this->brdb->insertCategory($pid, $title);

        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return;
        }

        $this->setSuccessMessage("Kategory wurde eingetragen");
        return;
    }

    private function processPostUpdate() {
        return false;
    }




    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {}
}
?>
