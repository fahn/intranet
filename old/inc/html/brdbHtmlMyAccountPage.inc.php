<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.	*
 *														*
 * Copyright 2017										*
 *														*
 * All Rights Reserved									*
 *														*
 * Copying, distribution, usage in any form is not 		*
 * allowed without  written permit.						*
 *														*
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)	*
 *														*
 ********************************************************/

include_once '../inc/html/brdbHtmlPage.inc.php';
include_once '../inc/logic/prgUser.inc.php';
include_once '../inc/logic/tools.inc.php';

class BrdbHtmlMyAccountPage extends BrdbHtmlPage {
	private $prgElementUser;
	
	public function __construct() {
		parent::__construct();
		$this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgElementUser);
	}
	
	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';
	
	protected function htmlBodyProtectedArea() {
		$variableNameEmail 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
		$variableNameFName 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
		$variableNameLName 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
		$variableNamePassw 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD);
		$variableNamePassw2			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD2);
		$variableNameAction 		= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
		$variableNameActionLogin 	= PrgPatternElementUser::FORM_USER_ACTION_UPDATE_MY_ACCOUNT;
		$variableNameGenderMale		= PrgPatternElementUser::FORM_USER_GENDER_MALE;
		$variableNameGenderFemale	= PrgPatternElementUser::FORM_USER_GENDER_FEMALE;
		
		$variableNameEmailValue		= strval($this->prgPatternElementLogin->getLoggedInUser()->email);
		$variableNameFNameValue		= strval($this->prgPatternElementLogin->getLoggedInUser()->firstName);
		$variableNameLNameValue		= strval($this->prgPatternElementLogin->getLoggedInUser()->lastName);
?>
	<div id="formUserRegister">
		<h3>Update Your Account for Badminton Ranking</h3>
		<p>Change your email, full name, gender and password.</p>
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
				formaction	= "<?php echo BrdbHtmlPage::PAGE_MY_ACCOUNT;?>"
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