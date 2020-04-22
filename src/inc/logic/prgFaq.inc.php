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

class PrgPatternElementFaq extends APrgPatternElement
{
    const __TABLE__             = "FAQ";

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

    protected PrgPatternElementLogin $prgElementLogin;


    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("faq");
       
        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
        $this->registerPostSessionVariable(self::FORM_FIELD_CATEGORYID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TEXT);
    }

    public function processPost(): void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
       
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array("reporter", "admin"), "or");

        if (!$this->issetPostVariable(self::FORM_ACTION))
        {
            $this->setFailedMessage("Kein Formular.");
            return;
        }

        $action = strval(trim($this->getPostVariable(self::FORM_ACTION)));

        switch ($action)
        {
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

    private function processPostDeleteFaq(): bool
    {

        $requireFields = array(self::FORM_FIELD_ID);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("keine FAQ-ID übergeben");
            return false;
        }

        try {
            $id = $this->getPostVariableInt(self::FORM_FIELD_ID);
            $this->brdb->deleteFaq($id);
            $this->setSuccessMessage("FAQ wurde gelöscht");
            return true;

        } catch (Exception $e)
        {
            $this->setFailedMessage($this->brdb->getError());
            return false;
        }
        unset($requireFields, $id);
    }

    public function processPostInsertFaq(): bool
    {
        $requireFields = array(self::FORM_FIELD_TITLE, self::FORM_FIELD_CATEGORYID, self::FORM_FIELD_TEXT);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("FAQ konnte nicht eingetragen werden");
            return false;
        }

        try {
            $faq = new Faq();
            $faq->setTitle($this->getPostVariableString(self::FORM_FIELD_TITLE));
            $faq->setCategoryId($this->getPostVariableInt(self::FORM_FIELD_CATEGORYID));
            $faq->setText($this->getPostVariableString(self::FORM_FIELD_TEXT));

            $this->brdb->insertFaq($faq);

            $this->setSuccessMessage("FAQ wurde eingetragen");
            return true;

        }
        catch (Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot insert FAQ. %s Details %s", $faq, $e->getMessage()), "", "POST");
            $this->setFailedMessage("FAQ konnte nicht eingetragen werden");
            return false;
        }       
    }



    /**
     * Update Faq
     *
     * @return boolean
     */
    public function processPostUpdateFaq(): bool
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_FIELD_ID, self::FORM_FIELD_TITLE, self::FORM_FIELD_CATEGORYID, self::FORM_FIELD_TEXT);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("FAQ konnte nicht aktualisiert werden.");
            return false;
        }

        try {
            $faq = new Faq();
            $faq->setFaqId($this->getPostVariableInt(self::FORM_FIELD_ID));
            $faq->setTitle($this->getPostVariableString(self::FORM_FIELD_TITLE));
            $faq->setCategoryId($this->getPostVariableInt(self::FORM_FIELD_CATEGORYID));
            $faq->setText($this->getPostVariableString(self::FORM_FIELD_TEXT));

            $this->brdb->updateFaqById($faq);

            $this->setSuccessMessage("FAQ wurde erfolgreich geändert.");
            return true;
        }
        catch (Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot update FAQ. %s Details %s", $faq, $e->getMessage()), "", "POST");
            $this->setFailedMessage("FAQ konnte nicht aktualisiert werden");
            return false;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() { }
}

