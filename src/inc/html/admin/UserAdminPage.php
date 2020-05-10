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
use \Badtra\Intranet\Logic\PrgPatternElementUser;

class UserAdminPage extends BrdbHtmlPage
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

    public function listView(): string
    {
        $this->smarty->assign(array(
            'users'      => $this->getUserList(),
        ));

        return $this->smartyFetchWrap('user/admin/list.tpl');
    }

    public function addView(): string
    {
         $this->smarty->assign(array(
            'clubs'  => $this->getClubs(),
            'info'   => $this->info,
            'hidden' => "Insert User",
            'task'   => "add",
         ));

        return $this->smartyFetchWrap('user/admin/update.tpl');
    }


    public function editView(): string
    {  
        $this->smarty->assign(array(
            'user'   => $this->getUserById($this->id),
            'clubs'  => $this->getClubs(),
            'info'   => $this->info,
            'hidden' => "Update User",
            'task'   => "edit",
        ));
       
        return $this->smartyFetchWrap('user/admin/update.tpl');
        unset($id, $player);
    }

    public function deleteView(): string
    {
        $this->smarty->assign(array(
            'user'   => $this->getUserById($this->id),
            'hidden' => "Delete User",
        ));

        return $this->smartyFetchWrap('user/admin/delete.tpl');
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
                $user = new \Badtra\Intranet\Model\User($dataSet);

                $dataSet['isAdmin']    = $user->isAdmin();
                $dataSet['isReporter'] = $user->isReporter();
                $dataSet['isPlayer']   = $user->isPlayer();

                $data[] = $dataSet;
            }
        }
        return $data;
        unset($data, $userList, $dataSet, $user);
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

