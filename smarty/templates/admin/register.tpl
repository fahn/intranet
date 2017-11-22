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
