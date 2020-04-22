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
require_once BASE_DIR ."/inc/logic/prgPattern.inc.php";

class PrgPatternElementStaff extends APrgPatternElement
{
    // TABLE
    const FORM_STAFF_STAFFID     = "staffId";
    const FORM_STAFF_USERID      = "userId";
    const FORM_STAFF_POSTION     = "position";
    const FORM_STAFF_DESCRIPTION = "description";
    const FORM_STAFF_ROW         = "row";
    const FORM_STAFF_SORT        = "sort";

    // POST-Fields
    const FORM_STAFF_ACTION        = "formAction";
    const FORM_STAFF_ACTION_INSERT = "Insert Staff";
    const FORM_STAFF_ACTION_UPDATE = "Edit Staff";

    // GET-Fields
    const GET_STAFF_ACTION = "action";
    const GET_STAFF_ACTION_DELETE = "delete";

    // SUCCESS
    const SUCCESS_STAFF_INSERT = "Succesfully registered account!";
    const SUCCESS_STAFF_UPDATE = "Staff wurde aktualisiert";
    const SUCCESS_STAFF_DELETE = "Succesfully deleted User from Staff!";

    // ERROR
    const ERROR_STAFF_INSERT = "Succesfully registered account!";
    const ERROR_STAFF_UPDATE   = "Staff wurde aktualisiert";
    const ERROR_STAFF_DELETE = "Succesfully deleted User from Staff!";
    const ERROR_STAFF_UPDATE_MISSING_INFORMATION = "Please provide all required information!";


    private $page = "adminStaff.inc.php";
    private $id;

    public function __construct(PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("staff");

        $this->prgElementLogin = $prgElementLogin;

        // load id
        $this->id = $this->getGetVariable("id");
    }

    public function processPost() {
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfnoRights(array("admin"));
       
       

        if (! $this->issetPostVariable(self::FORM_STAFF_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }


        $loginAction = $this->getPostVariable(self::FORM_STAFF_ACTION);
        switch ($loginAction) {
            case self::FORM_STAFF_ACTION_INSERT:
                $this->processPostInsertStaff();
                break;

            case self::FORM_STAFF_ACTION_UPDATE:
                $this->processPostUpdateStaff();
                break;
            default:
                # code...
                break;
        }
    }

    /** GET-Methods
      *
      */
    public function processGet() {
        // @TASK: Admin or not
        $loginAction = $this->getGetVariable(self::GET_STAFF_ACTION);
        switch ($loginAction) {
            case self::GET_STAFF_ACTION_DELETE:
                $this->processPostDeleteStaff();
                break;
        }
        return;
    }

    private function processPostInsertStaff() {
        if (!$this->checkRequiredFields()) {
            $this->setFailedMessage(self::ERROR_STAFF_UPDATE_MISSING_INFORMATION);
            return;
        }

        // insert
        $id = $this->insert();

        // create data array
        $data = $this->requiredFields2array();
        $data[self::FORM_STAFF_STAFFID] = $id;

        //
        if ($this->update($data)) {
            $this->setSuccessMessage(self::SUCCESS_STAFF_INSERT);
        } else {
            $this->setFailedMessage(self::ERROR_STAFF_INSERT);
        }

        $this->customRedirectArray(array(
          "page"   => "adminAllUser.php",
        ));
        return;
    }

    private function processPostUpdateStaff() {

      // Check that all information has been posted
      if (!$this->checkRequiredFields()) {
          $this->setFailedMessage(self::ERROR_STAFF_UPDATE_MISSING_INFORMATION);
          return;
      }
      // create data array
      $data = $this->requiredFields2array();
      $data[self::FORM_STAFF_STAFFID] = $this->getPostVariable(self::FORM_STAFF_STAFFID);


      if ($this->update($data)) {
          $this->setSuccessMessage(self::SUCCESS_STAFF_UPDATE);
      } else {
          $this->setFailedMessage(self::ERROR_STAFF_UPDATE);
      }

      $this->customRedirectArray(array(
        "page"   => $this->page,
        "action" => "edit",
        "id"     => $this->id,
      ));
      return;
    }

    private function processPostDeleteStaff() {
        // delete staff
        try {
            if (!$this->brdb->deleteStaff($this->id)) {
                $this->setFailedMessage("Staff konnte nicht gelöscht werden");
            }
           
            $this->setSuccessMessage(self::SUCCESS_STAFF_DELETE);
            $this->customRedirectArray(array(
                "page" => $this->page,
            ));
            return true;
        }
        catch (Exception $e){
            $this->log($this->__TABLE__, sprintf("Cannot delete Staff. %s Details %s", $this->id, $e->getMessage()), "", "POST");
            $this->setFailedMessage($e->getMessage());
            return false;
        }
    }
    /**************************************************************************/

    private function insert() {
        $this->brdb->insertStaff();

        return $this->brdb->insert_id();
    }

    private function update($data) {
        if (isset($data) && is_array($data)) {
            return $this->brdb->updateStaff($data);
        }
    }

    private function deleteStaff($staffId) {
        return $this->brdb->deleteStaff($staffId);
    }





}


