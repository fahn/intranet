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
declare(strict_types=1);

require_once ("../exception/badtra.exception.php");

class News 
{
    private int $newsId = 0;
    private string $newsTitle;
    private int $newsCategory = 0;
    private string $newsText;

    public function __construct($dataSet = null): void
    {
        if (isset($dataSet) && is_array($dataSet)) {
            try {
                extract($dataSet);
            } catch (Exception $e) {
                throw new BadtraException(sprintf("dataSet isnt valid: %s", serialize($dataSet)));
            }
        }/*
        public function __construct(Array $properties=array()){
            foreach($properties as $key => $value){
              $this->{$key} = $value;
            }
          }*/
    }

    public function getNewsId(): int
    {
        return $this->newsId;
    }

    public function  getNewsTitle(): string 
    {
        return $this->newsTitle;
    }

    public function getNewsCategory(): int 
    {
        return $this->newsCategory;
    }

    public function getNewsText(): string 
    {
        return $this->newsText;
    }

    public function __toString(): string 
    {
        return sprintf("ID: %d, Title: %s, Category: %d, Text: %s", $this->newsId, $this->newsTitle, $this->newsCategory, $this->newsText);
    }
}