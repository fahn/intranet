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
declare(strict_types=1);

include_once 'prgPattern.inc.php';
include_once BASE_DIR .'/inc/db/brdb.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementNews extends APrgPatternElement 
{

    private $brdb;

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


    protected $prgElementLogin;

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin): void
    {
        parent::__construct("news");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
        $this->registerPostSessionVariable(self::FORM_FIELD_CATEGORYID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TEXT);
    }

    public function processPost():void 
    {
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
            $this->setFailedMessage("keine ID übergeben");
            return false;
        }

        $id = intval(trim($this->getPostVariable(self::FORM_FIELD_ID)));

        $this->brdb->deleteNews($id);
        if ($this->brdb->hasError()) 
        {
            $this->setFailedMessage($this->brdb->getError());
            return false;
        }

        $this->setSuccessMessage("News wurde gelöscht");
        return true;
    }

    /**
     * Insert News
     *
     * @return boolean
     */
    public function processPostInsertNews(): bool
    {
        $requireFields = array(self::FORM_FIELD_TEXT, self::FORM_FIELD_CATEGORYID, self::FORM_FIELD_TEXT);

        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) 
        {
            $this->setFailedMessage("News konnte nicht eingetragen werden");
            return false;
        }

        $newsArr = array
        (
            'newsTitle'      => strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE))),
            'newsCategoryId' => intval(trim($this->getPostVariable(self::FORM_FIELD_CATEGORYID))),
            'newsText'       => strval(trim($this->getPostVariable(self::FORM_FIELD_TEXT))),
        );
        // create NewsObject
        $newsObj = new News($newsArr);
        
        // insert
        $this->brdb->insertNews($newsObj);
        if ($this->brdb->hasError()) 
        {
            $this->setFailedMessage($this->brdb->getError());
            return false;
        }

        $this->setSuccessMessage(sprintf("News '%s' wurde eingetragen",$newsObj->getNewsTitle()));
        return true;
    }



    /**
     * Update News
     *
     * @return boolean
     */
    public function processPostUpdateNews(): bool
    {
        // Check that all information has been posted
        $requireFields = array(self::FORM_FIELD_ID, self::FORM_FIELD_TEXT, self::FORM_FIELD_CATEGORYID, self::FORM_FIELD_TEXT);

        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) 
        {
            $this->setFailedMessage("News konnte nicht aktualisiert werden.");
            return false;
        }

        $newsArr = array
        (
            'newsId'         => strval(trim($this->getPostVariable(self::FORM_FIELD_ID))),
            'newsTitle'      => strval(trim($this->getPostVariable(self::FORM_FIELD_TITLE))),
            'newsCategoryId' => intval(trim($this->getPostVariable(self::FORM_FIELD_CATEGORYID))),
            'newsText'       => strval(trim($this->getPostVariable(self::FORM_FIELD_TEXT))),
        );

        // create NewsObject
        $newsObj = new News($newsArr);

        // insert News Object
        $this->brdb->updateNewsById($newsObj);
        if ($this->brdb->hasError()) 
        {
            $this->setFailedMessage($this->brdb->getError());
            return false;
        }

        $this->setSuccessMessage(sprintf("News '%s' wurde erfolgreich geändert.", $newsObj->getNewsTitle()));
        return true;
    }
}
?>
