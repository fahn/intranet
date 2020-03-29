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

include_once BASE_DIR .'/inc/db/brdb.inc.php';
#include_once BASE_DIR .'/inc/model/staff.inc.php';
include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

/*
`staffId` int(11) NOT NULL,
`userId` int(11) NOT NULL,
`position` varchar(200) NOT NULL,
`description` text NOT NULL,
`row` int(11) NOT NULL,
`sort` int(11) NOT NULL
*/

class PrgPatternElementStaff extends APrgPatternElement {
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

    protected $prgElementLogin;

    protected $brdb;

    private $page = "adminStaff.inc.php";
    private $id;

    private $requiredFields = array('FORM_STAFF_USERID', 'FORM_STAFF_POSTION', 'FORM_STAFF_DESCRIPTION', 'FORM_STAFF_ROW');

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("staff");

        $this->registerPostSessionVariables($this->requiredFields, true);
        #$this->registerPostSessionVariables(self::FORM_STAFF_STAFFID);

        // load DB
        $this->brdb = $brdb;

        // load Login
        $this->prgElementLogin = $prgElementLogin;

        // load id
        $this->id = $this->getGetVariable("id");
    }

    public function processPost() {
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfnoRights(array('admin'));
        
        

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
        if($this->update($data)) {
            $this->setSuccessMessage(self::SUCCESS_STAFF_INSERT);
        } else {
            $this->setFailedMessage(self::ERROR_STAFF_INSERT);
        }

        Tools::customRedirect(array(
          'page'   => "adminAllUser.php",
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
      print_r($data);
      $data[self::FORM_STAFF_STAFFID] = $this->getPostVariable(self::FORM_STAFF_STAFFID);


      if($this->update($data)) {
          $this->setSuccessMessage(self::SUCCESS_STAFF_UPDATE);
      } else {
          $this->setFailedMessage(self::ERROR_STAFF_UPDATE);
      }

      Tools::customRedirect(array(
        'page'   => $this->page,
        'action' => 'edit',
        'id'     => $this->id,
      ));
      return;
    }

    private function processPostDeleteStaff() {
        // delete staff
        $this->brdb->deleteStaff($this->id);
        if ($this->brdb->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
        } else {
            $this->setSuccessMessage(self::SUCCESS_STAFF_DELETE);
        }

        Tools::customRedirect(array(
            'page' => $this->page,
        ));
        return true;
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

?>
