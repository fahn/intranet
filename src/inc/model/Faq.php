<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
namespace Badtra\Intranet\Model;

class Faq extends \Badtra\Intranet\Model\BaseModel
{
   
    private int    $faqId;

    private int    $categoryId;

    private string $title;

    private string $text;

    private string $createdBy;

    private string $lastEdited;


    /**
     * get ID
     *
     * @return integer
     */
    public function getFaqId(): int
    {
        return $this->faqId;
    }//end getFaqId()


    /**
     * set ID
     *
     * @param  integer $id
     * @return void
     */
    public function setFaqId(int $id): void
    {
        if ($id < 1) {
            throw new \Exception("id <= 0");
        }

        $this->faqId = $id;
    }//end setFaqId()


    /**
     * get category ID
     *
     * @return integer
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }//end getCategoryId()


    /**
     * set category ID
     *
     * @param  integer $cid
     * @return void
     */
    public function setCategoryId(int $cid): void
    {
        if ($cid < 1) {
            throw new \Exception("id <= 0");
        }

        $this->categoryId = $cid;
    }//end setCategoryId()


    /**
     * get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }//end getTitle()


    /**
     * set title
     *
     * @param  string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        if (strlen($title) == 0) {
            throw new \Exception("strlen($title) == 0");
        }

        $this->title = $title;
    }//end setTitle()


    /**
     * get text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }//end getText()


    /**
     * set text
     *
     * @param  string $createdBy
     * @return void
     */
    public function setText(string $text): void
    {
        if (strlen($text) == 0) {
            throw new \Exception("strlen($text) == 0");
        }

        $this->text = $text;
    }//end setText()


    /**
     * get createdBy
     *
     * @return string
     */
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }//end getCreatedBy()


    /**
     * set createdBy
     *
     * @param  string $createdBy
     * @return void
     */
    public function setCcreatedBy(string $createdBy): void
    {
        if (strlen($createdBy) == 0) {
            throw new \Exception("strlen($createdBy) == 0");
        }

        $this->createdBy = $createdBy;
    }//end setCcreatedBy()


    /**
     * get lastEdited
     *
     * @return string
     */
    public function getLastEditedy(): string
    {
        return $this->lastEdited;
    }//end getLastEditedy()


    /**
     * set lastEdited
     *
     * @param  string $lastEdited
     * @return void
     */
    public function setLastEdited(string $lastEdited): void
    {
        if (strlen($lastEdited) == 0) {
            throw new \Exception("strlen($lastEdited) == 0");
        }

        $this->lastEdited = $lastEdited;
    }//end setLastEdited()


    public function __toString(): string
    {
        return sprintf(
            "ID: %i\nTitle: %s\nText: %s",
            $this->faqId,
            $this->title,
            $this->text
        );
    }//end __toString()
}//end class
