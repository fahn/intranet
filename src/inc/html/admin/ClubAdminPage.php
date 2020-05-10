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
namespace Badtra\Intranet\Html;

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementClub;

class ClubAdminPage extends BrdbHtmlPage
{
    private PrgPatternElementClub $prgPatternElementClub;

    public function __construct()
    {
        parent::__construct();

        $this->prgPatternElementClub = new PrgPatternElementClub($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);
    }

    private function listView(): string
    {
        $this->smarty->assign([
            "clubs"      => $this->loadClubList($this->page),
            //"pagination" => $this->prgPatternElementClub->getPageination($this->page),
        ]);

        return $this->smartyFetchWrap("admin/ClubList.tpl");
    }

    public function addView(): string
    {
      $this->smarty->assign([
          "action"   => $this->action,
          "clubs"    => $this->loadClubList(),
          "variable" => $this->getClubById($this->id),
      ]);
      return $this->smartyFetchWrap("admin/ClubEdit.tpl");
    }

    /**
     * Get all clubs
     *
     * @return array
     */
    public function loadClubList(): array
    {
        #echo $this->countRows = $this->brdb->selectAllClubs()->num_rows;
        #$max = self::MAX_ENTRIES*(1+$page);
        #$min = $max - self::MAX_ENTRIES;

        return $this->brdb->selectAllClubs(); #$min, $max);
    }

    /**
     * Get Club by Id
     *
     * @param integer $id
     * @return array
     */
    private function getClubById(): array
    {
        return $this->brdb->selectGetClubById($this->id);
    }

}

