<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
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


trait UserDB {

    /**
     * Call this method to hand back the User with the given email from the data base
     *
     * @param String $email
     *            the email to look for as string
     * @return mysqli_result
     */
    public function selectUserByEmail($email)
    {
        $query     = "SELECT * FROM User WHERE email = :email";
        $statement = $this->db->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();

        $user = $statement->fetchObject();
        if (empty($user)) {
            throw new BadtraException('User not found.');
        }

        return $user;
    }


    public function getUserByTerm($term) {
        $term = $this->db->real_escape_string($term);
        $term = "%".$term."%";
        
        $cmd = $this->db->prepare("SELECT * FROM User WHERE lastName LIKE ?");
        $cmd->bind_param("s", $term);

        return $this->executeStatement($cmd);
    }

    /**
     * Get a user from the data base by a given Id
     *
     * @param integer $userId
     *            The user ID as integer
     * @return mysqli_result the user from the database as SQL Result
     */
    public function selectUserById(int $userId) {
        $query = 'SELECT * FROM `User` WHERE userId = :id';
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $userId);
        $statement->execute();

        $user = $statement->fetch();
        #if (empty($user)) {
        #    throw new BadtraException('User not found.');
        #}

        return $user;
    }

    /**
     * This method deletes a User from the DB with the given userId
     *
     * @param integer $userId
     *            the id of the user to be deleted
     * @return mysqli_result result of the sql execution
     */
    public function deleteUserById($userId) {
        $query = "Update User set email = '', password = '', reporter = 0, admin = 0 WHERE userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        
        return $statement->execute();
    }

    /**
     * Get all users from the DB
     *
     * @return mysqli_result all users from the database as SQL Result
     */
    public function selectAllUser(){
        $query = "SELECT *, CONCAT_WS(' ', User.firstName, User.lastName) as fullName FROM User";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAllUserSortBy($sort, $asc = 'ASC') {
        $query = "SELECT * FROM User ORDER BY :sort :order";
        $statement = $this->db->prepare($query);
        $statement->bindParam('sort', $sort);
        $statement->bindParam('order', $order);

        return $statement->execute();
    }

    public function selectAllUserPagination(int $min = 0, int $max = 50) {
        $query = "SELECT User.* FROM User ORDER BY User.lastName LIMIT :min,:max";
        $statement = $this->db->prepare($query);
        $statement->bindParam('min', $min);
        $statement->bindParam('max', $max);
        $statement->execute();

        print_r($statement);
        print_r($statement->debugDumpParams());
        return $statement->fetchAll();
    }

    public function GetActiveAndReporterOrAdminPlayer() {
        $query     = "SELECT * FROM User WHERE activePlayer = 1 AND (admin = 1 OR reporter = 1) ORDER BY lastName ASC";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    /**
     * Call this method to register a new user
     *
     * @param unknown $email
     *            the email of the new user
     * @param unknown $fname
     *            the first name of the new user
     * @param unknown $lname
     *            the last name of the new user
     * @param unknown $pass
     *            the password to be used as sha256 hash
     * @return mysqli_result
     */
    public function registerUser($email, $firstName, $lastName, $gender, $bday, $playerId) {
        $query = "INSERT INTO User (email, firstName, lastName, gender, bday, playerId, activePlayer) 
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
     * Call this method to update a user
     *
     * @param unknown $userId
     *            the id of the user to be updated
     * @param unknown $email
     *            the email to be set
     * @param unknown $fname
     *            the first name to be set
     * @param unknown $lName
     *            the last name to be set
     * @param unknown $phone
     *            the phone to be set
     * @return mysqli_result result of the statement execution
     */
    public function updateUser(int $userId, $email, $firstName, $lastName, $gender, $phone, $bday) {
        $query = "Update User set email = :email, firstName = :firstName, lastName = :lastName, gender = :gender, phone = :phone, bday = :bday WHERE userId = :userId";
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

    public function updateUserPassword(int $userId, String $pass) {
        $query = "UPDATE User set password = :password WHERE userId = :userId";
        $statement = $this->db->prepare($query);

        $statement->bindParam('userId', $userId);
        $statement->bindParam('password', $password);

        return $statement->execute();
    }

    public function checkUserPassword(int $userId, $password) {
        $cmd = $this->db->prepare("SELECT userId FROM User WHERE userId = :userId AND password = :password ");
        $statement = $this->db->prepare($query);

        $statement->bindParam('userId', $userId);
        $statement->bindParam('password', $password);

        return $statement->execute();
    }

    public function insertUser($email, $firstName, $lastName, $gender){
        $query = "INSERT INTO User (email, firstName, lastName, gender) VALUES (:email, :firstName, :lastName, :gender)";
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
        $query = "UPDATE User set email=:email, firstName=:fname, lastName=:lName, gender=:gender, phone=:phone, bday = :bday, 
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

    public function selectNextBirthdays() {
        $query = "SELECT userId, CONCAT_WS(' ', firstName, lastName) AS userName, bday
                    FROM User
                    WHERE DATE_ADD(bday, INTERVAL YEAR(CURDATE())-YEAR(bday) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(bday),1,0) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                    ORDER BY DAY(bday) ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAllPlayerByOurClub($clubId = 2) {
        $query = "SELECT userId, CONCAT_WS(' ', firstName, lastName) as fullName FROM User WHERE clubId = :clubId ORDER BY fullName ASC";
        $statement = $this->db->prepare($query);
        $statement->bindParam('clubId', $clubId);

        return $statement->execute();
    }

    // User Password
    public function insertUserPassHash($userId, $token, $ip) {
        $query = "INSERT INTO UserPassHash (userId, token, ip) VALUES (:userId, :token, :ip)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('token', $token);
        $statement->bindParam('ip', $ip);

        return $statement->execute();
    }

    public function GetUserPassHash($mail, $token) {
        $query = "SELECT * FROM User
                    LEFT JOIN UserPassHash AS PASS ON PASS.userId = User.userId
                    WHERE User.email = :mail AND PASS.token = :token AND PASS.valid = 1 AND PASS.createDate > NOW()-86440";
        $statement = $this->db->prepare($query);
        $statement->bindParam('mail', $mail);
        $statement->bindParam('token', $token);

        return $statement->execute();
    }

    public function DeleteUserPassHash($userid, $token) {
        $query = "UPDATE UserPassHash set valid = 0 WHERE userId = :userId AND token = :token";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('token', $token);

        return $statement->execute();
    }

    /**
     * set image to User
     */
    public function updateUserImage($userId, $image) {
        $query = "UPDATE User set image = :image WHERE userId = :userId ";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('image', $image);

        return $statement->execute();
    }

    public function getUserImages() {
        $query = "SELECT * FROM User where image is not null";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();

    }
}

?>
