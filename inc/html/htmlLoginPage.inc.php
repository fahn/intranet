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
        
        /* SQL CONNECTION */
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
        
        $this->htmlContent();
        $this->htmlBody();
	}
	
	/**
	 * This method hands back the currently logged in user
	 * @return User the currently logged in user or null in 
	 * noone is logged in
	 */
	public function getLoggedInUser() {
		return $this->prgPatternElementLogin->getLoggedInUser();
	}
    
    
    protected function htmlContent() {
        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
        $user           = "";
        if($isUserLoggedIn) {
            $user = $this->prgPatternElementLogin->getLoggedInUser()->getFullName();
        }
        $this->smarty->assign(array(
            'currentUserName' => $user,
            'isUserLoggedIn'  => $isUserLoggedIn,
        ));
        
        #die('-'.strlen($this->content).'-');
        
        if(strlen($this->content) == 0) {
            $this->content = $this->defaultContent();
            #$this->smarty->assign(array(
            #    'content' => $this->content,
            #));
        }
    }
    
    private function defaultContent() {
        
        return $this->smarty->fetch('default.tpl');
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
		$isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
		
		// Now decide the content on if use ris logged in or not
		if($isUserLoggedIn) {
			// in case there is a logged in user show the logout dialog
			// and display the body area with the protected content
			$this->smarty->display('index.tpl');
		} else {
			// if there is no user logged in, then show the content to
			// to perform a new login
            $this->htmlBodyLogin();
		}

	}

	
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
	protected function htmlBodyProtectedArea() {}
	
	/**
	 * This method implements html content which should always be displayed
	 * in the body area no matter who is logged in and which status is given.
	 */
	protected function htmlBodyUnProtectedArea() {}
	
	protected function htmlBodyMessage() {
		if ($this->prgPattern->hasStatus()) {
			foreach ($this->prgPattern->getRegisteredPrgElements() as $prg) {
                $messages[] = $prg->getStatusMessage();
			}
            $this->smarty->assign('messages', $messages);		
		}
	}
	
	
	protected function htmlBodyLogin() {
        echo "bb";
        $variableNameEmail 			= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
		$variableNameEmailValue		= $this->prgPatternElementLogin->safeGetSessionVariable(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
		$variableNamePassw 			= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_PASSWORD);
		$variableNameAction 		= $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_ACTION);
		$variableNameActionLogin 	= PrgPatternElementLogin::FORM_LOGIN_ACTION_LOGIN;
        
        $this->smarty->assign(array(
            'variableNameEmail'       => $variableNameEmail,
            'variableNamePassw'       => $variableNamePassw,
            'formTO'                  => BrdbHtmlPage::PAGE_INDEX,
            'variableNameAction'      => $variableNameAction,
            'variableNameActionLogin' => $variableNameActionLogin,
        ));
        $this->smarty->display('login.tpl');
        return;
	}
}
?>