<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.	*
 *														*
 * Copyright 2017										*
 *														*
 * All Rights Reserved									*
 *														*
 * Copying, distribution, usage in any form is not 		*
 * allowed withoput written permit.						*
 *														*
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)	*
 *														*
 ********************************************************/

include_once '../inc/html/brdbHtmlPage.inc.php';
include_once '../inc/logic/prgUser.inc.php';
include_once '../inc/logic/tools.inc.php';

class BrdbHtmlMyRegistrationPage extends BrdbHtmlPage {
	private $prgPatternElementRegister;
	
	public function __construct() {
		parent::__construct();
		$this->prgPatternElementRegister = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgPatternElementRegister);
	}
	
	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';
	
	protected function showProtectedArea() {
		// Only Admins are allowed to register users
		return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
	}
	
	protected function htmlBodyProtectedArea() {
		$variableNameEmail 			= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
		$variableNameFName 			= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
		$variableNameLName 			= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
		$variableNameGender			= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_GENDER);
		$variableNamePassw 			= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD);
		$variableNamePassw2			= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD2);
		$variableNameAction 		= $this->prgPatternElementRegister->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
		$variableNameActionLogin 	= PrgPatternElementUser::FORM_USER_ACTION_REGISTER;
		$variableNameGenderMale		= PrgPatternElementUser::FORM_USER_GENDER_MALE;
		$variableNameGenderFemale	= PrgPatternElementUser::FORM_USER_GENDER_FEMALE;
		
		$variableNameEmailValue		= $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_EMAIL);
		$variableNameFNameValue		= $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_FNAME);
		$variableNameLNameValue		= $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_LNAME);
		$variableNameGenderValue	= $this->prgPatternElementRegister->getSessionVariable(PrgPatternElementUser::FORM_USER_GENDER);
		
		$checkedAttributeGenderMale 	= ($variableNameGenderValue === $variableNameGenderMale) 	? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		$checkedAttributeGenderFemale 	= ($variableNameGenderValue === $variableNameGenderFemale) 	? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
?>
	<div id="formUserRegister">
		<h3>Register a New User for Badminton Ranking</h3>
		<p>Type in the email, full name, gender and password of the new user.</p>
		<hr/>
		<form>
			<label for = "<?php echo $variableNameEmail;?>">Account E-mail:</label>
		    <input 
		    	type		= "text" 
		    	id			= "<?php echo $variableNameEmail;?>" 
		    	name		= "<?php echo $variableNameEmail;?>" 
		    	placeholder	= "your.name@bc-comet.de"
		    	value		= "<?php echo $variableNameEmailValue;?>"
		    />
			<label for = "<?php echo $variableNameFName;?>">First Name:</label>
		    <input 
		    	type		= "text" 
		    	id			= "<?php echo $variableNameFName;?>" 
		    	name		= "<?php echo $variableNameFName;?>" 
		    	placeholder	= "Jane"
		    	value		= "<?php echo $variableNameFNameValue;?>"
		    />
			<label for = "<?php echo $variableNameLName;?>">Last Name:</label>
		    <input 
		    	type		= "text" 
		    	id			= "<?php echo $variableNameLName;?>" 
		    	name		= "<?php echo $variableNameLName;?>" 
		    	placeholder	= "Doe"
		    	value		= "<?php echo $variableNameLNameValue;?>"
		    />
		    <label>Gender:</label>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameGenderMale;?>" 
		    	name		= "<?php echo $variableNameGender;?>" 
		    	value		= "<?php echo $variableNameGenderMale;?>"
		    	<?php echo $checkedAttributeGenderMale .rn; ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameGenderMale;?>">Male</label>
		    </div>
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameGenderFemale;?>" 
		    	name		= "<?php echo $variableNameGender;?>" 
		    	value		= "<?php echo $variableNameGenderFemale;?>"
		    	<?php echo $checkedAttributeGenderFemale . rn;  ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameGenderFemale;?>">Female</label>
		    </div>
		    </div>
		    </div>
		    <label for = "<?php echo $variableNamePassw;?>">Account Password:</label>
		    <input 
		    	type	= "password" 
		    	id		= "<?php echo $variableNamePassw;?>" 
		    	name	= "<?php echo $variableNamePassw;?>" 
		    />
		    <label for = "<?php echo $variableNamePassw2;?>">Repeat Password:</label>
		    <input 
		    	type	= "password" 
		    	id		= "<?php echo $variableNamePassw2;?>" 
		    	name	= "<?php echo $variableNamePassw2?>" 
		    />
			<input
				type		= "submit"
				name		= "<?php echo $variableNameAction;?>"
				value		= "<?php echo $variableNameActionLogin;?>"
				formaction	= "<?php echo BrdbHtmlPage::PAGE_MY_REGISTRATION;?>"
				formmethod	= "post"
			/>
		</form>
	</div>
<?php 
	}
	
	protected function htmlBodyLogin() {
?>
	<div class = "goToLogin">
		<p>You are not logged in! Please log in <a href="<?php echo BrdbHtmlPage::PAGE_INDEX;?>">here</a>!</p>
	</div>
<?php 
	}
}

?>