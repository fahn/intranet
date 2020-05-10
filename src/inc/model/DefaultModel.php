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

use \Badtra\Intranet\Exception\BadtraException;

abstract class DefaultModel
{
    public function __construct($dataSet = null)
    {
        if (isset($dataSet) && is_array($dataSet)) {
            try {
                extract($dataSet);
            } catch (\Exception $e) {
                throw new BadtraException("", sprintf("dataSet isnt valid: %s", serialize($dataSet)));
            }
        }/*
        public function __construct(Array $properties=array()){
            foreach($properties as $key => $value){
              $this->{$key} = $value;
            }
          }*/
    }
   
}

