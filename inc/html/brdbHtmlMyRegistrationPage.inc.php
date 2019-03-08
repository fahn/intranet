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

include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgUser.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

class BrdbHtmlMyRegistrationPage extends BrdbHtmlPage {
    private $prgPatternElementRegister;
    private $variable;
    const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';

    public function __construct() {
        parent::__construct();
        $this->prgPatternElementRegister = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementRegister);

        $variable['NameEmail']             = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
        $variable['NameFName']             = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
        $variable['NameLName']             = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
        $variable['NameGender']            = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_GENDER);
        $variable['NamePassw']             = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD);
        $variable['NamePassw2']            = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD2);
        $variable['NameAction']         = $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
        $variable['NameActionLogin']     = PrgPatternElementUser::FORM_USER_ACTION_REGISTER;
        $variable['NameGenderMale']        = PrgPatternElementUser::FORM_USER_GENDER_MALE;
        $variable['NameGenderFemale']    = PrgPatternElementUser::FORM_USER_GENDER_FEMALE;

        $variable['NameEmailValue']        = $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_EMAIL);
        $variable['NameFNameValue']        = $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_FNAME);
        $variable['NameLNameValue']        = $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_LNAME);
        $variable['NameGenderValue']    = $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_GENDER);
    }


    public function htmlBody() {
        #$checkedAttributeGenderMale     = ($variableNameGenderValue === $variableNameGenderMale)     ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
        #$checkedAttributeGenderFemale     = ($variableNameGenderValue === $variableNameGenderFemale)     ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

        $content = $this->loadContent();
        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');

    }

    private function loadContent() {
        $this->smarty->assign(array(
            'variable' => $this->variable,
            'clubs'    => $this->loadClubList(),
        ));
        return $this->smarty->fetch('admin/register.tpl');
    }

    private function loadClubList() {
    $res = $this->brdb->selectAllClubs();
    $loop = array();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $loop[] = $dataSet; //new User($dataSet);

      }
    }

    return $loop;
  }

}

?>
