<?php

/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/
include_once('prgSetup.inc.php');

class PrgPatternElementSetup extends APrgPatternElement 
{

    private $page = "adminSetup.inc.php";

    //private $requiredFields = array('FORM_STAFF_USERID', 'FORM_STAFF_POSTION', 'FORM_STAFF_DESCRIPTION', 'FORM_STAFF_ROW');

    public function __construct(PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("setup");

        $this->prgElementLogin = $prgElementLogin;

    }

    public function processPost() {
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfnoRights(array('admin'));
    }

    /** GET-Methods
      *
      */
    public function processGet() {

        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfnoRights(array('admin'));
        // @TASK: Admin or not
        /*
        $loginAction = $this->getGetVariable(self::GET_STAFF_ACTION);
        switch ($loginAction) {
            case self::GET_STAFF_ACTION_DELETE:
                $this->processPostDeleteStaff();
                break;
        }
        return;
        */
    }

}

?>
