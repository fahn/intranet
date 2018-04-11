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

include_once '../inc/html/htmlPage.inc.php';
include_once '../inc/logic/prgLogin.inc.php';
include_once '../inc/logic/tools.inc.php';
include_once '../inc/db/brdb.inc.php';

/**
 * Implements an html page with login capabilities.
 * It also provides a protected content are which is only
 * displayed on sucessful login. the class also handles the login
 * and logout dialog. All pages that use this class and derive from it
 * will have support for the login functionality
 * 
 * @author philipp
 *
 */
abstract class AHtmlLoginPage extends HtmlPageProcessor {
	
	protected $brdb;
	protected $prgPattern;
	
	protected $prgPatternElementLogin;
	
	/**
	 * Standard Constructor for the HTML Login page. It
	 * takes care of creating the connection to the data base
	 * and stores the DB connection object into the member variable
	 * $brdb. it is protected and therefore accessible by all derived
	 * classes (pages)
	 */
	public function __construct() {
		parent::__construct();
		$this->brdb = new BrankDB();
		$this->brdb->connectAndSelectDB();
		$this->brdb->prepareCommands();
		
		$this->prgPatternElementLogin = new PrgPatternElementLogin($this->brdb);
		
		$this->prgPattern = new PrgPattern();
		$this->prgPattern->registerPrg($this->prgPatternElementLogin);
	}
	
	public function processPage() {
		// Call all prgs and process them all
		$this->prgPattern->processPRG();
		parent::processPage();
	}
	
	/**
	 * This method hands back the currently logged in user
	 * @return User the currently logged in user or null in 
	 * noone is logged in
	 */
	public function getLoggedInUser() {
		return $this->prgPatternElementLogin->getLoggedInUser();
	}
	
	/**
	 * This method handles the display of the content
	 * depending on the current user being logged in.
	 * The method also provides rendering of the protected
	 * content. This content is onyl displayed if a user is
	 * successfully logged in and other additional criteria
	 * are met.
	 */
	protected function htmlBody() {
?>
<body>	
<?php 
		$isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
		
		$this->htmlBodyHeader();
		$this->htmlBodyNav();
		$this->htmlBodyMessage();
		
		// Now decide the content on if use ris logged in or not
		if($isUserLoggedIn) {
			// in case there is a logged in user show the logout dialog
			// and display the body area with the protected content
			$this->htmlBodyLogout();
			if ($this->showProtectedArea()) {
				$this->htmlBodyProtectedArea();
			}
		} else {
			// if there is no user logged in, then show the content to
			// to perform a new login
			$this->htmlBodyLogin();
		}
		
		// Always display the unprotected content
		$this->htmlBodyUnProtectedArea();
		$this->htmlBodyFooter();
?>
</body>
<?php 
	}
	
	abstract protected function htmlBodyHeader();
	abstract protected function htmlBodyFooter();
	abstract protected function htmlBodyNav();
	
	/**
	 * This method is asked before showing the protected area
	 * in case this method returns true, then the content is shown
	 * otherwise it will be hidden.
	 * @return boolean true in case the protected area should be shown
	 */
	protected function showProtectedArea() {
		return true;
	}
	
	/**
	 * Protected area in the html body, which should not be visible to all users
	 * this area is only shown when a user is logged in and if the showProtectedArea
	 * function returns true. In all otehr cases this area is not displayed to the user
	 */
	protected function htmlBodyProtectedArea() {
	}
	
	/**
	 * This method implements html content which should always be displayed
	 * in the body area no matter who is logged in and which status is given.
	 */
	protected function htmlBodyUnProtectedArea() {
	}
	
	protected function htmlBodyMessage() {
		if ($this->prgPattern->hasStatus()) {
?>
<div class = "small">
<?php
			foreach ($this->prgPattern->getRegisteredPrgElements() as $prg) {
				echo '<p>' . $prg->getStatusMessage() . '</p>' .rn;
			}
?>
</div>
<?php 			
		}
	}
	
	protected function htmlBodyLogout() {
		$currentUserName			= $this->prgPatternElementLogin->getLoggedInUser()->getFullName();
		$variableNameAction 		= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_ACTION);
		$variableNameActionLogout 	= PrgPatternElementLogin::FORM_LOGIN_ACTION_LOGOUT;
?>
<div id = "formUserLogout" class = "small">
	<form>
		<p>Hello <?php echo $currentUserName;?>! You are logged in!</p>
		<input
			type		= "submit"
			name		= "<?php echo $variableNameAction;?>"
			value		= "<?php echo $variableNameActionLogout;?>"
			formaction	= "<?php echo BrdbHtmlPage::PAGE_INDEX;?>"
			formmethod	= "post"
		/>
	</form>
</div>

<?php 
	}
	
	protected function htmlBodyLogin() {
		$variableNameEmail 			= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
		$variableNameEmailValue		= $this->prgPatternElementLogin->safeGetSessionVariable(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
		$variableNamePassw 			= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_PASSWORD);
		$variableNameAction 		= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_ACTION);
		$variableNameActionLogin 	= PrgPatternElementLogin::FORM_LOGIN_ACTION_LOGIN;
		?>
<div id="formUserLogin">
	<h3>Login to the Badminton Ranking</h3>
	<p>Type in your email and password.</p>
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
	    <label for = "<?php echo $variableNamePassw;?>">Account Password:</label>
	    <input 
	    	type	= "password" 
	    	id		= "<?php echo $variableNamePassw;?>" 
	    	name	= "<?php echo $variableNamePassw;?>" 
	    >
		<input
			type		= "submit"
			name		= "<?php echo $variableNameAction;?>"
			value		= "<?php echo $variableNameActionLogin;?>"
			formaction	= "<?php echo BrdbHtmlPage::PAGE_INDEX;?>"
			formmethod	= "post"
		/>
	</form>
	<hr/>
	<p>You are about to login to Badminton Ranking. By logging in you are
		accepting the terms and conditions of Badminton Ranking. For further
		information either consult the License or Manual. In case you cannot
		agree, we kindly ask to leave the page immediately.</p>
</div>
<?php 
	}
}
?>