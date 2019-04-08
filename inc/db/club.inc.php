<?php



class Club {
    private $clubId;
    private $clubName;
    private $clubNumber;


    public function __construct($dataSet = null) {
        if ($dataSet == null) {
            return;
        }

        try {
            extract($row);
            $this->clubId = $clubId;
            $this->clubName = $clubName;
            $this->clubNumber = $clubNumber;
        } Exception ($e) {
            return;
        }

    }

    public function __toString() {
        return sprintf("%s => %s [ID: %i]", $this->clubNumber, $this->clubName, $this->clubId);
    }
