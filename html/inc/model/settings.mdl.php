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


class Setting 
{

    private int $id;
    private string $dataType;
    private string $name;
    private string $value;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id) 
    {
        if ($id <= 0) {
            throw new Exception("id <= 0");
        }

        $this->id = $id;
    }

    public function getDataType(): string
    {
        return $this->dataType;
    }

    public function setDataType(string $dataType): void
    {
        $dataTypeArr = array('id', 'dataType', 'name', 'value');
        if (in_array($dataType, $dataTypeArr)) {
            throw new Exception("dataType not in range {%s}", implode(", ", $dataTypeArr));
        }

        $this->dataType = $dataType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void 
    {
        if (strlen($name) == 0) {
            throw new Exception("Länge des Names == 0");
        }

        $this->name = $name;
    }

    /**
     * get value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->getValue();
    }

    /**
     * set value
     *
     * @param string $value
     * @return void
     */
    public function setValue(string $value): void 
    {
        $this->value = $value;
    }

    public function __toString(): string 
    {
        return sprintf("SETTING\nID: %d\nDataType: %s\nName: %s\nWert: %s\n",
        $this->id,
        $this->dataType,
        $this->name,
        $this->value);
    }

}
?>