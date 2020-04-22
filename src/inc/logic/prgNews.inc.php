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

class PrgPatternElementNews extends APrgPatternElement
{
    const __TABLE__             = "News";

    // FORMS
    const FORM_FIELD_ID         = "newsId";
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
        parent::__construct("news");

        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
        $this->registerPostSessionVariable(self::FORM_FIELD_CATEGORYID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TEXT);
    }

    public function processPost():void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
       
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array("reporter", "admin"), "or");

        if (!$this->issetPostVariable(self::FORM_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }

        $action = strval(trim($this->getPostVariable(self::FORM_ACTION)));

        switch ($action) {
            case self::FORM_INSERT:
                $this->processPostInsertNews();
                break;

            case self::FORM_DELETE:
                $this->processPostDeleteNews();
                break;

            case self::FORM_UPDATE:
                $this->processPostUpdateNews();
                break;

            default:
                return;
                break;
        }

    }

    private function processPostDeleteNews(): bool
    {
        $requireFields = array(self::FORM_FIELD_ID);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("keine News-ID übergeben");
            return false;
        }

        try {
            $id = intval(trim($this->getPostVariable(self::FORM_FIELD_ID)));
            $this->brdb->deleteNews($id);
            $this->setSuccessMessage("FAQ wurde gelöscht");
            return true;

        } catch (Exception $e)
        {
            $this->setFailedMessage($e->getMessage());
            return false;
        }
        unset($requireFields, $id);
    }

    /**
     * Insert News
     *
     * @return boolean
     */
    public function processPostInsertNews(): bool
    {
        $requireFields = array(self::FORM_FIELD_TITLE, self::FORM_FIELD_CATEGORYID, self::FORM_FIELD_TEXT);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("News konnte nicht eingetragen werden");
            return false;
        }

        try {
            $news = new News();
            $news->setTitle(strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE))));
            $news->setCategoryId(intval(trim($this->getPostVariable(self::FORM_FIELD_CATEGORYID))));
            $news->setText(strval(trim($this->getPostVariable(self::FORM_FIELD_TEXT))));

            $this->brdb->insertNews($news);

            $this->setSuccessMessage(sprintf("News "%s" wurde eingetragen", $news->getTitle()));
            return true;

        }
        catch (Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot insert News. %s Details %s", $news, $e->getMessage()), "", "POST");
            $this->setFailedMessage("News konnte nicht eingetragen werden");
            return false;
        }
    }



    /**
     * Update News
     *
     * @return boolean
     */
    public function processPostUpdateNews(): bool
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_FIELD_ID, self::FORM_FIELD_TITLE, self::FORM_FIELD_CATEGORYID, self::FORM_FIELD_TEXT);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("News konnte nicht aktualisiert werden.");
            return false;
        }

        try {
            $news = new News();
            $news->setNewsId(intval(trim($this->getPostVariable(self::FORM_FIELD_ID))));
            $news->setTitle(strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE))));
            $news->setCategoryId(intval(trim($this->getPostVariable(self::FORM_FIELD_CATEGORYID))));
            $news->setText(strval(trim($this->getPostVariable(self::FORM_FIELD_TEXT))));

            $this->brdb->updateNewsById($news);

            $this->setSuccessMessage("News wurde erfolgreich geändert.");
            return true;
        }
        catch (Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot update News. %s Details %s", $news, $e->getMessage()), "", "POST");
            $this->setFailedMessage("FAQ konnte nicht aktualisiert werden");
            return false;
        }
    }
}

