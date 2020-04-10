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

#namespace Exception\Badtra;

class BadtraException extends Exception {
    private $title;
    protected $message;

    // Die Exception neu definieren, damit die Mitteilung nicht optional ist
    public function __construct($message) {
        // etwas Code
        $this->title = $message;
        #$this->message = $message != null ? $message : "";
        // sicherstellen, dass alles korrekt zugewiesen wird
        #parent::__construct($message, $code, $previous);
    }

    // maßgeschneiderte Stringdarstellung des Objektes
    public function __toString() {
        return __CLASS__ . ": {$this->message}\n";
    }

    function exception_handler($exception) {
        return __CLASS__ . ": {$this->message}\n";
    }
}
?>
