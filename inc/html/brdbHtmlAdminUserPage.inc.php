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
		$variableNameEmail 					= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
		$variableNameFName 					= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
		$variableNameLName 					= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
		$variableNameGender					= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_GENDER);
		$variableNameIsPlayer				= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_IS_PLAYER);
		$variableNameIsAdmin				= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_IS_ADMIN);
		$variableNameIsReporter				= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_IS_REPORTER);
		$variableNamePassw 					= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD);
		$variableNamePassw2					= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD2);
		$variableNameAction 				= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
		
		$variableNameActionUpdateAccount 	= PrgPatternElementUser::FORM_USER_ACTION_UPDATE_ACCOUNT;
		$variableNameGenderMale				= PrgPatternElementUser::FORM_USER_GENDER_MALE;
		$variableNameGenderFemale			= PrgPatternElementUser::FORM_USER_GENDER_FEMALE;
		
		$variableNameIsYes					= PrgPatternElementUser::FORM_USER_IS_YES;
		$variableNameIsNo					= PrgPatternElementUser::FORM_USER_IS_NO;
		
		$adminUser = $this->prgElementUser->getAdminUser();
		
		$variableNameEmailValue		= strval($adminUser->email);
		$variableNameFNameValue		= strval($adminUser->firstName);
		$variableNameLNameValue		= strval($adminUser->lastName);
		$variableNameGenderValue	= strval($adminUser->gender);
		$variableNamePlayerValue	= strval($adminUser->isPlayer());
		$variableNameAdminValue		= strval($adminUser->isAdmin());
		$variableNameReporterValue	= strval($adminUser->isReporter());
		
		$checkedAttributeGenderMale 	= ($variableNameGenderValue === $variableNameGenderMale) 	? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		$checkedAttributeGenderFemale 	= ($variableNameGenderValue === $variableNameGenderFemale) 	? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

		$checkedAttributeIsPlayerYes 	= ($variableNamePlayerValue == 1) 		? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		$checkedAttributeIsPlayerNo 	= ($variableNamePlayerValue == 0) 		? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

		$checkedAttributeIsAdminYes 	= ($variableNameAdminValue == 1) 		? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		$checkedAttributeIsAdminNo 		= ($variableNameAdminValue == 0) 		? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		
		$checkedAttributeIsReporterYes 	= ($variableNameReporterValue == 1) 	? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		$checkedAttributeIsReporterNo 	= ($variableNameReporterValue == 0) 	? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		
?>
	<div id="formUserRegister">
		<h3>Administrate User Account for Badminton Ranking</h3>
		<p>Change the email, full name, gender and password and all other settings.</p>
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
		    <label>Is Player:</label>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameIsPlayer.$variableNameIsYes;?>" 
		    	name		= "<?php echo $variableNameIsPlayer;?>" 
		    	value		= "<?php echo $variableNameIsYes;?>"
		    	<?php echo $checkedAttributeIsPlayerYes .rn; ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameIsPlayer.$variableNameIsYes;?>">Yes</label>
		    </div>
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameIsPlayer.$variableNameIsNo;?>" 
		    	name		= "<?php echo $variableNameIsPlayer;?>" 
		    	value		= "<?php echo $variableNameIsNo;?>"
		    	<?php echo $checkedAttributeIsPlayerNo. rn;  ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameIsPlayer.$variableNameIsNo;?>">No</label>
		    </div>
		    </div>
		    </div>
		    <label>Is Admin:</label>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameIsAdmin.$variableNameIsYes;?>" 
		    	name		= "<?php echo $variableNameIsAdmin;?>" 
		    	value		= "<?php echo $variableNameIsYes;?>"
		    	<?php echo $checkedAttributeIsAdminYes .rn; ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameIsAdmin.$variableNameIsYes;?>">Yes</label>
		    </div>
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameIsAdmin.$variableNameIsNo;?>" 
		    	name		= "<?php echo $variableNameIsAdmin;?>" 
		    	value		= "<?php echo $variableNameIsNo;?>"
		    	<?php echo $checkedAttributeIsAdminNo. rn;  ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameIsAdmin.$variableNameIsNo;?>">No</label>
		    </div>
		    </div>
		    </div>
		    <label>Is Reporter:</label>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameIsReporter.$variableNameIsYes;?>" 
		    	name		= "<?php echo $variableNameIsReporter;?>" 
		    	value		= "<?php echo $variableNameIsYes;?>"
		    	<?php echo $checkedAttributeIsReporterYes .rn; ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameIsReporter.$variableNameIsYes;?>">Yes</label>
		    </div>
		    <div class = "radioCell">
		    <input 
		    	type		= "radio" 
		    	id			= "<?php echo $variableNameIsReporter.$variableNameIsNo;?>" 
		    	name		= "<?php echo $variableNameIsReporter;?>" 
		    	value		= "<?php echo $variableNameIsNo;?>"
		    	<?php echo $checkedAttributeIsReporterNo. rn;  ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameIsReporter.$variableNameIsNo;?>">No</label>
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
				value		= "<?php echo $variableNameActionUpdateAccount;?>"
				formaction	= "<?php echo BrdbHtmlPage::PAGE_ADMIN_USER;?>"
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