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
namespace Badtra\Intranet\Logic;
require_once "PrgPattern.php";

class PrgPatternElementSupport extends APrgPatternElement
{
    const FORM_FIELD_SUBJECT            = "subject";
    const FORM_FIELD_MESSAGE            = "message";
    const FORM_FIELD_ACTION             = "formAction";
    const FORM_VALUE_ACTION_CONTACT_US  = "Contact Us";

    // Errors that can be set by methods of this class
    const SUCCESS_MESSAGE     = "Deine Anfrage wurde versendet.";
    const ERROR_MESSAGE       = "Please provide all required information!";

    protected PrgPatternElementLogin $prgElementLogin;


    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("support");

        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FIELD_SUBJECT);
        $this->registerPostSessionVariable(self::FORM_FIELD_MESSAGE);
    }

    public function processPost(): void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        $requireFields = array(self::FORM_FIELD_ACTION);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage("Kein Formular.");
            return;
        }
       
        $loginAction = strval(trim($this->getPostVariable(self::FORM_FIELD_ACTION)));
        if (($loginAction === self::FORM_VALUE_ACTION_CONTACT_US))
        {
            $this->processPostContactUs();
        }
    }

    public function processPostContactUs(): bool
    {
        $requireFields = array(self::FORM_FIELD_SUBJECT, self::FORM_FIELD_MESSAGE, self::FORM_FIELD_MESSAGE);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields))
        {
            $this->setFailedMessage(self::ERROR_MESSAGE);
            return false;
        }
       
        try {
            // page title
            $pageTitle = $this->settings->getSettingInt("SITE_NAME");
            // get ini values
            $supporter = $this->settings->getSettingInt("SUPPORT_USER");
            $user = $this->brdb->selectUserById($supporter);

            $subject   = sprintf("%s %s", $pageTitle, $this->getPostVariableString(self::FORM_FIELD_SUBJECT));
            $message   = sprintf("<p>%s</p>", $this->getPostVariableString(self::FORM_FIELD_MESSAGE));
            $to        = $user["email"];
            $name      = $user["fullname"];
            $preheader = "Anfrage über den Support";

            $message  .= "<h2>DEBUG:</h2>";
            $message  .= "Zeit: ". date("d.m.Y H:i") ."<br>";
            $message  .= sprintf("IP: %s <br>", $this->getUserIPAdress());
            $message  .= sprintf("USER: <a href=\"%s\">%s</a>", $this->linkTo(
                array(
                    "page" => "user.php",
                    "id" => $this->prgElementLogin->getLoggedInUser()->getID()
                )
            ), $this->prgElementLogin->getLoggedInUser()->getFullname());

            if (!$this->sendMail($to, $name, $subject, $preheader, $message, false, false))
            {
                throw new \Exception("Die Nachricht konnte nicht versendet werden.");
            }

            $this->setSuccessMessage(self::SUCCESS_MESSAGE);
            return true;

        } catch (\Exception $e)
        {
            $this->log($this->__TABLE__, sprintf("Cannot add new SupportCase. Details %s",$e->getMessage()), "", "POST");
            $this->setFailedMessage("Die Nachricht konnte nicht versendet werden.");
            return false;
        }
    }
}


