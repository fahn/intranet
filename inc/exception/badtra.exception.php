<?php
class BadtraException extends Exception {
    private $title;
    protected $message;

    // Die Exception neu definieren, damit die Mitteilung nicht optional ist
    public function __construct($title, $message, $code = 0, Exception $previous = null) {
        // etwas Code
        $this->title = $title;
        $this->message = $message;
        // sicherstellen, dass alles korrekt zugewiesen wird
        parent::__construct($message, $code, $previous);
    }

    // maßgeschneiderte Stringdarstellung des Objektes
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    function exception_handler($exception) {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>
