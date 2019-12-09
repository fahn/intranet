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
include_once(dirname(dirname(__FILE__)) .'/config.php');

include_once BASE_DIR .'/inc/logic/http.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

interface IPrgPatternElement {

    /**
     * Method to check if there is a status in the session
     * @return boolean true in case there is a sucess or fail status
     */
    public function hasStatus();

    /**
     * Use this method to check for a Success status
     * @return boolean true in case there is a success status
     */
    public function hasStatusSuccess();

    /**
     * Use this method to check for a failed status
     * @return boolean true in case there is a failed status in the session
     */
    public function hasStatusFailed();

    /**
     * Call this method to return the status message in the session
     * @return string the message stored in the session
     */
    public function getStatusMessage();

    /**
     * This method shall be used to handle the post action of the
     * http request to the PRG pattern
     */
    public function processPost();

    /**
     * This method is called to process the get of the http request
     * of this prg pattern. Usually this method is not implemented
     */
    public function processGet();

    /**
     * This method copies all registered variables from the POST systen
     * variable into the SESSION system variable. by this thze values can
     * be reused when processing a failed formular, and previous values
     * can be entered again
     */
    public function copyPostToSession();
}

/**
 * Common methods for handling status messages
 * through http post method handling. This class provides
 * functionality to store errors in from such post processing
 * methods into the current session
 * @author philipp
 *
 */
abstract class APrgPatternElement implements IPrgPatternElement {

    const PRG_METHOD_STATUS         = "PostMethodStatus";
    const PRG_METHOD_MESSAGE        = "PostMethodMessage";
    const PRG_METHOD_STATUS_SUCCESS = "success";
    const PRG_METHOD_STATUS_FAILED  = "danger";
    const PRG_METHOD_STATUS_NONE    = "none";

    private $sessionPrefix;
    private $variableNameArray;

    /**
     * The constructor to set a prefix for the session variables
     * @param string $sessionPrefix the prefix to be used for this object
     */
    public function __construct($sessionPrefix) {
        // Pick Up the session and clear the status
        $this->sessionPrefix = $sessionPrefix;
        $this->variableNameArray = array();
    }

    public function getSessionPrefix() {
        return $this->sessionPrefix;
    }

    public function __destruct() {
        if ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_GET) {
            $this->clearStatus();
        }
    }

    /**
     * Use this method to set a post method succes status
     * @param string $message The message to be placed in the session
     */
    protected function setSuccessMessage($message) {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] = self::PRG_METHOD_STATUS_SUCCESS;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = $message;
    }

    /**
     * Use this method to set a post method failed status into the session
     * @param string $message the message to be stored with the session
     */
    protected function setFailedMessage($message) {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] = self::PRG_METHOD_STATUS_FAILED;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = $message;
    }

    /**
     * Use this method to clear the session status and message of the last called post method.
     * Pay attention to this method. It should usually be called once a page has been rendered to
     * not transmit a status to the next page when using a link.
     */
    protected function clearStatus() {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] = self::PRG_METHOD_STATUS_NONE;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = "";
    }

    /**
     * Method to check if there is a status in the session
     * @return boolean true in case there is a sucess or fail status
     */
    public function hasStatus() {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] != self::PRG_METHOD_STATUS_NONE;
        }
        return false;
    }

    /**
     * Use this method to check for a Success status
     * @return boolean true in case there is a success status
     */
    public function hasStatusSuccess() {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] == self::PRG_METHOD_STATUS_SUCCESS;
        }
        return false;
    }

    /**
     * Use this method to check for a failed status
     * @return boolean true in case there is a failed status in the session
     */
    public function hasStatusFailed() {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] == self::PRG_METHOD_STATUS_FAILED;
        }
        return false;
    }

    /**
     * Call this method to return the status message in the session
     * @return string the message stored in the session
     */
    public function getStatusMessage() {
        return array(
            'type'    => $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)],
            'message' => $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)],
        );
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processPost()
     */
    public abstract function processPost();

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
    }

    public function getPrefixedName($variableName) {
        $preparedVariableName = ucfirst(trim($variableName));
        $prefixedVariableName = $this->sessionPrefix . ucfirst(trim($variableName));
        return lcfirst($prefixedVariableName);
    }

    protected function registerPostSessionVariable($variableName) {
        array_push($this->variableNameArray, $variableName);
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::copyPostToSession()
     */
    public function copyPostToSession() {
        foreach ($this->variableNameArray as $variableName) {
            if ($this->issetPostVariable($variableName)) {
                $variablePostValue = $this->getPostVariable($variableName);
                $this->setSessionVariable($variableName, $variablePostValue);
            }
        }
    }

    protected function clearSessionVariables() {
        foreach ($this->variableNameArray as $variableName) {
            $this->unsetSessionVariable($variableName);
        }
    }

    public function issetSessionVariable($variableName) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return isset($_SESSION[$prefixedVariableName]);
    }

    public function unsetSessionVariable($variableName) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        unset($_SESSION[$prefixedVariableName]);
    }

    public function getSessionVariable($variableName) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return Tools::escapeInput($_SESSION[$prefixedVariableName]);
    }

    public function setSessionVariable($variableName, $value) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        $_SESSION[$prefixedVariableName] = $value;
    }

    public function issetPostVariable($variableName) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return isset($_POST[$prefixedVariableName]);
    }

    public function unsetPostVariable($variableName) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        unset($_POST[$prefixedVariableName]);
    }

    public function getPostVariable($variableName) {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        if(isset($_POST) && isset($_POST[$prefixedVariableName])) {
            $data = $_POST[$prefixedVariableName];
            if (!is_array($data)) {
                $data = strval(trim($data));
            }
            return Tools::escapeInput($data);
        }
        return null;
    }

    public function safeGetSessionVariable($variableName, $defaultValue = "") {
        if ($this->issetSessionVariable($variableName)) {
            return strval($this->getSessionVariable($variableName));
        } else {
            return $defaultValue;
        }
    }

    public function getGetVariable($variableName) {
        if (isset($_GET[$variableName])) {
            return Tools::escapeInput($_GET[$variableName]);
        } else {
            return "";
        }
    }

    public function issetGetVariable($variableName) {
        return isset($_GET[$variableName]);
    }
}

class PrgPattern {

    private $registeredPrgElements;

    public function __construct() {
        $this->registeredPrgElements= array();
    }

    public function registerPrg(IPrgPatternElement $prgElement) {
        array_push($this->registeredPrgElements, $prgElement);
    }

    public function hasStatus() {
        foreach ($this->registeredPrgElements as $prgElement) {
            if ($prgElement->hasStatus()) {
                return true;
            }
        }
        return false;
    }

    public function getRegisteredPrgElements() {
        return $this->registeredPrgElements;
    }

    public function processPRG() {
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
        } elseif ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_GET) {
            foreach ($this->registeredPrgElements as $prgElement) {
                // And here we do the processing for the get
                // usually nothing happens here
                $prgElement->processGet();
            }
        }
    }

    /**
     * This method is called internally to redirect the http POST
     * request to an http get request on the same URL
     */
    protected function processRedirect() {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$_SERVER[Http::SERVER_REQUEST_URI]);
        exit("Exit of the PRG pattern...");
    }

    protected function customRedirect($url) {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$url);
        exit("Exit of the PRG pattern...");
    }
}
?>
