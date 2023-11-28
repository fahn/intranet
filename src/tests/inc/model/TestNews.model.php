<?php
/*******************************************************************************
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
use PHPUnit\Framework\TestCase;

/**
 * @covers \News
 */
class NewsTest extends TestCase
{
    private array $newsArr;
    private Object $newsObj;

    protected function setUp(): void
    {

        $this->newsArr = array(
            'newsTitle'    => 'Meine Headline',
            'newsCategory' => 1,
            'newsText'     => 'das ist mein 1. Test',
        );

        // News Object
        $this->newsObj = new \News($this->newsArr);
    }

    /**
     * @covers News getNewsTitle
     *
     * @return void
     */
    public function testGetNewsTitle(): void
    {
        $this->assertEquals($this->newsArr['newsTitle'], $this->newsObj->getNewsTitle());
    }

    public function testNewsText(): void
    {
        $this->assertEquals($this->newsArr['newsText'], $this->newsObj->getNewsText());
    }

    public function testNewsCategoryId(): void
    {
        $this->assertEquals($this->newsArr['newsCategory'], $this->newsObj->getNewsCategory());
    }



}

