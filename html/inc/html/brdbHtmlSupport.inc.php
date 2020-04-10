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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgSupport.inc.php';


class brdbHtmlSupport extends BrdbHtmlPage 
{
    private $prgElementSupport;

    public function __construct()
    {
        parent::__construct();

        $this->prgElementSupport = new PrgPatternElementSupport($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementSupport);

    }

    protected function htmlBody(): void
    {
        $content = $this->loadContent();

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent(?string $param1 = null): string
    {
        $action = $param1 == null ? $this->prgElementSupport->get('action') : $param1;

        $message = "";
        $subject = "";

        switch ($action) 
        {
            case 'new_player':
                $message = sprintf('Hallo,&#13;ich möchte hiermit folgenden SpielerIn melden.&#13;&#13;Name:???&#13;Spielernummer:???&#13;Verein:???&#13;Vereinsnummer:???&#13;');
                $subject = "Neuer Spieler";
                break;

            case 'register':
                $message = sprintf('Hallo,&#13;ich möchte hiermit registrieren.&#13;&#13;Name:???&#13;Spielernummer:???&#13;Verein:???&#13;');
                $subject = "Registrierung";
                break;

            default:
                $message = "";
                break;
        }

        $this->smarty->assign(array(
            'action'  => $action,
            'subject' => $subject,
            'message' => $message,
        ));

        return $this->smarty->fetch('support.tpl');
    }

    public function register(): string
    {
        return $this->loadContent('register');
    }
}
?>
