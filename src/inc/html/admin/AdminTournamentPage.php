<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
namespace Badtra\Intranet\Html\Admin;

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementTournament;

class AdminTournamentPage extends BrdbHtmlPage
{

    private PrgPatternElementTournament $prgPatternElementTournament;

    public function __construct() {
        parent::__construct();

        $this->prgPatternElementTournament = new PrgPatternElementTournament($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementTournament);
    }


    public function listView():string
    {
        $tournaments = $this->brdb->selectTournamentList();

        $this->smarty->assign(array(
            "tournaments" => $tournaments,
        ));

        return $this->smartyFetchWrap("tournament/admin/list.tpl");
    }

    public function addView():string
    {
        $this->smarty->assign(array(
            "task" => "add",
        ));

        return $this->smartyFetchWrap("tournament/admin/add.tpl");
    }

    public function updateView(int $id):string
    {
        $tournament = $this->brdb->getTournamentData($id);

        $this->smarty->assign(array(
            "tournament" => $tournament,
            "task" => "update",
        ));

        return $this->smartyFetchWrap("tournament/admin/update.tpl");
    }

    public function deleteView(int $id):string
    {
        $tournament = $this->brdb->getTournamentData($id);

        $this->smarty->assign(array(
            "tournament" => $tournament,
        ));

        return $this->smartyFetchWrap("tournament/admin/delete.tpl");
    }

    public function lockView(int $id):string
    {
        $tournament = $this->brdb->getTournamentData($id);

        $this->smarty->assign(array(
            "tournament" => $tournament,
        ));

        return $this->smartyFetchWrap("tournament/admin/lock.tpl");
    }

    public function unlockView(int $id):string
    {
        $tournament = $this->brdb->getTournamentData($id);

        $this->smarty->assign(array(
            "tournament" => $tournament,
        ));

        return $this->smartyFetchWrap("tournament/admin/unlock.tpl");
    }

    public function exportView(int $id):string
    {
        $tournament = $this->brdb->getTournamentData($id);

        $this->smarty->assign(array(
            "tournament" => $tournament,
        ));

        return $this->smartyFetchWrap("tournament/admin/export.tpl");
    }


    /**
     * Backup Tournament Data
     *
     * @return string
     */
    public function backupView(int $id):string
    {
      $diff    = "";
      $backup = $this->brdb->getTournamentBackup($id);

        $this->smarty->assign(array(
            "backup" => $backup,
            "diff"   => $diff,
        ));

        if (isset($this->id) && count($backup) > 1) {
            $first  = $backup[0]["backupId"];
            $second = $backup[1]["backupId"];
            $rows = $this->brdb->getTournamentBackupDiff($first, $second);
            if (isset($backup) && is_array($backup)) {
                $rows = array();
                foreach ($backup as $row) {
                    $rows[] = unserialize($row["data"]);
                }

                $result = $this->arrayRecursiveDiff($rows[0], $rows[1]);
               
                $this->smarty->assign(array(
                "diffResult"   => $result,
                "diff"         => $rows,
                ));

                #$diff = Diff::toTable(Diff::compare($rows[0], $rows[1]));
            }
        }

        return $this->smartyFetchWrap("tournament/admin/backup.tpl");
    }


    private function arrayRecursiveDiff(array $aArray1, array $aArray2): array
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
          if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
              $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
              if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
            } else {
              if ($mValue != $aArray2[$mKey]) {
                $aReturn[$mKey] = $mValue;
              }
            }
          } else {
            $aReturn[$mKey] = $mValue;
          }
        }
        return $aReturn;

        /*
            use SebastianBergmann\Diff\Differ;
            use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;

            $builder = new DiffOnlyOutputBuilder(
                "--- Original\n+++ New\n"
            );

            $differ = new Differ($builder);
            print $differ->diff("foo", "bar");
                 */
    }


}