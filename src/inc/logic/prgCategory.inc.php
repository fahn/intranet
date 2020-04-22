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
require_once "prgPattern.inc.php";

class PrgPatternElementCategory extends APrgPatternElement
{

    const __TABLE__             = "Category";
    // FORMS
    const FORM_FIELD_ID         = "id";
    const FORM_FIELD_PID        = "pid";
    const FORM_FIELD_TITLE      = "title";

    // FIELDS
    const FORM_ACTION = "formAction";
    const FORM_INSERT = "Insert";
    const FORM_UPDATE = "Update";
    const FORM_DELETE = "Delete";

    protected $prgElementLogin;

    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("category");
       
        // set pattern
        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FIELD_ID);
        $this->registerPostSessionVariable(self::FORM_FIELD_PID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
    }

    public function processPost():void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array("admin"));

        if (!$this->issetPostVariable(self::FORM_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }

        $action = $this->getPostVariableString(self::FORM_ACTION);

        switch ($action) {
            case self::FORM_INSERT:
                $this->processPostInsertCategory();
                break;
/*
            case self::FORM_DELETE:
                $this->processPostDeleteNews();
                break; */

            case self::FORM_UPDATE:
                $this->processPostUpdateCategory();
                break;

            default:
                return;
                break;
        }
    }

    private function processPostInsertCategory(): bool
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_FIELD_PID, self::FORM_FIELD_TITLE);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("Kategorie konnte nicht eingetragen werden");
            return false;
        }

        try {
            $cat = new Category();
            $cat->setTitle($this->getPostVariableString(self::FORM_FIELD_TITLE));
            $cat->setPid($this->getPostVariableInt(self::FORM_FIELD_PID));

            $this->brdb->insertCategory($cat);

            $this->setSuccessMessage("Kategorie wurde eingetragen");
            return true;
        }

        catch (Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot insert Kategorie. %s Details %s", $cat, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Kategorie konnte nicht eingetragen werden");
            return false;
        }
    }

    private function processPostUpdateCategory(): bool
    {
        $requireFields = array(self::FORM_FIELD_ID, self::FORM_FIELD_PID, self::FORM_FIELD_TITLE);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            return false;
        }

        try {
            $cat = new Category();
            $cat->setTitle($this->getPostVariableString(self::FORM_FIELD_TITLE));
            $cat->setPid($this->getPostVariableInt(self::FORM_FIELD_PID));
            $cat->setId($this->getPostVariableInt(self::FORM_FIELD_ID));

            $this->brdb->updateCategory($cat);

            $this->setSuccessMessage("Kategorie wurde aktualisiert");
            return true;
        }

        catch (Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot update Kategorie. %s Details %s", $cat, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Kategorie konnte nicht aktualisiert werden");
            return false;
        }
    }


    private function processPostDeleteCategory(): bool
    {
        $requireFields = array(self::FORM_FIELD_ID, self::FORM_FIELD_PID, self::FORM_FIELD_TITLE);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            return false;
        }

    }



    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {}
}

