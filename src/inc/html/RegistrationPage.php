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
use \Badtra\Intranet\Logic\PrgPatternElementRegister;

class RegistrationPage extends BrdbHtmlPage
{

    private PrgPatternElementRegister $prgPatternElementRegister;


    public function __construct()
    {
        parent::__construct();

        $this->prgPatternElementRegister = new PrgPatternElementRegister($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementRegister);
    }//end __construct()

    public function registerView(): string
    {
        $this->smarty->assign(
            []
        );
        return $this->smartyFetchWrap("login/register.tpl");
    }//end loadContent()


}//end class
