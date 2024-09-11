<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2024
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
 * @copyright 2017-2024 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
namespace Badtra\Intranet\Html;

use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Logic\PrgPatternElementUser;

class News extends BrdbHtmlPage
{
    protected PrgPatternElementUser $prgElementUser;

    public function __construct()
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }
 
    public function listView(): string
    {
        $this->smarty->assign("news", $this->brdb->statementGetAllNews());
        
        return $this->smarty->fetch("news/list.tpl");
    }

    public function detailView($id): string
    {
        $this->smarty->assign("news", $this->brdb->statementGetNewsById($id)[0]);
        
        return $this->smarty->fetch("news/detail.tpl");
    }

}