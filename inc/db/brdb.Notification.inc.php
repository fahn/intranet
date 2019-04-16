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

trait Noticiation
{
    /**
     *  Get all notification by User
     * @param unknown $userId
     * @return unknown
     */
    public function statementGetNotificationByUserId($userId)
    {
        $cmd = $this->db->prepare("SELECT * FROM `Notification` WHERE userId = ?");
        $cmd->bind_param("i", $userId);

        return $this->executeStatement($cmd);
    }

    /** 
     * Get all non read notification from User
     * @param unknown $userId
     * @return unknown
     */
    public function statementGetNonReadNotificationByUserId($userId)
    {
        $cmd = $this->db->prepare("SELECT * FROM `Notification` WHERE userId = ? and isRead = 0");
        $cmd->bind_param("i", $userId);

        return $this->executeStatement($cmd);
    }

    /**
     * set notification as readed by user
     * @param unknown $notificationId
     * @param unknown $userId
     * @return unknown
     */
    public function statementReadNotificationByUserId($notificationId, $userId)
    {
        $cmd = $this->db->prepare("UPDATE `Notification` set isRead=1 WHERE notificationId = ? AND userId = ?");
        $cmd->bind_param("ii", $notificationId, $userId);

        return $this->executeStatement($cmd);
    }

   
    /**
     * set all messages as readed by user
     * @param unknown $userId
     * @return unknown
     */
    public function statementReadAllNotificationByUserId($userId)
    {
        $cmd = $this->db->prepare("UPDATE `Notification` set isRead=1 WHERE userId = ?");
        $cmd->bind_param("i", $userId);

        return $this->executeStatement($cmd);
    }
}

?>