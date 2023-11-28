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

require_once BASE_DIR ."/vendor/autoload.php";

class ParseMDPage extends BrdbHtmlPage
{

    protected $markDownFile;
    protected \Parsedown $parsedown;

    public function __construct()
    {
        parent::__construct();
        $this->parsedown = new \Parsedown();
    }


    /*            VIEWS            */

    public function aboutView(): string
    {
        $markDownFile = "doc/about.md";

        return $this->renderMD($markDownFile);
    }

    public function changelogView(): string
    {
        $markDownFile = "CHANGELOG.md";

        return $this->renderMD($markDownFile);
    }

    public function imprintView(): string
    {
        $markDownFile = BASE_DIR."/doc/Impressum.md";

        return $this->renderMD($markDownFile);
    }

    private function renderMD(string $filename): string
    {
        if (is_file($this->markDownFile)) {
            $mdfile = $this->parsedown->text(file_get_contents($this->filename));
        } else {
            $mdfile = "No content found.";
        }

        $this->smarty->assign(
            'content', $this->renderMD($mdfile)
        );

        return $this->smartyFetchWrap("markdown.tpl");
    }


}

