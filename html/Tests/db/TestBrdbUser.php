<?php declare(strict_types=1);

namespace Tests\integration\User;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

define("BASE_DIR", "/var/www/html/");

require_once(BASE_DIR .'/Tests/baseTest.php');
require_once(BASE_DIR .'/inc/brdb/brdb.User.inc.php');

class UserBrdbTest extends BaseTestCase {
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

?>