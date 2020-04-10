<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

require_once(BASE_DIR .'/inc/exception/badtra.exception.php');


trait UserDB 
{

    /**
     * Call this method to hand back the User with the given email from the data base
     *
     * @param String $email
     *            the email to look for as string
     * @return mysqli_result
     */
    public function selectUserByEmail(string $email): ?array
    {
        $query     = "SELECT * FROM `User` WHERE email = :email LIMIT 1";
        $statement = $this->db->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();
        
        return $statement->fetch();
    }


    public function getUserByTerm(string $term): ?array
    {
        // preparing
        $term = '%'. $term .'%';
        
        $query = "SELECT * FROM `User` WHERE lastName LIKE %:term%";
        $statement = $this->db->prepare($query);
        $statement->bindParam('term', $term, PDO::PARAM_STR);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function setUserLastLogin(int $userId): bool
    {
        $query = "UPDATE `USER` set last_login = NOW() WHERE userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);

        return $statement->execute();
        unset($userId, $query, $statement);
    }

    /**
     * Get a user from the data base by a given Id
     *
     * @param integer $userId
     * @return array
     */
    public function selectUserById(int $userId): ?array
    {
        
        $query = "SELECT * FROM `User` WHERE userId = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $userId);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * This method deletes a User from the DB with the given userId
     *
     * @param integer $userId
     *            the id of the user to be deleted
     * @return mysqli_result result of the sql execution
     */
    public function deleteUserById(int $userId): bool
    {
        $query = "UPDATE `User` SET email = '', password = '', reporter = 0, admin = 0 WHERE userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        
        return $statement->execute();
    }

    /**
     * Get all users from the DB
     *
     * @return mysqli_result all users from the database as SQL Result
     */
    public function selectAllUser(): array 
    {
        $query = "SELECT *, CONCAT_WS(' ', User.firstName, User.lastName) as fullName FROM `User`";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAllUserSortBy($sort, $order = 'ASC'): ?array
    {
        $query = "SELECT * FROM `User` ORDER BY :sort :order";
        $statement = $this->db->prepare($query);
        $statement->bindParam('sort', $sort);
        $statement->bindParam('order', $order);
        $statement->execute();

        return $statement->fetchAll();
    }


    public function selectAllUserPagination(int $min = 0, int $max = 50): ?array
    {
        $query = "SELECT * FROM `User` ORDER BY `lastName` LIMIT :min,:max";
        $statement = $this->db->prepare($query);
        $statement->bindParam('min', $min);
        $statement->bindParam('max', $max);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function GetActiveAndReporterOrAdminPlayer(): ?array
    {
        $query     = "SELECT * FROM `User` WHERE `activePlayer` = 1 AND (`admin` = 1 OR `reporter` = 1) ORDER BY `lastName` ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * register a new user
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $gender
     * @param string $bday
     * @param string $playerId
     * @return boolean
     */
    public function registerUser(string $email, string $firstName, string $lastName, string $gender, string $bday, string $playerId): bool
    {
        $query = "INSERT INTO `User` (email, firstName, lastName, gender, bday, playerId, activePlayer) 
                  VALUES (:email, :firstName, :lastName, :gender, :bday, :playerId, 1)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('email', $email);
        $statement->bindParam('firstName', $firstName);
        $statement->bindParam('lastName', $lastName);
        $statement->bindParam('gender', $gender);
        $statement->bindParam('bday', $bday);
        $statement->bindParam('playerId', $playerId);

        return $statement->execute();
    }

    /**
     * Update User
     *
     * @param integer $userId
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $gender
     * @param string $phone
     * @param string $bday
     * @return boolean
     */
    public function updateUser(int $userId, string $email, string $firstName, string $lastName, string $gender, string $phone, string $bday): bool
    {
        $query = "UPDATE `User` SET email = :email, firstName = :firstName, lastName = :lastName, gender = :gender, phone = :phone, bday = :bday WHERE userId = :userId";
        $statement = $this->db->prepare($query);

        $statement->bindParam('email', $email);
        $statement->bindParam('firstName', $firstName);
        $statement->bindParam('lastName', $lastName);
        $statement->bindParam('gender', $gender);
        $statement->bindParam('bday', $bday);
        $statement->bindParam('phone', $phone);
        //$statement->bindParam('playerId', $playerId);
        $statement->bindParam('userId', $userId);


        return $statement->execute();
    }

    /**
     * Update User Password
     *
     * @param integer $userId
     * @param string $password
     * @return boolean
     */
    public function updateUserPassword(int $userId, string $password): bool
    {
        $query = "UPDATE `User` set password = :password WHERE userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('password', $password);

        return $statement->execute();
    }

    /**
     *  Check Password
     *
     * @param integer $userId
     * @param string $password
     * @return array
     */
    public function checkUserPassword(int $userId, string $password): array
    {
        $query     = "SELECT userId FROM `User` WHERE userId = :userId AND password = :password ";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('password', $password);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Insert User
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $gender
     * @return boolean
     */
    public function insertUser(string $email, string $firstName, string $lastName, string $gender): bool
    {
        $query = "INSERT INTO `User` (email, firstName, lastName, gender) VALUES (:email, :firstName, :lastName, :gender)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('email', $email);
        $statement->bindParam('firstName', $firstName);
        $statement->bindParam('lastName', $lastName);
        $statement->bindParam('gender', $gender);

        return $statement->execute();
    }

    /**
     * Call this method to update a user
     *
     * @param unknown $userId
     *            the id of the user to be updated
     * @param unknown $email
     *            the email of the user to be set
     * @param unknown $fname
     *            the first name of the user
     * @param unknown $lName
     *            the last name of the user
     * @param unknown $gender
     *            the gender, set to either "Male" or "Female"
     * @param unknown $pass
     *            password to be set or null in case the password should not be updated
     * @param unknown $isAdmin
     *            set to 1 if the suer is an admin 0 if not
     * @param unknown $isPlayer
     *            set to 1 if the user is an actoive palyer 0 if not
     * @param unknown $isReporter
     *            set to 1 if the user is a reporter or 0 if not
     * @return mysqli_result the result of the executed maysql statement
     */
    public function updateAdminUser($userId, $email, $fname, $lName, $gender, $phone, $bday, $playerId, $isPlayer, $isReporter, $isAdmin) {
        $query = "UPDATE `User` set email=:email, firstName=:fname, lastName=:lName, gender=:gender, phone=:phone, bday = :bday, 
                    playerId = :playerId, activePlayer = :isPlayer, reporter = :isReporter, admin = :isAdmin 
                    WHERE userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('email', $email);
        $statement->bindParam('fname', $fname);
        $statement->bindParam('lName', $lName);
        $statement->bindParam('gender', $gender);
        $statement->bindParam('phone', $phone);
        $statement->bindParam('bday', $bday);
        $statement->bindParam('playerId', $playerId);
        $statement->bindParam('isPlayer', $isPlayer);
        $statement->bindParam('isReporter', $isReporter);
        $statement->bindParam('isAdmin', $isAdmin);
        $statement->bindParam('userId', $userId);

        return $statement->execute();
    }

    /**
     * get upcoming Birthdays
     *
     * @return array
     */
    public function getUpcomingBirthdays(): array
    {
        $query = "SELECT userId, CONCAT_WS(' ', firstName, lastName) AS userName, bday
                    FROM `User`
                    WHERE DATE_ADD(bday, INTERVAL YEAR(CURDATE())-YEAR(bday) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(bday),1,0) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                    ORDER BY DAY(bday) ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get all Player from Hometown club
     *
     * @param integer $clubId
     * @return array
     */
    public function selectAllPlayerByOurClub(int $clubId): array
    {
        $query = "SELECT userId, CONCAT_WS(' ', firstName, lastName) as fullName FROM User WHERE clubId = :clubId ORDER BY fullName ASC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Insert PassHash for new password request
     *
     * @param integer $userId
     * @param String $token
     * @param String $ip
     * @return boolean
     */
    public function insertUserPassHash(int $userId, string $token, string $ip): bool
    {
        $query = "INSERT INTO `UserPassHash` (`userId`, `token`, `ip`) VALUES (:userId, :token, :ip)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('token', $token);
        $statement->bindParam('ip', $ip);

        return $statement->execute();
    }

    /**
     * Get valid User Password hashes
     *
     * @param string $mail
     * @param string $token
     * @return array
     */
    public function GetUserPassHash(string $mail, string $token): array
    {
        $query = "SELECT * FROM User
                    LEFT JOIN UserPassHash AS PASS ON PASS.userId = User.userId
                    WHERE User.email = :mail AND PASS.token = :token AND PASS.valid = 1 AND PASS.createDate > NOW()-86440";
        $statement = $this->db->prepare($query);
        $statement->bindParam('mail', $mail);
        $statement->bindParam('token', $token);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Delete User Hashes
     *
     * @param integer $userId
     * @param string $token
     * @return boolean
     */
    public function DeleteUserPassHash(int $userId, string $token): bool
    {
        $query = "UPDATE `UserPassHash` set valid = 0 WHERE userId = :userId AND token = :token";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('token', $token);

        return $statement->execute();
    }

    /**
     * Update User Picture
     *
     * @param integer $userId
     * @param string $image
     * @return boolean
     */
    public function updateUserImage(int $userId, string $image): bool
    {
        $query = "UPDATE User set image = :image WHERE userId = :userId ";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('image', $image);

        return $statement->execute();
    }

    /**
     * get all User Images
     *
     * @return array
     */
    public function getUserImages(): array
    {
        $query = "SELECT * FROM `User` WHERE `image` IS NOT NULL";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * get User Information with Player details
     *
     * @param integer $playerNr
     * @return array
     */
    public function selectUserByPlayerId(int $playerNr): array
    {
        $query = "SELECT * FROM User LEFT JOIN Player ON User.playerNr = Player.playerNr WHERE playerNr = :playerNr";
        $statement = $this->db->prepare($query);
        $statement->bindParam('playerNr', $playerNr);
        $statement->execute();

        return $statement->fetch();

    }
}

?>
