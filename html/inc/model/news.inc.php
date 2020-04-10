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


class News 
{
    
    private int    $newsId;
    private int    $categoryId;
    private string $title;
    private string $text;
    private string $createdBy;
    private string $lastEdited;
    private int    $userId;


    /**
     * get ID
     *
     * @return integer
     */
    public function getNewsId(): int 
    {
        return $this->newsId;
    }

    /**
     * set ID
     *
     * @param integer $id
     * @return void
     */
    public function setNewsId(int $id): void
    {
        if ($id < 1) {
            throw new Exception("id <= 0");
        }

        $this->newsId = $id;
    }

    /**
     * get category ID
     *
     * @return integer
     */
    public function getCategoryId(): int 
    {
        return $this->categoryId;
    }

    /**
     * set category ID
     *
     * @param integer $cid
     * @return void
     */
    public function setCategoryId(int $cid): void
    {
        if ($cid < 1) {
            throw new Exception("id <= 0");
        }

        $this->categoryId = $cid;
    }

    /**
     * get title
     *
     * @return string
     */
    public function getTitle(): string 
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        if (strlen($title) == 0 ) {
            throw new Exception("strlen($title) == 0");
        }

        $this->title = $title;
    }

    /**
     * get text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * set text
     *
     * @param string $createdBy
     * @return void
     */
    public function setText(string $text): void
    {
        if (strlen($text) == 0 ) {
            throw new Exception("strlen($text) == 0");
        }

        $this->text = $text;
    }

    /**
     * get createdBy
     *
     * @return string
     */
    public function getCreatedBy(): string 
    {
        return $this->createdBy;
    }

    /**
     * set createdBy
     *
     * @param string $createdBy
     * @return void
     */
    public function setCcreatedBy(string $createdBy): void
    {
        if (strlen($createdBy) == 0 ) {
            throw new Exception("strlen($createdBy) == 0");
        }

        $this->createdBy = $createdBy;
    }

    /**
     * get lastEdited
     *
     * @return string
     */
    public function getLastEditedy(): string 
    {
        return $this->lastEdited;
    }

    /**
     * set lastEdited
     *
     * @param string $lastEdited
     * @return void
     */
    public function setLastEdited(string $lastEdited): void
    {
        if (strlen($lastEdited) == 0 ) {
            throw new Exception("strlen($lastEdited) == 0");
        }

        $this->lastEdited = $lastEdited;
    }

    public function __toString(): string
    {
        return sprintf("ID: %i\nTitle: %s\nText: %s",
            $this->faqId,
            $this->title,
            $this->text);
    }

    /**
     * get ID
     *
     * @return integer
     */
    public function getUserId(): int 
    {
        return $this->userId;
    }

    /**
     * set ID
     *
     * @param integer $id
     * @return void
     */
    public function setUsersId(int $id): void
    {
        if ($id < 1) {
            throw new Exception("id <= 0");
        }

        $this->userId = $id;
    }

}
?>