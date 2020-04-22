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
require_once "brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgUser.inc.php";

class BrdbHtmlMyAccountPage extends BrdbHtmlPage
{
    private $prgElementUser;
   
    public function __construct()
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }

    public function htmlBody(): void
    {
        $variableName["Email"]          = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
        $variableName["FName"]          = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
        $variableName["LName"]          = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
        $variableName["Passw"]          = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_NEW_PASSWORD);
        $variableName["Passw2"]         = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_REPEAT_NEW_PASSWORD);
        $variableName["Action"]         = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
        $variableName["ActionLogin"]    = PrgPatternElementUser::FORM_USER_ACTION_UPDATE_MY_ACCOUNT;
        $variableName["GenderMale"]     = PrgPatternElementUser::FORM_USER_GENDER_MALE;
        $variableName["GenderFemale"]   = PrgPatternElementUser::FORM_USER_GENDER_FEMALE;

        $variableName["EmailValue"]     = strval($this->prgPatternElementLogin->getLoggedInUser()->email);
        $variableName["FNameValue"]     = strval($this->prgPatternElementLogin->getLoggedInUser()->firstName);
        $variableName["LNameValue"]     = strval($this->prgPatternElementLogin->getLoggedInUser()->lastName);
        $variableName["playerIdValue"]  = strval($this->prgPatternElementLogin->getLoggedInUser()->playerId);
        $variableName["phoneValue"]     = strval($this->prgPatternElementLogin->getLoggedInUser()->phone);
        $variableName["bdayValue"]      = strval($this->prgPatternElementLogin->getLoggedInUser()->bday);
        $variableName["genderValue"]    = strval($this->prgPatternElementLogin->getLoggedInUser()->gender);

        $content = $this->TMPL_editOwnAccount($variableName);

        $this->smarty->assign(array(
            "content" => $content,
        ));

        $this->smarty->display("index.tpl");
    }

    private function TMPL_editOwnAccount(array $vars): string
    {
        $this->smarty->assign(array(
            "vars" => $vars,
        ));
       
        return $this->smarty->fetch("user/myAccount.tpl");
    }

}

