<?php declare(strict_types=1);

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
    