<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlStatsTools.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgTable.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgStatsTable.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

/**
 * A General Page for displaying Player / Single Game Results
 * @author philipp
 *
 */
abstract class ABrdbHtmlStatsPlayerPage extends BrdbHtmlPage {

    private $prgPatternElementStatsTable;
    private $tableTitle;

    public function __construct($tableTitle, $tableName) {
        parent::__construct();
        $this->prgPatternElementStatsTable= new PrgPlayerStatsTablePattern($tableName, $this->brdb);
        $this->prgPattern->registerPrg($this->prgPatternElementStatsTable);
        $this->tableTitle = $tableTitle;
    }

  public function processPage() {
        // Call all prgs and process them all
        $this->prgPattern->processPRG();
        parent::processPage();
    }

    protected function explainTable() {}

    protected function htmlBody() {
        $dataSet = array();
      while ($data = $this->prgPatternElementStatsTable->fetchResultViewRow()) {
      if($data['games'] == 0) {
        continue;
      }

        $dataSet[] = $data;
      }

        $this->smarty->assign(array(
            'tableTitle' => $this->tableTitle,
            'players'    => $dataSet,
            'explain'    => $this->explainTable(),
        ));

    $this->content = $this->smarty->fetch("ranking/StatsSingle.tpl");
    $this->smarty->assign('content', $this->content);

        $this->smarty->display('index.tpl');
    }
}

/**
 * Class to display the results of Single Player Alltime Scores
 * @author philipp
 *
 */
class BrdbHtmlStatsPlayerAlltimePage extends ABrdbHtmlStatsPlayerPage {
    public function __construct() {
        parent::__construct("Player Ranking Alltime", "UserStatsPlayerAlltimePos");
    }

    protected function explainTable() {
        return "This is the Alltime Player Ranking. All games ever played account into this table. No matter if it is a Double Men, a Single Women or playing with three persons or a Single Women again Single Men";
    }
}

/**
 * Class to display the results of Single Player Overall Scores
 * from games men vs men, girl vs. girl, men vs girl.
 * @author philipp
 *
 */
 class BrdbHtmlStatsPlayerOverallPage extends ABrdbHtmlStatsPlayerPage {
    public function __construct() {
        parent::__construct("Player Ranking Overall", "UserStatsPlayerOverallPos");
    }

    protected function explainTable() {
        return "This is the Overall Player Ranking. All Single games played account into this table. Single Men or Women or Men against Women games.";
    }
}

/**
 * Class to display the results of Single Player Discipline games - Single Men only
 * @author philipp
 *
 */
class BrdbHtmlStatsPlayerDisciplineSingleMenPage extends ABrdbHtmlStatsPlayerPage {
    public function __construct() {
        parent::__construct("Player Ranking Discipline - Single Men", "UserStatsPlayerDisciplineSingleMenPos");
    }

    protected function explainTable() {
        return "This is the Discipline Player Ranking for Single Men games. Only Single Men games acount into this table.";
    }
}

/**
 * Class to display the results of Single Player Discipline games - Single Women only
 * @author philipp
 *
 */
 class BrdbHtmlStatsPlayerDisciplineSingleWomenPage extends ABrdbHtmlStatsPlayerPage {
    public function __construct() {
        parent::__construct("Player Ranking Discipline - Single Women", "UserStatsPlayerDisciplineSingleWomenPos");
    }

    protected function explainTable() {
        return "This is the Discipline Player Ranking for Single Women games. Only Single Women games acount into this table.";
    }
}
?>
