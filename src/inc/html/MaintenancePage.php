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

class MaintenancePage extends BrdbHtmlPage
{
    public function __construct()
    {
        parent::__construct();
    }//end __construct()


    public function defaultView()
    {
        $this->smarty->assign(
            [
                "headline" => $this->settings->getSettingString("MAINTENANCE_TITLE"),
                "text"     => $this->settings->getSettingString("MAINTENANCE_TEXT"),
                "date"     => $this->settings->getSettingString("MAINTENANCE_DATE"),
                "link"     => $this->prgPatternElementLogin->linkTo(["page" => "index.php"]),
            ]
        );

        return $this->smartyFetchWrap("maintenance.tpl");
    }//end htmlBody()
    
}//end class
