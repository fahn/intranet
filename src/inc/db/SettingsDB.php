<?php
/**
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
namespace Badtra\Intranet\DB;
trait SettingsDB
{

    protected String $__TABLE__ = "Settings";

    public function loadAllSettings(): array
    {
        $query     = "SELECT * FROM `Settings` ORDER BY name ASC";
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();

    }//end loadAllSettings()


    public function getSetting(string $name): array
    {
        $query     = "SELECT * FROM `Settings` WHERE `name` = :name";
        $statement = $this->db->prepare($query);
        $statement->bindParam('name', $name);

        $statement->execute();

        return $statement->fetch();
    }//end getSetting()


    public function getSettingById(int $id): array
    {
        $query     = "SELECT * FROM `Settings` WHERE `id` = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->execute();

        return $statement->fetch();
    }//end getSettingById()


    public function insertSettings(\Badtra\Intranet\Model\Setting $setting):bool
    {
        $query     = "INSERT INTO `Settings` (`name`, `dataType`, `value`) VALUES (:name, :dataType, :value)";
        $statement = $this->db->prepare($query);
        $statement->bindParam('name', $setting->getName());
        $statement->bindParam('dataType', $setting->getDataType());
        $statement->bindParam('value', $setting->getValue());

        return $statement->execute();
    }//end insertSettings()


    public function updateSettings(\Badtra\Intranet\Model\Setting $setting):bool
    {
        $query     = "UPDATE `Settings` SET `name` = :name, `dataType` = :dataType, `value` = :value WHERE id = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('name', $setting->getName());
        $statement->bindParam('dataType', $setting->getDataType());
        $statement->bindParam('value', $setting->getValue());
        $statement->bindParam('id', $setting->getId());

        return $statement->execute();
    }//end updateSettings()


    public function deleteSettings(int $id): bool
    {
        $query     = "DELETE * FROM `Settings` WHERE id = :id";
        $statement = $this->db->prepare($query);
        $statement->bindParam('id', $id);

        return $statement->execute();
    }//end deleteSettings()
}
