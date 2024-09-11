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
namespace Badtra\Intranet\Logic;
require_once "PrgPattern.php";

class PrgPatternElementCup extends APrgPatternElement
{
    const __TABLE__             = "CUP";

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
        parent::__construct("cup");
       
        $this->prgElementLogin = $prgElementLogin;
/*
        $this->registerPostSessionVariable(self::FORM_FIELD_TITLE);
        $this->registerPostSessionVariable(self::FORM_FIELD_CATEGORYID);
        $this->registerPostSessionVariable(self::FORM_FIELD_TEXT);
        */
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
            /*case self::FORM_INSERT:
                $this->processPostInsertFaq();
                break;

            case self::FORM_DELETE:
                $this->processPostDeleteFaq();
                break;

            case self::FORM_UPDATE:
                $this->processPostUpdateFaq();
                break;
*/
            default:
                return;
        }

    }



    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() { }
}

