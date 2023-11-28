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
require_once "_base.model.php";

class Category extends \Badtra\Intranet\Model\BaseModel
{
    private int $id;
    private int $pid;
    private string $title;


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new \Exception("id <= 0");
        }
        $this->id = $id;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        if ($pid < 0) {
            throw new \Exception("pid < 0");
        }
        $this->pid = $pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        if (strlen($title) == 0)
        {
            throw new \Exception("Länge des Titles ist gleich 0!");
        }
        $this->title = $title;
    }

    public function __toString(): string
    {
        return sprintf("CATEGORY:\nID: %d\nPID: %d\nTitle: %s",
            $this->id,
            $this->pid,
            $this->title);
    }

}