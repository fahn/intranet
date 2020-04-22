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
namespace Tests\integration\User;

use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase {
    private static $id;

    public  function __construcotr() {
       
    }


    public function testCreateUser(){
        $userRepository = new \App\Repository\UserRepository($this->db);
        $redisService = new \App\Service\RedisService(new \Predis\Client());
        $userService = new \App\Service\UserService($userRepository, $redisService);
        $input = ['name' => 'Eze', 'email' => 'eze@gmail.com', 'password' => 'AnyPass1000'];
        $user = $userService->createUser($input);
        self::$id = $user->id;
        $this->assertStringContainsString('Eze', $user->name);
    }
    