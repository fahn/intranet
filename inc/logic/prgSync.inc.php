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

include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementSync extends APrgPatternElement {
    private $brdb;
    protected $prgElementLogin;


    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("image");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;
    }

    public function processPost() {
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        $isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if (!$this->prgElementLogin->isUserLoggedIn()) {
            return;
        }

        if (!$isUserReporter) {
            return;
        }
    }


    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin     = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if ( !$this->prgElementLogin->isUserLoggedIn() || !$isUserAdmin ) {
            return;
        }
        
        #die("12345");
        
        $action = strval(trim($this->getGetVariable('action')));

        switch ($action) {
            case 'delete':
                $this->processGetDeleteImage($this->getGetVariable('id'));
                break;
                
            default:
                break;
        }
        return;
    }

}
?>
