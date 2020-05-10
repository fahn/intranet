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

Class Notification extends \Badtra\Intranet\Model\BaseModel
{

    public int    $id;

    public int    $userId;

    public string $created;

    public string $text;

    public bool   $isRead;


    public function __construct($dataSet = null)
    {
        if ($dataSet == null) {
            return;
        }

        $this->id      = $dataSet['id'];
        $this->userId  = $dataSet['userId'];
        $this->created = $dataSet['created'];
        $this->text    = $dataSet['text'];
        $this->isRead  = $dataSet['isRead'];
    }//end __construct()


    public function isRead(): bool
    {
        return $this->isRead;
    }//end isRead()
}//end class
