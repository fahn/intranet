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

class BrdbHtmlAdminAllUserPage extends BrdbHtmlPage {
	private $prgPatternElementUser;
	
	public function __construct() {
		parent::__construct();
		$this->prgPatternElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgPatternElementUser);
	}
	
	protected function showProtectedArea() {
		return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
	}
	
	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';
	
	/**
	 * override this method to change the links to other resources such as CSS
	 */
	protected function htmlLink() {
		echo '<link href="../css/style.css" type="text/css" rel="stylesheet" />' . rn;
		echo '<script type="text/javascript" src="../jscript/tablesorter.js"></script>' . rn;
	}
	
	protected function htmlBodyProtectedArea() {
		$variableNameAdminUserId			= $this->prgPatternElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ADMIN_USER_ID);
		$variableNameAction 				= $this->prgPatternElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
		$variableNameActionSelectAccount 	= PrgPatternElementUser::FORM_USER_ACTION_SELECT_ACCOUNT;
		$variableNameActionDeleteAccount 	= PrgPatternElementUser::FORM_USER_ACTION_DELETE_ACCOUNT;
?>
	<div id="formAllUser">
		<h3>User Accounts for Badminton Ranking</h3>
		<p>Select an account for update or delete.</p>
		<hr/>
		<form>
			<table>
				<caption>Table of All Registered Users.</caption>
				<thead>
					<tr>
						<th>Select</th>
						<th>EMail</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Gender</th>
						<th>Player</th>
						<th>Reporter</th>
						<th>Admin</th>
					</tr>
				</thead>
				<tbody>
<?php 
		$res = $this->brdb->selectAllUser();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				$loopUser 		= new User($dataSet);
				$radioId 		= $variableNameAdminUserId . "_" . $loopUser->userId;
				$isLoggedInUser = ($this->prgPatternElementLogin->getLoggedInUser()->userId == $loopUser->userId);
?>
					<tr>
						<td>
							<input 
								type	= "radio" 
								id		= "<?php echo $radioId; ?>"
								name	= "<?php echo $variableNameAdminUserId; ?>" 
								value	= "<?php echo $loopUser->userId;?>"
								<?php echo ($isLoggedInUser ? 'checked = "checked"' : '') . rn; ?>
							/>
							<label class = "radio" for = "<?php echo $radioId; ?>"><?php echo $loopUser->userId; ?></label>
						</td>
						<td><?php echo $loopUser->email; ?></td>
						<td><?php echo $loopUser->firstName; ?></td>
						<td><?php echo $loopUser->lastName; ?></td>
						<td><?php echo $loopUser->gender; ?></td>
						<td><?php echo $loopUser->isPlayer() 	? PrgPatternElementUser::FORM_USER_IS_YES : PrgPatternElementUser::FORM_USER_IS_NO; ?></td>
						<td><?php echo $loopUser->isReporter()	? PrgPatternElementUser::FORM_USER_IS_YES : PrgPatternElementUser::FORM_USER_IS_NO; ?></td>
						<td><?php echo $loopUser->isAdmin()		? PrgPatternElementUser::FORM_USER_IS_YES : PrgPatternElementUser::FORM_USER_IS_NO; ?></td>
					</tr> 
<?php 
			}
		} else {
			echo "<p> Failed to get all User from data base. Reason: " . $brdb->getError() . "</p>";
		}
?>
				</tbody>
			</table>
			<p>
				<input
					type		= "submit"
					name		= "<?php echo $variableNameAction; ?>"
					value		= "<?php echo $variableNameActionDeleteAccount; ?>"
					formaction	= "<?php echo BrdbHtmlPage::PAGE_ADMIN_ALL_USER;?>"
					formmethod	= "post"
				/>
				<input
					type		= "submit"
					name		= "<?php echo $variableNameAction; ?>"
					value		= "<?php echo $variableNameActionSelectAccount; ?>"
					formaction	= "<?php echo BrdbHtmlPage::PAGE_ADMIN_USER;?>"
					formmethod	= "post"
				/>
			</p>
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