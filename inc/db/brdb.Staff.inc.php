<?php

trait StaffDB {
    public function selectStaffList() {
        $cmd = $this->db->prepare("SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name, User.image, User.gender FROM UserStaff AS US
                                 LEFT JOIN User ON User.userId = US.userId
                                 ORDER BY US.row ASC, US.sort ASC, User.lastName ASC");

        return $this->executeStatement($cmd);
    }
}
?>
