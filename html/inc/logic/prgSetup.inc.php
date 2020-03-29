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
include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class PrgPatternElementSetup extends APrgPatternElement {
    
    protected $prgElementLogin;

    protected $brdb;

    private $page = "adminSetup.inc.php";

    //private $requiredFields = array('FORM_STAFF_USERID', 'FORM_STAFF_POSTION', 'FORM_STAFF_DESCRIPTION', 'FORM_STAFF_ROW');

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("staff");

        #$this->registerPostSessionVariables($this->requiredFields, true);

        // load DB
        $this->brdb = $brdb;

        // load Login
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
