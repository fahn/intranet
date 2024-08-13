<?php
/**
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
namespace Badtra\Intranet\Html;

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementSupport;

class SupportPage extends BrdbHtmlPage
{

    private PrgPatternElementSupport $prgElementSupport;


    public function __construct()
    {
        parent::__construct();

        $this->prgElementSupport = new PrgPatternElementSupport($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementSupport);

    }//end __construct()


    public function showSupportForm(): string 
    {

        $action = "";
        $message = "";
        $subject = "";

        switch ($action) {
            case "new_player":
                $message = sprintf("Hallo,&#13;ich möchte hiermit folgenden SpielerIn melden.&#13;&#13;Name:???&#13;Spielernummer:???&#13;Verein:???&#13;Vereinsnummer:???&#13;");
                $subject = "Neuer Spieler";
                break;

            case "register":
                $message = sprintf("Hallo,&#13;ich möchte hiermit registrieren.&#13;&#13;Name:???&#13;Spielernummer:???&#13;Verein:???&#13;");
                $subject = "Registrierung";
                break;

            default:
                $message = "";
                break;
        }

        $this->smarty->assign(
            [
                "action"  => $action,
                "subject" => $subject,
                "message" => $message,
            ]
        );

        return $this->smartyFetchWrap("support.tpl");
    }//end htmlBody()

}//end class
