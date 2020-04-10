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
include_once('htmlLoginPage.inc.php');

class BrdbHtmlPage extends AHtmlLoginPage 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function processPage() 
    {
        // Call all prgs and process them all
        $this->prgPattern->processPRG();

        parent::processPage();
    }

}

?>
