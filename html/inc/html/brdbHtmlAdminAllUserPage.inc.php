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

include_once BASE_DIR .'/inc/logic/prgUser.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminAllUserPage extends BrdbHtmlPage {
    private $prgPatternElementUser;
    private $countRows;

    private $info;

    const MAX_ENTRIES = 50;


    public function __construct() {
        parent::__construct();

        $this->prgPatternElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementUser);

        $this->info  = array('firstName', 'lastName', 'email', 'gender', 'bday', 'phone', 'playerId', 'clubId');

    }

    protected function showProtectedArea() {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }


    public function htmlBody() {
        $content = "";
        // check if Admin
        if(!$this->prgPatternElementLogin->getLoggedInUser()->isAdmin()) {
            $content = $this->smarty->fetch('no_access.tpl');
            return;
        }

        $action = $this->tools->get('action');
        switch ($action) {
            case 'add_player':
              $content = $this->TMPL_addPlayer();
              break;

            case 'edit':
              $content = $this->TMPL_editPlayer();
              break;

            case 'delete':
              $content = $this->TMPL_deletePlayer();
              break;

            default:
              $content = $this->TMPL_listPlayer();
              break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));


        $this->smarty->display('index.tpl');
    }


    private function TMPL_listPlayer() {
        $page = $this->tools->get("page");
        $page = isset($page) && is_numeric($page) && $page > 0 ? $page-1 : 0;

        $this->smarty->assign(array(
            'users'      => $this->loadUserList($page),
            'error'      => $this->brdb->getError(),
            'pagination' => $this->getPageination($page),
        ));
        return $this->smarty->fetch('admin/UserList.tpl');
    }

    /**
      * PAGINATION
      */
    private function loadUserList($page = 0) {
        $this->countRows = count($this->brdb->selectAllUser());
        $max = self::MAX_ENTRIES*(1+$page);
        $min = $max - self::MAX_ENTRIES;

        $res = $this->brdb->selectAllUserPagination($min, $max);
        $loopUser = array();
        print_r($res);
        foreach ($res as $dataSet) { // = $res->fetch_assoc()) {
            $user = new User($dataSet);

            $dataSet['isAdmin']    = $user->isAdmin();
            $dataSet['isReporter'] = $user->isReporter();
            $dataSet['isPlayer']   = $user->isPlayer();

            $loopUser[] = $dataSet; //new User($dataSet);
        }
        return $loopUser;
    }

    private function getPageination($active = 0) {
        $tmp = $this->countRows;
        $key = 0;
        $page = array();

        do {
            $page[] = array(
                'status' => ($key == $active ? 'active' : ''),
                'id'     => ++$key,
            );
            $tmp -= self::MAX_ENTRIES;
        } while ($tmp > 0);

        return $page;
    }

    private function TMPL_addPlayer() {
         $this->smarty->assign(array(
            'clubs'  => $this->getClubs(),
            'info'   => $this->info,
            'hidden' => "Insert User",
            'task'   => "add",
         ));

        return $this->smarty->fetch('admin/UserUpdate.tpl');
    }


    private function TMPL_editPlayer() {

        $id  = $this->tools->get('id');
        $res = $this->brdb->selectUserById($id);
        if (!$this->brdb->hasError()) {
            $this->info = $res->fetch_assoc();
        }
        $this->smarty->assign(array(
            'clubs'  => $this->getClubs(),
            'info'   => $this->info,
            'hidden' => "Update User",
            'task'   => "edit",
        ));
        return $this->smarty->fetch('admin/UserUpdate.tpl');
    }

    private function TMPL_deletePlayer() {
        $id  = $this->tools->get('id');
        $res = $this->brdb->selectUserById($id);
        if (!$this->brdb->hasError()) {
            $this->user = $res->fetch_assoc();
        }

        $this->smarty->assign(array(
            'user'   => $this->user,
            'hidden' => "Delete User",
        ));

        return $this->smarty->fetch('admin/UserDelete.tpl');
    }

    private function getClubs() {
        return $this->brdb->selectAllClubs();
    }
}
?>
