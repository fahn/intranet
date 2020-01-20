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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgClub.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminAllClubPage extends BrdbHtmlPage {
  private $prgPatternElementClub;

  const MAX_ENTRIES = 50;

    public function __construct() {
        parent::__construct();

        $this->prgPatternElementClub = new PrgPatternElementClub($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);
    }


    public function htmlBody() {
        $action = $this->tools->get("action");
        $id     = $this->tools->get("id");

        switch ($action) {
          case 'add_club':
            $content = $this->loadContentAddEdit($action, $id);
            break;

          case 'edit':
            $content = $this->loadContentAddEdit($action, $id);
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


    private function loadContent() {
        $page = $this->tools->get("page");
        $page = isset($page) && is_numeric($page) && $page > 0 ? $page-1 : 0;
        $this->smarty->assign(array(
            'clubs'      => $this->loadClubList($page),
            'pagination' => $this->tools->getPageination($page, $this->countRows),
        ));
        return $this->smarty->fetch('admin/ClubList.tpl');
    }

    private function loadContentAddEdit($action, $id) {
      $this->smarty->assign(array(
          'action'   => $action,
          'clubs'    => $this->loadClubList(),
          'variable' => $this->getClubById($id),
      ));
      return $this->smarty->fetch('admin/ClubEdit.tpl');
    }

  public function loadClubList() {
    #echo $this->countRows = $this->brdb->selectAllClubs()->num_rows;
    #$max = self::MAX_ENTRIES*(1+$page);
    #$min = $max - self::MAX_ENTRIES;

    return $this->brdb->selectAllClubs(); #$min, $max);
  }

  /** GET CLUB BY ID
    *
    */
  private function getClubById(int $id) {
      return $id > 0 ? $this->brdb->selectGetClubById($id) : array();
  }

}
?>
