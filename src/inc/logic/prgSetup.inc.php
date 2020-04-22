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
require_once "prgSetup.inc.php";

class PrgPatternElementSetup extends APrgPatternElement
{

    private $page = "adminSetup.inc.php";

    // private $requiredFields = array("FORM_STAFF_USERID", "FORM_STAFF_POSTION", "FORM_STAFF_DESCRIPTION", "FORM_STAFF_ROW");
    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("setup");

        $this->prgElementLogin = $prgElementLogin;

    }//end __construct()


    public function processPost()
    {
        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfnoRights(["admin"]);
    }//end processPost()


    /**
     * GET-Methods
     */
    public function processGet()
    {

        $this->prgElementLogin->redirectUserIfNotLoggindIn();
        $this->prgElementLogin->redirectUserIfnoRights(["admin"]);
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

    }//end processGet()
}//end class
