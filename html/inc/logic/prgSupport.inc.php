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
declare(strict_types=1);
include_once 'prgPattern.inc.php';

include_once BASE_DIR .'/inc/db/brdb.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementSupport extends APrgPatternElement 
{
  // DB
  private $brdb;
  // TOOLS
  private $tools;

  const FORM_FIELD_SUBJECT            = "subject";
  const FORM_FIELD_MESSAGE            = "message";
  const FORM_FIELD_ACTION             = "formAction";
  const FORM_VALUE_ACTION_CONTACT_US  = "Contact Us";

  // Errors that can be set by methods of this class
  const SUCCESS_MESSAGE     = "Deine Anfrage wurde versendet.";
  const ERROR_MESSAGE       = "Please provide all required information!";

  protected $prgElementLogin;

  public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin): void
  {
    parent::__construct("support");

    $this->brdb            = $brdb;
    $this->prgElementLogin = $prgElementLogin;

    $this->registerPostSessionVariable(self::FORM_FIELD_SUBJECT);
    $this->registerPostSessionVariable(self::FORM_FIELD_MESSAGE);

    // tools
    $this->tools = new Tools();
  }

    public function processPost(): void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        if (! $this->issetPostVariable(self::FORM_FIELD_ACTION)) 
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

    public function processPostContactUs(): void 
    {
        // Check that all information has been posted
        if (! $this->issetPostVariable(self::FORM_FIELD_SUBJECT) ||
            ! $this->issetPostVariable(self::FORM_FIELD_MESSAGE) ) {
            $this->setFailedMessage(self::ERROR_MESSAGE);
            return;
        }

        // get ini values
        $supportIni = $this->tools->getIniValue('Support');

        $subject   = $this->tools->getIniValue('pageTitle') . strval(trim($this->getPostVariable(self::FORM_FIELD_SUBJECT)));
        $message   = "<p>". strval(trim($this->getPostVariable(self::FORM_FIELD_MESSAGE))) ."</p>";
        $to        = $supportIni['receiverEmail'];
        $name      = $supportIni['receiverName'];
        $preheader = "Anfrage über den Support";

        $message  .= "<h2>DEBUG:</h2>";
        $message  .= "Zeit: ". date("d.m.Y H:i") ."<br>";
        $message  .= sprintf("IP: %s <br>", $this->tools->getUserIPAdress());
        $message  .= sprintf('USER: <a href="%s">%s</a>', $this->tools->linkTo(array('page' => 'user.php', 'id' => $this->prgElementLogin->getLoggedInUser()->getID())), $this->prgElementLogin->getLoggedInUser()->getFullname());

        if (!$this->tools->sendMail($to, $name, $subject, $preheader, $message, false, false)) {
            $this->setFailedMessage('Die Nachricht konnte nicht versendet werden.');
            return;
        }

        $this->setSuccessMessage(self::SUCCESS_MESSAGE);
        return;
    }
}

?>
