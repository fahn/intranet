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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgUser.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminUserPage extends BrdbHtmlPage {
    private $prgElementUser;

    public function __construct() {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }

    protected function showProtectedArea() {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }

    const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';

    protected function htmlBodyProtectedArea() {
/*
        #$variableNameEmail                     = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
       # $variableNameFName                     = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
        #$variableNameLName                     = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
        $variableNameGender                    = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_GENDER);
        $variableNameIsPlayer                = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_IS_PLAYER);
        #$variableNameIsAdmin                = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_IS_ADMIN);
        #$variableNameIsReporter                = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_IS_REPORTER);
        $variableNamePassw                     = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD);
        $variableNamePassw2                    = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD2);
        #$variableNameAction                 = $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);

        $variableNameActionUpdateAccount     = PrgPatternElementUser::FORM_USER_ACTION_UPDATE_ACCOUNT;
        $variableNameGenderMale                = PrgPatternElementUser::FORM_USER_GENDER_MALE;
        $variableNameGenderFemale            = PrgPatternElementUser::FORM_USER_GENDER_FEMALE;

        #$variableNameIsYes                    = PrgPatternElementUser::FORM_USER_IS_YES;
        $variableNameIsNo                    = PrgPatternElementUser::FORM_USER_IS_NO;

        $adminUser = $this->prgElementUser->getAdminUser();

        $variableNameEmailValue        = strval($adminUser->email);
        #$variableNameFNameValue        = strval($adminUser->firstName);
        $variableNameLNameValue        = strval($adminUser->lastName);
        $variableNameGenderValue    = strval($adminUser->gender);
        $variableNamePlayerValue    = strval($adminUser->isPlayer());
        $variableNameAdminValue        = strval($adminUser->isAdmin());
        $variableNameReporterValue    = strval($adminUser->isReporter());

        #$checkedAttributeGenderMale     = ($variableNameGenderValue === $variableNameGenderMale)     ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
        #$checkedAttributeGenderFemale     = ($variableNameGenderValue === $variableNameGenderFemale)     ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

        #$checkedAttributeIsPlayerYes     = ($variableNamePlayerValue == 1)         ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
        $checkedAttributeIsPlayerNo     = ($variableNamePlayerValue == 0)         ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

        #$checkedAttributeIsAdminYes     = ($variableNameAdminValue == 1)         ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
        #$checkedAttributeIsAdminNo         = ($variableNameAdminValue == 0)         ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

        #$checkedAttributeIsReporterYes     = ($variableNameReporterValue == 1)     ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
        $checkedAttributeIsReporterNo     = ($variableNameReporterValue == 0)     ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

*/
    }

    public function htmlBody() {

    $this->smarty->assign(array(
      'content' => $this->loadContent(),
    ));

    $this->smarty->display('index.tpl');
  }


  private function loadContent() {
    return $this->smarty->fetch('admin/users.tpl');
  }
}

?>
