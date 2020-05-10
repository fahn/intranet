<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 * * PHP versions 7
 * * @category  BadtraIntranet
 *
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
namespace Badtra\Intranet\Logic;

class PrgPattern
{

    private array $registeredPrgElements;


    public function __construct()
    {
        $this->registeredPrgElements = [];
    }//end __construct()


    public function registerPrg(IPrgPatternElement $prgElement): void
    {
        array_push($this->registeredPrgElements, $prgElement);
    }//end registerPrg()


    public function hasStatus(): bool
    {
        foreach ($this->registeredPrgElements as $prgElement) {
            if ($prgElement->hasStatus()) {
                return true;
            }
        }

        return false;
    }//end hasStatus()


    public function getRegisteredPrgElements(): ?array
    {
        return $this->registeredPrgElements;
    }//end getRegisteredPrgElements()


    public function processPRG(): void
    {
        // Decide if the current call is a post, then we have to process it
        if ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_POST) {
            // now loop over all registered PRGs
            foreach ($this->registeredPrgElements as $prgElement) {
                // first copy all registered variables from post to session
                // this way they can easily be reused for forms in case a form
                // shall be retransmittable. Imagine a user login where the login failed
                // on the second try the user could be still in this input field
                $prgElement->copyPostToSession();
                // now actually call the code for processing the post call
                // once it is processed do the redirection to change the post
                // into a get. thus avoiding retransmissions with the post when stepping
                // back in the browser or reloading the page. The browser would reexecute the post
                // which is not desired.
                $prgElement->processPost();
            }

            // Now after all posts have been processed start doing the redirect
            $this->processRedirect();
        } else if ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_GET) {
            foreach ($this->registeredPrgElements as $prgElement) {
                // And here we do the processing for the get
                // usually nothing happens here
                $prgElement->processGet();
            }
        }//end if
    }//end processPRG()


    /**
     * This method is called internally to redirect the http POST
     * request to an http get request on the same URL
     */
    protected function processRedirect(): void
    {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$_SERVER[Http::SERVER_REQUEST_URI]);
        exit("Exit of the PRG pattern...");
    }//end processRedirect()


    protected function customRedirect(string $url): void
    {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$url);
        echo "Exit of the PRG pattern...";
        return;
    }//end customRedirect()
}//end class
