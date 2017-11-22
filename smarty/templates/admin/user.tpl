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
