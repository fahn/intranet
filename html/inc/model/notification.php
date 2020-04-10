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

Class Notification {
    public int    $id;
    public int    $userId;
    public string $created;
    public string $text;
    public bool   $isRead;

    public function __construct($dataSet = null) 
    {
        if ($dataSet == null) 
        {
            break;
        }

        $this->id      = $dataSet['id'];
        $this->userId  = $dataSet['userId'];
        $this->created = $dataSet['created'];
        $this->text    = $dataSet['text'];
        $this->isRead  = $dataSet['isRead'];
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }
}

?>
