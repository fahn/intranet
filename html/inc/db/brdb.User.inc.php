<?php
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
        $cmd = $this->db->prepare("SELECT * FROM User WHERE email = ?");
        $cmd->bind_param("s", $email);

        return $this->executeStatement($cmd);
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
    public function selectUserById($userId)
    {
        $cmd = $this->db->prepare("SELECT * FROM User WHERE userId = ?");
        $cmd->bind_param("i", $userId);

        return $this->executeStatement($cmd);
    }

    /**
     * This method deletes a User from the DB with the given userId
     *
     * @param integer $userId
     *            the id of the user to be deleted
     * @return mysqli_result result of the sql execution
     */
    public function deleteUserById($userId) {
        $cmd = $this->db->prepare("Update User set email = '', password = '', reporter = 0, admin = 0 WHERE userId = ?");
        $cmd->bind_param("i", $userId);

        return $this->executeStatement($cmd);
    }

    /**
     * Get all users from the DB
     *
     * @return mysqli_result all users from the database as SQL Result
     */
    public function selectAllUser()
    {
        $cmd = $this->db->prepare("SELECT *, CONCAT_WS(' ', User.firstName, User.lastName) as fullName FROM User");

        return $this->executeStatement($cmd);
    }

    public function selectAllUserSortBy($sort, $asc = 'ASC')
    {
        $cmd = $this->db->prepare("SELECT * FROM User ORDER BY ? ?");
        $cmd->bind_param("ss", $sort, $asc);

        return $this->executeStatement($cmd);
    }

    public function selectAllUserPagination($min = 0, $max = 50)
    {
        $cmd = $this->db->prepare("SELECT User.* FROM User ORDER BY User.lastName LIMIT ?,?");
        $cmd->bind_param("ii", $min, $max);

        return $this->executeStatement($cmd);
    }

    public function GetActiveAndReporterOrAdminPlayer()
    {
        $cmd = $this->db->prepare("SELECT * FROM User WHERE activePlayer = 1 AND (admin = 1 OR reporter = 1) ORDER BY lastName ASC");

        return $this->executeStatement($cmd);
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
        $cmd = $this->db->prepare("INSERT INTO User (email, firstName, lastName, gender, bday, playerId, activePlayer) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $cmd->bind_param("ssssss", $email, $firstName, $lastName, $gender, $bday, $playerId);

        return $this->executeStatement($cmd);
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
    public function updateUser($userId, $email, $fname, $lName, $gender, $phone, $bday) {
        $cmd = $this->db->prepare("Update User set email = ?, firstName = ?, lastName = ?, gender = ?, phone = ?, bday = ? WHERE userId = ?");
        $cmd->bind_param("ssssssi", $email, $fname, $lName, $gender, $phone, $bday, $userId);

        return $this->executeStatement($cmd);
    }

    public function updateUserPassword($userId, $pass) {
        $cmd = $this->db->prepare("UPDATE User set password = ? WHERE userId = ?");
        $cmd->bind_param("si", $pass, $userId);

        return $this->executeStatement($cmd);
    }

    public function checkUserPassword($userId, $hashedPassword) {
        $cmd = $this->db->prepare("SELECT userId FROM User WHERE userId = ? AND password = ? ");
        $cmd->bind_param("is", $userId, $hashedPassword);

        return $this->executeStatement($cmd);
    }

    public function insertUser($email, $firstName, $lastName, $gender){
        $cmd = $this->db->prepare("INSERT INTO User (email, firstName, lastName, gender) VALUES (?, ?, ?, ? )");
        $cmd->bind_param("ssss", $email, $firstName, $lastName, $gender);

        return $this->executeStatement($cmd);
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
    public function updateAdminUser($userId, $email, $fname, $lName, $gender, $phone, $bday, $playerId, $isPlayer, $isReporter, $isAdmin)
    {
        $cmd = $this->db->prepare("UPDATE User set email=?, firstName=?, lastName=?, gender=?, phone=?, bday = ?, playerId = ?, activePlayer = ?, reporter = ?, admin = ? WHERE UserId = ?");
        $cmd->bind_param("sssssssiiii", $email, $fname, $lName, $gender, $phone, $bday, $playerId, $isPlayer, $isReporter, $isAdmin, $userId);

        return $this->executeStatement($cmd);
    }

    public function selectNextBirthdays()
    {
        $cmd = $this->db->prepare("SELECT userId, CONCAT_WS(' ', firstName, lastName) AS userName, bday
                                 FROM User
                                 WHERE DATE_ADD(bday, INTERVAL YEAR(CURDATE())-YEAR(bday) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(bday),1,0) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                                 ORDER BY DAY(bday) ASC");

        return $this->executeStatement($cmd);
    }

    public function selectAllPlayerByOurClub($clubId = 2)
    {
        $cmd = $this->db->prepare("SELECT userId, CONCAT_WS(' ', firstName, lastName) as fullName FROM User WHERE clubId = ? ORDER BY fullName ASC");
        $cmd->bind_param("i", $clubId);

        return $this->executeStatement($cmd);
    }

    // User Password
    public function insertUserPassHash($id, $token, $ip)
    {
        $cmd = $this->db->prepare("INSERT INTO UserPassHash (userId, token, ip) VALUES (?, ?, ?)");
        $cmd->bind_param("iss", $id, $token, $ip);

        return $this->executeStatement($cmd);
    }

    public function GetUserPassHash($mail, $token)
    {
        $cmd = $this->db->prepare("SELECT * FROM User
         LEFT JOIN UserPassHash AS PASS ON PASS.userId = User.userId
         WHERE User.email = ? AND PASS.token = ? AND PASS.valid = 1 AND PASS.createDate > NOW()-86440");
        $cmd->bind_param("ss", $mail, $token);

        return $this->executeStatement($cmd);
    }

    public function DeleteUserPassHash($userid, $token)
    {
        $cmd = $this->db->prepare("UPDATE UserPassHash set valid = 0 WHERE userId = ? AND token = ?");
        $cmd->bind_param("ss", $userid, $token);

        return $this->executeStatement($cmd);
    }

    /**
     * This method hands back a user by a given full name
     *
     * @param String $fullUserName
     *            the user to be handed back by the DB
     * @return mysqli_result
     */
    public function getUserIdByFullName($fullUserName)
    {
        $cmd = $this->db->prepare("SELECT userId FROM User WHERE LOWER(_UserFullName(firstName, lastName)) = LOWER(?)");
        $cmd->bind_param("s", $fullUserName);

        return $this->executeStatement($cmd);
    }

    /**
     * set image to User
     */
    public function updateUserImage($userId, $image) {
        $cmd = $this->db->prepare("UPDATE User set image = ? WHERE userId = ? ");
        $cmd->bind_param("si", $image, $userId);

        return $this->executeStatement($cmd);
    }
}

?>
