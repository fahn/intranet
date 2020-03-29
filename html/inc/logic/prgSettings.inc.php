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
include_once 'prgPattern.inc.php';

include_once BASE_DIR .'/inc/db/brdb.inc.php';
include_once BASE_DIR .'/inc/model/user.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementTournament extends APrgPatternElement {

    private $brdb;
    private $tools;

    protected $prgElementLogin;

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("tournament");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;


        $this->tools = new Tools();

    }

    public function processPost() {}


    

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {}
}
?>
