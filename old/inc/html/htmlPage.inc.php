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

include_once '../inc/logic/tools.inc.php';

/**
 * This class helps to build up HTML pages
 * using object oriented php classes. It
 * is based on HTML 5 and provides a standard
 * layout to show a html page. It also links to a
 * CSS style sheet and provides all relevant information
 * such as the header, meta data (UTF-8), title, etc.
 * 
 * @author philipp
 *
 */
abstract class HtmlPageProcessor {
	
	/**
	 * Standard constructor which gets called
	 * by some derived classes
	 */
	public function __construct() {
	}
	
	/**
	 * Call this method to process / render the complete HTML page
	 */
	public function processPage() {
?>
<!-- 
/********************************************************
 * This file belongs to the Badminton Ranking Project.  *
 *                                                      *
 * Copyright 2017                                       *
 *                                                      *
 * All Rights Reserved                                  *
 *                                                      *
 * Copying, distribution, usage in any form is not      *
 * allowed without written permit. Exemption to this    *
 * rule is given in the frame of using this file in the *
 * intended way of interacting with Badminton Ranking   *
 * as a regular user.                                   *
 *                                                      *
 * Philipp M. Fischer (fiphi@gmx.de)                    *
 *                                                      *
 ********************************************************/
 -->
<?php 
		$this->docType();
		echo '<html lang="de">' . rn;
		$this->htmlHead();
		$this->htmlBody();
		echo '</html> ' . rn;
	}

	/**
	 * Override this method to change the html doc type
	 */
	protected function docType() {
		echo '<!DOCTYPE html>' . rn;
	}
	
	/**
	 * Override this method to change the content of the html head
	 */
	protected function htmlHead() {
		$this->htmlLink();
		$this->htmlMeta();
		$this->htmlTitle();
	}

	/**
	 * Override this method to change the html title
	 */
	protected function htmlTitle() {
		echo '<title>Page Title</title>' . rn;
	}
	
	/**
	 * override this method to change the links to other resources such as CSS
	 */
	protected function htmlLink() {
		echo '<link href="../css/style.css" type="text/css" rel="stylesheet" />' . rn;
	}

	/**
	 * Override this method to change meta information of the html
	 */
	protected function htmlMeta() {
		echo '<meta charset="UTF-8">' . rn;
	}
	
	/**
	 * Override this method to change the body content of the html.
	 * In most derived classes this method is changed to display the specific
	 * content of the html.
	 */
	protected function htmlBody() {
		echo '<body>' . rn;
		echo '</body>' . rn;
	}
}
?>