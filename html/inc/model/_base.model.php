<?php

abstract class baseModel
{
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
    
}

?>