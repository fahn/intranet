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

trait NotificationDB 
{

    /**
     *  Get all notification by User
     * @param unknown $userId
     * @return unknown
     */
    public function statementGetNotificationByUserId(int $userId): array
    {
        $query = "SELECT * FROM `Notification` WHERE userId = ?";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get all non read notification from User
     * @param unknown $userId
     * @return unknown
     */
    public function statementGetNonReadNotificationByUserId(int $userId): bool
    {
        $query = "SELECT * FROM `Notification` WHERE userId = :userId and isRead = 0";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);

        return $statement->execute();
    }

    /**
     * set notification as readed by user
     * @param unknown $notificationId
     * @param unknown $userId
     * @return unknown
     */
    public function statementReadNotificationByUserId(int $notificationId, int $userId): bool
    {
        $query = "UPDATE `Notification` set isRead=1 WHERE notificationId = :notificationId AND userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('notificationId', $notificationId);

        return $statement->execute();
    }


    /**
     * set all messages as readed by user
     * @param unknown $userId
     * @return unknown
     */
    public function statementReadAllNotificationByUserId(int $userId):bool
    {
        $query = "UPDATE `Notification` set isRead=1 WHERE userId = :userId";
        $statement = $this->db->prepare($query);
        $statement->bindParam('userId', $userId);

        return $statement->execute();
    }
}

?>
