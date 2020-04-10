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
include_once 'prgPattern.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementNotification extends APrgPatternElement {
    protected PrgPatternElementLogin $prgElementLogin;

    public function __construct(PrgPatternElementLogin $prgElementLogin) 
    {
        parent::__construct("player");

        $this->prgElementLogin = $prgElementLogin;
    }

    public function processPost():void
    {

    }

    public function processGet():void
    {

    }

}