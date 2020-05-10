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
use \Badtra\Intranet\Logic\PrgPatternElementUser;

class MyAccountPage extends BrdbHtmlPage
{
    protected PrgPatternElementUser $prgElementUser;

    public function __construct()
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }//end __construct()


    public function defaultView(): string
    {
        $variableName = [
            "Email" => $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL),
            "FName"=> $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME),
            "LName"=> $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME),
            "Passw"=> $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_NEW_PASSWORD),
            "Passw2"       => $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_REPEAT_NEW_PASSWORD),
            "Action"       => $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION),
            "ActionLogin"  => PrgPatternElementUser::FORM_USER_ACTION_UPDATE_MY_ACCOUNT,
            "GenderMale"   => PrgPatternElementUser::FORM_USER_GENDER_MALE,
            "GenderFemale" => PrgPatternElementUser::FORM_USER_GENDER_FEMALE,
            
            "EmailValue"    => strval($this->prgPatternElementLogin->getLoggedInUser()->email),
            "FNameValue"    => strval($this->prgPatternElementLogin->getLoggedInUser()->firstName),
            "LNameValue"    => strval($this->prgPatternElementLogin->getLoggedInUser()->lastName),
            "playerIdValue"=> strval($this->prgPatternElementLogin->getLoggedInUser()->playerId),
            "phoneValue"    => strval($this->prgPatternElementLogin->getLoggedInUser()->phone),
            "bdayValue"     => strval($this->prgPatternElementLogin->getLoggedInUser()->bday),
            "genderValue"   => strval($this->prgPatternElementLogin->getLoggedInUser()->gender),
        ];

        $this->smarty->assign(
            [
                "variableName" => $variableName
            ]
        );

        return $this->smartyFetchWrap("user/myAccount.tpl");
    }//end htmlBody()

}//end class
