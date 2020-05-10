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
use \Badtra\Intranet\Logic\PrgPatternElementNotification;

class NotificationPage extends BrdbHtmlPage
{
    protected PrgPatternElementNotification $prgElementNotificattion;

    public function __construct()
    {
        parent::__construct();

        $this->prgElementNotificattion = new PrgPatternElementNotification($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementNotificattion);
    }//end __construct()

    public function listView(): string
    {
        $this->smarty->assign(
            [
                "row" => $this->getNotification(),
            ]
        );

        return $this->smartyFetchWrap("notification/list.tpl");
    }//end TMPL_showList()


    public function getNotification(): array
    {
        $user = $this->prgPatternElementLogin->getLoggedInUser();

        return $user->userId > 0 ? $this->brdb->statementGetNotificationByUserId($user->userId) : [];
    }//end getNotification()
}//end class
