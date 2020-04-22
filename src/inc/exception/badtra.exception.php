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
class BadtraException extends Exception
{
    private $title;
    protected $message;

    // Die Exception neu definieren, damit die Mitteilung nicht optional ist
    public function __construct(string $title, string $message)
    {
        // etwas Code
        $this->title = $title;
        $this->title = $message;
        #$this->message = $message != null ? $message : "";
        // sicherstellen, dass alles korrekt zugewiesen wird
        #parent::__construct($message, $code, $previous);
    }

    // maßgeschneiderte Stringdarstellung des Objektes
    public function __toString():string
    {
        return __CLASS__ . ": {$this->message}\n";
    }

    public function exception_handler(Exception $exception):string
    {
        return __CLASS__ . ": {$this->message}\n";
    }
}

