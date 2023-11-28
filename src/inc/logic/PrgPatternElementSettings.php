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

class PrgPatternElementSettings extends APrgPatternElement
{
    const __TABLE__ = "Settings";

    const FORM_ACTION = "formAction";
    const FORM_INSERT = "Insert";
    const FORM_UPDATE = "Update";
    const FORM_DELETE = "Delete";

    const FORM_FIELD_ID       = "id";
    const FORM_FIELD_NAME     = "name";
    const FORM_FIELD_DATATYPE = "datatype";
    const FORM_FIELD_VALUE    = "value";

    // PATTERN
    protected PrgPatternElementLogin $prgElementLogin;


    public function __construct(PrgPatternElementLogin $prgElementLogin = null)
    {
        parent::__construct("settings");

        $this->prgElementLogin = $prgElementLogin;

        // pattern
        if (isset($prgElementLogin)) {
            $this->prgElementLogin = $prgElementLogin;
        }

        // fields
        $this->registerPostSessionVariable(self::FORM_FIELD_NAME);
        $this->registerPostSessionVariable(self::FORM_FIELD_ID);
        $this->registerPostSessionVariable(self::FORM_FIELD_DATATYPE);
        $this->registerPostSessionVariable(self::FORM_FIELD_VALUE);

    }//end __construct()


    public function processPost()
    {
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(["admin"]);

        if (!$this->issetPostVariable(self::FORM_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }

        $action = strval(trim($this->getPostVariable(self::FORM_ACTION)));

        switch ($action) {
            case self::FORM_INSERT:
                $this->processPostInsertSettings();
                break;

        case self::FORM_DELETE:
            $this->processPostDeleteSettings();
break;

        case self::FORM_UPDATE:
            $this->processPostUpdateSettings();
break;

        default:
return;
                break;
        }
    }//end processPost()


    private function processPostInsertSettings()
    {
        $requireFields = [
            self::FORM_FIELD_NAME,
            self::FORM_FIELD_DATATYPE,
            self::FORM_FIELD_VALUE,
        ];
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
            $this->setFailedMessage("keine Werte übergeben");
            return false;
        }

        try {
            $setting = new \Badtra\Intranet\Model\Setting();
            $setting->setName($this->getPostVariableString(self::FORM_FIELD_NAME));
            $setting->setDataType($this->getPostVariableString(self::FORM_FIELD_DATATYPE));

            // value
            $value = trim($this->getPostVariable(self::FORM_FIELD_VALUE));
            if (is_array($value)) {
                $value = serialize($value);
            }
            $setting->setValue($value);

            $this->brdb->insertSettings($setting);

            $this->setSuccessMessage("FAQ wurde eingetragen");
            return true;
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot insert Setting. %s Details %s", $setting, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Setting konnte nicht eingetragen werden");
            return false;
        }//end try
    }//end processPostInsertSettings()


    private function processPostUpdateSettings()
    {
        $requireFields = [
            self::FORM_FIELD_ID,
            self::FORM_FIELD_NAME,
            self::FORM_FIELD_DATATYPE,
            self::FORM_FIELD_VALUE,
        ];
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
            $this->setFailedMessage("keine ID übergeben");
            return false;
        }

        try {
            $setting = new \Badtra\Intranet\Model\Setting();
            $setting->setId(intval(trim($this->getPostVariable(self::FORM_FIELD_ID))));
            $setting->setName(strval(trim($this->getPostVariable(self::FORM_FIELD_NAME))));
            $setting->setDataType(strval(trim($this->getPostVariable(self::FORM_FIELD_DATATYPE))));

            // value
            $value = trim($this->getPostVariable(self::FORM_FIELD_VALUE));
            if (is_array($value)) {
                $value = serialize($value);
            }
            $setting->setValue($value);

            $this->brdb->updateSettings($setting);

            $this->setSuccessMessage("FAQ wurde eingetragen");
            return true;
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot update Setting. %s Details %s", $setting, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Setting konnte nicht geändert werden");
            return false;
        }//end try
    }//end processPostUpdateSettings()


    private function processPostDeleteSettings():bool
    {
        $requireFields = [self::FORM_FIELD_ID];
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) {
            $this->setFailedMessage("keine ID übergeben");
            return false;
        }

        $id = intval(trim($this->getPostVariable(self::FORM_FIELD_ID)));

        try {
            $this->brdb->deleteSettings($id);
            return true;
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot delete Setting. %d Details %s", $id, $e->getMessage()), "", "POST");
            $this->setFailedMessage("Setting konnte nicht geändert werden");
            return false;
        }
    }//end processPostDeleteSettings()


    /**
     * {@inheritDoc}
     *
     * @see IPrgPatternElement::processGet()
     */
    public function processGet()
    {

    }//end processGet()
}//end class
