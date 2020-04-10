<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/
include_once('brdbHtmlPage.inc.php');
include_once BASE_DIR .'/inc/logic/prgClub.inc.php';

class BrdbHtmlAdminAllClubPage extends BrdbHtmlPage 
{
    private PrgPatternElementClub $prgPatternElementClub;

    //private int MAX_ENTRIES = 50;

    public function __construct()
    {
        parent::__construct();

        $this->prgPatternElementClub = new PrgPatternElementClub($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);
    }


    public function htmlBody(): void
    {

        switch ($this->action) 
        {
            case 'add_club':
              $content = $this->loadContentAddEdit();
              break;

            case 'edit':
                $content = $this->loadContentAddEdit();
                break;

            default:
                $content = $this->loadContent();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent(): string 
    {
        $this->smarty->assign(array(
            'clubs'      => $this->loadClubList($this->page),
            'pagination' => $this->prgPatternElementClub->getPageination($this->page, $this->countRows),
        ));

        return $this->smarty->fetch('admin/ClubList.tpl');
    }

    private function loadContentAddEdit(): string
    {
      $this->smarty->assign(array(
          'action'   => $this->action,
          'clubs'    => $this->loadClubList(),
          'variable' => $this->getClubById($this->id),
      ));
      return $this->smarty->fetch('admin/ClubEdit.tpl');
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
?>
