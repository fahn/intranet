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


include_once __PFAD__ .'/inc/logic/tools.inc.php';
include_once __PFAD__ .'/inc/logic/prgTable.inc.php';
include_once __PFAD__ .'/inc/logic/prgStatsTable.inc.php';

/**
 * This class implements  some tools that are needed for the player and
 * team stats tables. Mostly methods which will be needed in both implementations
 * but it is not worth implementing a common super class, since the class hierarchy
 * is already quite complex anyway
 * @author philipp
 *
 */
class BrdbHtmlStatsTool {

	/**
	 * Call this method to print a HTML link <a></a> tag into the current page
	 * the href will be set with the correct settings for sorting the table column
	 * @param unknown $tableColumnName the column name to be displayed in the <a> text area
	 * @param unknown $viewColumnName the column name as use din the sql View used for sorting
	 */
	public static function htmlGetSortLinkFor(APrgStatsTablePattern $prgElementTable, $tableColumnName, $viewColumnName) {
		$phpBaseName = basename($_SERVER[Tools::PHP_SELF]);

		$currentSortedColumn = $prgElementTable->getCurrentSortedColumn();
		$currentSortedOrder = $prgElementTable->getCurrentSortedOrder();

		// In case the to be displayed column is the one that is currently selected for sorting
		// the next link shold inverse the order. Standard is Asecending
		$linkSortOrder = APrgSqlTablePattern::PRG_VARIABLE_VALUE_ORDER_ASC;
		if ($currentSortedColumn == $viewColumnName) {
			if ($currentSortedOrder == APrgSqlTablePattern::PRG_VARIABLE_VALUE_ORDER_ASC) {
				$linkSortOrder = APrgSqlTablePattern::PRG_VARIABLE_VALUE_ORDER_DESC;
			}
		}

		$fullLink = $phpBaseName . "?" . $prgElementTable->buildLinkVariables($viewColumnName, $linkSortOrder);
		?>
		<a href = "<?php echo $fullLink ;?>"><?php echo $tableColumnName ;?></a>
<?php
	}
}
?>
