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

include_once '../inc/html/htmlLoginPage.inc.php';
include_once '../inc/logic/prgGame.inc.php';


class BrdbHtmlPage extends AHtmlLoginPage {
	
	const PAGE_INDEX 				= "index.php";
	const PAGE_MY_REGISTRATION 		= "myRegistration.php";
	const PAGE_MY_ACCOUNT 			= "myAccount.php";
	const PAGE_ADMIN_ALL_USER 		= "adminAllUser.php";
	const PAGE_ADMIN_USER 			= "adminUser.php";
	const PAGE_REPORT_INSERT_GAME 	= "reportInsertGame.php";
	const PAGE_REPORT_UPDATE_GAME 	= "reportUpdateGame.php";
	const PAGE_REPORT_ALL_GAME 		= "reportAllGame.php";
	const PAGE_STATS_PLAYER_ALLTIME = "statsPlayerAlltime.php";
	const PAGE_STATS_PLAYER_OVERALL = "statsPlayerOverall.php";
	const PAGE_STATS_PLAYER_MEN 	= "statsPlayerMen.php";
	const PAGE_STATS_PLAYER_WOMEN 	= "statsPlayerWomen.php";
	const PAGE_STATS_TEAM_OVERALL 	= "statsTeamOverall.php";
	const PAGE_STATS_TEAM_MEN 		= "statsTeamMen.php";
	const PAGE_STATS_TEAM_WOMEN 	= "statsTeamWomen.php";
	const PAGE_STATS_TEAM_MIXED 	= "statsTeamMixed.php";
	
	const PAGE_INFO_MANUAL 		= "infoManual.php";
	const PAGE_INFO_LICENSE 	= "infoLicense.php";
	const PAGE_INFO_TPL 		= "infoThirdPartyLicenses.php";
	const PAGE_INFO_IMPRESSUM  	= "infoImpressum.php";
	
	public function __construct() {
		parent::__construct();
	}
    
    public function processPage() {
		// Call all prgs and process them all
		$this->prgPattern->processPRG();
        
		parent::processPage();
        
        
	}

	
	protected function htmlBodyNav1() {
        return;
?>
	<nav>
		<ul>
			<li>
				<a href = "<?php echo self::PAGE_INDEX;?>">Home</a>
			</li>
			<li>
				<a>My</a>
				<ul>
					<li><a href = "<?php echo self::PAGE_MY_REGISTRATION;?>">Registration</a></li>
					<li><a href = "<?php echo self::PAGE_MY_ACCOUNT;?>">Account</a></li>
					<li><a href = "<?php echo self::PAGE_ADMIN_ALL_USER;?>">Users</a></li>
				</ul>
			</li>
			<li>
				<a>Rankings</a>
				<ul>
					<li><a href = "<?php echo self::PAGE_STATS_PLAYER_ALLTIME;?>">Alltime</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_PLAYER_OVERALL;?>">Single Overall</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_PLAYER_MEN;?>">Single Men</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_PLAYER_WOMEN;?>">Single Women</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_TEAM_OVERALL;?>">Double Overall</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_TEAM_MEN;?>">Double Men</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_TEAM_WOMEN;?>">Double Women</a></li>
					<li><a href = "<?php echo self::PAGE_STATS_TEAM_MIXED;?>">Double Mixed</a></li>
				</ul>
			</li>
			<li>
				<a>Report</a>
				<ul>
					<li><a href = "<?php echo self::PAGE_REPORT_INSERT_GAME . "?" . PrgPatternElementGame::FORM_GAME_ACTION . "=" . PrgPatternElementGame::FORM_GAME_ACTION_NEW_GAME;?>">Enter Game</a></li>
					<li><a href = "<?php echo self::PAGE_REPORT_ALL_GAME;?>">List Games</a></li>
				</ul>
			</li>
			<li id="rightFloat">
				<a>Info</a>
				<ul>
					<li><a href = "<?php echo self::PAGE_INFO_MANUAL;?>">Manual</a></li>
					<li><a href = "<?php echo self::PAGE_INFO_LICENSE;?>">License</a></li>
					<li><a href = "<?php echo self::PAGE_INFO_TPL;?>">TPLicenses</a></li>
					<li><a href = "<?php echo self::PAGE_INFO_IMPRESSUM;?>">Impressum</a></li>
				</ul>
			</li>
		</ul>
	</nav>
<?php 
	}
    
    
}
