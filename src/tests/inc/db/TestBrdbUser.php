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
namespace Badtra\Tests\integration\User;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

define("__BASE_DIR__", "/var/www/html/");

require_once __BASE_DIR__ ."/Tests/baseTest.php";
require_once __BASE_DIR__ ."/inc/brdb/brdb.User.inc.php";

class UserBrdbTest extends BaseTestCase
{
    private static $id;
    private $user;

    private $userId = 1;
    private $email = "test@test.de";
    private $firstName = "surTest";
    private $lastName = "lastTest";
    private $gender = "male";
    private $phone = "110";
    private $bday = "1984-04-12";

    public  function __constructor() {

        $this->user = new User();
       
    }

    /**
     * @test
     */
    public function testRegisterUser() {
        $result = $this->user->registerUser($this->email, $this->firstName, $this->lastName, $this->gender, $this->bday, $this->playerId);


        $this->assertTrue(false, $result);
    }


    /**
     * Undocumented function
     *
     * @test
     *
     * @return void
     */
    public function testUpdateUser() {

        $this->user->updateUser($this->userId, $this->email, $this->firstName, $this->lastName, $this->gender, $this->phone, $this->bday);
       
    }


}

