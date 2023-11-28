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
use PHPUnit\DbUnit\TestCaseTrait;

class TestPrgUser extends TestCase
{
    private $db;

    /**
     * Test it
     *
     * @return void
     */
    function testGetProjectInformation():void
    {
        // Here, we create a mock prMysql object so we don't use the original
        $prMysql = $this->getMockBuilder(prMysql::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* Here, we say that we expect a call to mysql_query with a given query,
        * and when we do, return a certain result.
        * You will also need to mock other methods as required */
        $expectedQuery = "SELECT product_id, projectName FROM test_projects
                WHERE projectId = 1";
        $returnValue = [['product_id' => 1, 'projectName' => 'test Name']];
        $prMysql->expects($this->once())
            ->method('query')
            ->with($this->equalTo($expectedQuery))
            ->willReturn($returnValue);

        // Here we call the method and do some checks on it
        $object = new ProjectHandler($prMysql);
        $result = $object->getProjectInformation(1);
        $this->assertSame($returnValue, $result);
    }

}
