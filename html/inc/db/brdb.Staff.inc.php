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
declare(strict_types=1);

trait StaffDB 
{
    public function getStaffList() {
        $query = "SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name, User.image, User.gender FROM UserStaff AS US
                    LEFT JOIN User ON User.userId = US.userId
                    ORDER BY US.row ASC, US.sort ASC, User.lastName ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectGetStaffById(int $staffId) {
        $query = "SELECT US.*, CONCAT_WS(' ', User.firstName, User.lastName) AS name FROM UserStaff AS US
                    LEFT JOIN User ON User.userId = US.userId
                    WHERE US.staffId = :staffId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('staffId', $staffId);
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function insertStaff() {
        $query = "INSERT INTO UserStaff (userId, position, description, row, sort) VALUES (99, 1, '', 1, 99)";
        $statement = $this->db->prepare($query);

        return $statement->execute();
    }

    public function updateStaff($data) {
        try {
            #$query = "UPDATE UserStaff set userId = :userId where staffId = :staffid");
            $query = "UPDATE UserStaff set userId = :userId, position  = :position, description  = :desciption, row  = :row where staffId = :staffId";
            $statement = $this->db->prepare($query);
            $statement->bindParam('userId', $data['userId']);
            $statement->bindParam('position', $data['position']);
            $statement->bindParam('desciption', $data['desciption']);
            $statement->bindParam('row', $data['row']);
            $statement->bindParam('staffId', $data['staffId']);

            return $statement->execute();
        } catch (Exception $e) {
            throw new BadtraException('Failed to update Staff');
        }

    }

    public function deleteStaff(int $staffId) {
        try {
            $query = "DELETE FROM UserStaff WHERE staffId = :staffId";
            $statement = $this->db->prepare($query);
            $statement->bindParam('staffId', $staffId);

            return $statement->execute();
        } catch (Exception $e) {
            throw new BadtraException('Failed to update Staff');
        }
    }
}
?>
