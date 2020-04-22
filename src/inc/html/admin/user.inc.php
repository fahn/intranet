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
require_once "/brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgUser.inc.php";

class BrdbHtmlAdminAllUserPage extends BrdbHtmlPage
{
    private PrgPatternElementUser $prgPatternElementUser;
    private array $info;

    const MAX_ENTRIES = 50;


    public function __construct() {
        parent::__construct();

        $this->prgPatternElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementUser);

        $this->info  = array('firstName', 'lastName', 'email', 'gender', 'bday', 'phone', 'playerId', 'clubId');

    }

    protected function showProtectedArea() {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }


    public function htmlBody() {
        $content = "";
        // check if Admin
        if (!$this->prgPatternElementLogin->getLoggedInUser()->isAdmin()) {
            $content = $this->smarty->fetch('no_access.tpl');
            return;
        }

        switch ($this->action) {
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


    private function TMPL_listPlayer(): string
    {
        $this->smarty->assign(array(
            'users'      => $this->getUserList(),
        ));

        return $this->smarty->fetch('admin/UserList.tpl');
    }

    /**
      * PAGINATION
      */
    private function getUserList(): array
    {
        /*
        $this->countRows = count($this->brdb->selectAllUser());
        $max = self::MAX_ENTRIES*(1+$page);
        $min = $max - self::MAX_ENTRIES; */

        $data  = array();

        $userList = $this->brdb->selectAllUser(); #Pagination($min, $max);

        if (isset($userList) && !empty($userList))
        {
            foreach ($userList as $dataSet)
            {
                $user = new User($dataSet);

                $dataSet['isAdmin']    = $user->isAdmin();
                $dataSet['isReporter'] = $user->isReporter();
                $dataSet['isPlayer']   = $user->isPlayer();

                $data[] = $dataSet;
            }
        }
        return $data;
        unset($data, $userList, $dataSet, $user);
    }

    /* private function getPageination(int $active = 0): int
    {
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
    } */

    private function TMPL_addPlayer(): string
    {
         $this->smarty->assign(array(
            'clubs'  => $this->getClubs(),
            'info'   => $this->info,
            'hidden' => "Insert User",
            'task'   => "add",
         ));

        return $this->smarty->fetch('admin/UserUpdate.tpl');
    }


    private function TMPL_editPlayer(): string
    {  
        $this->smarty->assign(array(
            'user'   => $this->getUserById($this->id),
            'clubs'  => $this->getClubs(),
            'info'   => $this->info,
            'hidden' => "Update User",
            'task'   => "edit",
        ));
       
        return $this->smarty->fetch('admin/UserUpdate.tpl');
        unset($id, $player);
    }

    private function TMPL_deletePlayer(): string
    {
        $this->smarty->assign(array(
            'user'   => $this->getUserById($this->id),
            'hidden' => "Delete User",
        ));

        return $this->smarty->fetch('admin/UserDelete.tpl');
    }

    private function getClubs(): array
    {
        return $this->brdb->selectAllClubs();
    }

    private function getUserById(int $id): array
    {
        return $id > 0 ? $this->brdb->selectUserById($id) : array();
    }
}

