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
declare(strict_types=1);

trait StaffDB {
    public function selectGetStaff() {
        $cmd = $this->db->prepare("SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name, User.image, User.gender FROM UserStaff AS US
                                 LEFT JOIN User ON User.userId = US.userId
                                 ORDER BY US.row ASC, US.sort ASC, User.lastName ASC");

        return $this->executeStatement($cmd);
    }

    public function selectGetStaffById($staffId) {
        $cmd = $this->db->prepare("SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name FROM UserStaff AS US
                                 LEFT JOIN User ON User.userId = US.userId
                                 WHERE US.staffId = ?");
        $cmd->bind_param("i", $staffId);

        return $this->executeStatement($cmd);
    }

    public function insertStaff() {
        $cmd = $this->db->prepare("INSERT INTO UserStaff (userId, position, description, row, sort) VALUES (99, 1, '', 1, 99)");

        return $this->executeStatement($cmd);
    }

    public function updateStaff($data) {
        try {
            #$cmd = $this->db->prepare("UPDATE UserStaff set userId = :userId where staffId = :staffid");
            $cmd = $this->db->prepare("UPDATE UserStaff set userId = ?, position  = ?, description  = ?, row  = ? where staffId = ?");
            $cmd->bind_param("iisii", $data['userId'], $data['position'], $data['desciption'], $data['row'], $data['staffId']);
            #$cmd->bindParam('userId', $data['userId']);
            #$cmd->bindParam('staffid', $data['staffid']);

            return $this->executeStatement($cmd);
        } catch (Exception $e) {
            throw new BadtraException('Failed to update Staff');
        }

    }

    public function deleteStaff($staffId) {
        try {
            $cmd = $this->db->prepare("DELETE FROM UserStaff WHERE staffId = ?");
            $cmd->bind_param("i", $staffId);

            return $this->executeStatement($cmd);
        } catch (Exception $e) {
            throw new BadtraException('Failed to update Staff');
        }
    }
}
?>
