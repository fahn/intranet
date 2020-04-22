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
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
require_once dirname(dirname(__FILE__)) ."/config.php";
// DB
require_once BASE_DIR ."/inc/db/brdb.inc.php";

require_once BASE_DIR ."/inc/logic/http.inc.php";

require_once BASE_DIR ."/vendor/autoload.php";

use Nette\Mail\SendmailMailer;
use Nette\Mail\Message;

interface IPrgPatternElement{

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
abstract class APrgPatternElement implements IPrgPatternElement{
    const __TABLE__ = "Unkown";

    const PRG_METHOD_STATUS         = "PostMethodStatus";
    const PRG_METHOD_MESSAGE        = "PostMethodMessage";
    const PRG_METHOD_STATUS_SUCCESS = "success";
    const PRG_METHOD_STATUS_FAILED  = "danger";
    const PRG_METHOD_STATUS_NONE    = "none";

    private string $sessionPrefix;
    private array $variableNameArray;

    // database
    protected BrankDB $brdb;

    /**
     * The constructor to set a prefix for the session variables
     * @param string $sessionPrefix the prefix to be used for this object
     */
    public function __construct(string $sessionPrefix)
    {
        // Pick Up the session and clear the status
        $this->sessionPrefix = $sessionPrefix;
        $this->variableNameArray = array();

        // load db
        $this->brdb = new BrankDB();
    }

  
    public function getSessionPrefix(): string
    {
        return $this->sessionPrefix;
    }

    public function __destruct()
    {
        if ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_GET)        {
            $this->clearStatus();
        }
    }

    /**
     * Use this method to set a post method succes status
     * @param string $message The message to be placed in the session
     */
    protected function setSuccessMessage(string $message): void
    {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] = self::PRG_METHOD_STATUS_SUCCESS;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = $message;
    }

    /**
     * Use this method to set a post method failed status into the session
     * @param string $message the message to be stored with the session
     */
    protected function setFailedMessage(string $message): void
    {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] = self::PRG_METHOD_STATUS_FAILED;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = $message;
    }

    /**
     * Use this method to clear the session status and message of the last called post method.
     * Pay attention to this method. It should usually be called once a page has been rendered to
     * not transmit a status to the next page when using a link.
     */
    protected function clearStatus(): void
    {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] = self::PRG_METHOD_STATUS_NONE;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = "";
    }

    /**
     * Method to check if there is a status in the session
     * @return boolean true in case there is a sucess or fail status
     */
    public function hasStatus(): bool
    {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] != self::PRG_METHOD_STATUS_NONE;
        }
        return false;
    }

    /**
     * Use this method to check for a Success status
     * @return boolean true in case there is a success status
     */
    public function hasStatusSuccess(): bool    {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] == self::PRG_METHOD_STATUS_SUCCESS;
        }
        return false;
    }

    /**
     * Use this method to check for a failed status
     * @return boolean true in case there is a failed status in the session
     */
    public function hasStatusFailed(): bool
    {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)]))        {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] == self::PRG_METHOD_STATUS_FAILED;
        }
        return false;
    }

    /**
     * Call this method to return the status message in the session
     * @return string the message stored in the session
     */
    public function getStatusMessage(): array
    {
        return array(
            "type"    => $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)],
            "message" => $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)],
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
    public function processGet() {}

    public function getPrefixedName(string $variableName): string
    {
        $preparedVariableName = ucfirst(trim($variableName));
        $prefixedVariableName = $this->sessionPrefix . ucfirst(trim($variableName));
        return lcfirst($prefixedVariableName);
    }

    protected function registerPostSessionVariable(string $variableName): void
    {
        array_push($this->variableNameArray, $variableName);
    }

    protected function registerPostSessionVariables(array $varArray): void
    {
        if (isset($varArray) && !empty($varArray))        {
            foreach ($varArray as $item)            {
                try                {
                    $this->registerPostSessionVariable($item);
                }                catch (Exception $e)                {
                    print_r(sprintf("Cannot register POST-Variable: %s, Message: %s ", $item, $e->getMessage()));
                }
            }

        }
    }

    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::copyPostToSession()
     */
    public function copyPostToSession():void
    {
        foreach ($this->variableNameArray as $variableName)        {
            if ($this->issetPostVariable($variableName))            {
                $variablePostValue = $this->getPostVariable($variableName);
                $this->setSessionVariable($variableName, $variablePostValue);
            }
        }
    }

    protected function clearSessionVariables(): void
    {
        foreach ($this->variableNameArray as $variableName)        {
            $this->unsetSessionVariable($variableName);
        }
    }

    public function issetSessionVariable(string $variableName): bool
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return isset($_SESSION[$prefixedVariableName]);
    }

    public function unsetSessionVariable(string $variableName): void    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        unset($_SESSION[$prefixedVariableName]);
    }

    public function getSessionVariable(string $variableName): string
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return $this->escapeInput($_SESSION[$prefixedVariableName]);
    }

    public function setSessionVariable(string $variableName, string $value): void
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        $_SESSION[$prefixedVariableName] = $value;
    }

    public function issetPostVariable(string $variableName): bool
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return isset($_POST[$prefixedVariableName]);
    }

    public function unsetPostVariable(string $variableName): void
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        unset($_POST[$prefixedVariableName]);
    }

    public function getPostVariable(string $variableName): ?string
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        if (isset($_POST) && isset($_POST[$prefixedVariableName]))
        {
            $data = $_POST[$prefixedVariableName];
            if (is_array($data))
            {
                throw new Exception(sprintf("Cannot cast array to string. Fieldname: %s", $variableName));
            }           
           
            return $data = $this->escapeInput($data);
        }

        return null;
    }

    /**
     * recusive method: if input is an array, then     *
     * @param array $arr
     * @param integer $count - min 0 max 5     * @return array
     */
    public function escapeInputArray(array $arr, int $count = 0): array
    {
        $tmp = array();
        if($count > 5) {
            throw new Exception("Array to deep");
        }
               foreach ($arr as $key => $value)        {
            if (is_array($value))
            {
                $value = $this->escapeInputArray($value, ++$count);
            }
            else {
                $value = $this->escapeInput($value);
            }

            $tmp[$this->escapeInput($key)] = $value;
        }
        return $tmp;
           }

    /**
     * return POST-var as String
     *
     * @param string $variableName
     * @return string|null
     */
    public function getPostVariableString(string $variableName): ?string
    {
        return strval($this->escapeInput($this->getPostVariable($variableName)));
    }



    public function getAllPostVariable(): ?array
    {
        $tmpArr = array();
        if (isset($_POST) && is_array($_POST) && count($_POST) > 0)
        {
            foreach ($_POST as $key => $value) {
                $tmpArr = $this->getPostVariable($key);
            }
        }
        return $tmpArr;
        unset($tmpArr);
    }

    public function getPostVariableInt(string $variableName): ?int
    {
        return intval(trim($this->getPostVariable($variableName)));
    }

    public function safeGetSessionVariable(string $variableName, ?string $defaultValue = ""): string
    {
        if ($this->issetSessionVariable($variableName)) {
            return strval($this->getSessionVariable($variableName));
        } else {
            return $defaultValue;
        }
    }

    public function getGetVariable(string $variableName): string    {
        if (isset($_GET) && array_key_exists($variableName, $_GET))        {
            $value = $this->escapeInput($_GET[$variableName]);
        } else {
            $value = "";
        }

        return $value;
    }

    public function issetGetVariable($variableName): bool
    {
        return isset($_GET[$variableName]);
    }

    private function getConstant(string $field): ?string
    {
        try        {
            $oClass = new ReflectionClass (__CLASS__);
            $array = $oClass->getConstants();
            if (isset($array) && is_array($array) && in_array($field, $array))            {
                return $array[$field];
            }
            return null;
        }        catch (Exception $e)        {
            return null;
        }
    }


    /**
     * check if all requiredFields are in POST
     *
     * @param array $requireFields
     * @return boolean
     */
    public function checkRequiredFields(array $requireFields = null): bool
    {
        $requireFields = $requireFields == null ? $this->requiredFields : $requireFields;
        try        {
            if (isset($this->requiredFields) && !empty($this->requiredFields))            {
                foreach ($this->requiredFields as $field)                {
                    if (!$this->issetPostVariable($this->getConstant($field))) {
                        return false;
                    }
                }
            }
            return true;
        }        catch (Exception $e)        {
            return false;
        }
           }

    /**
     * Generate array of all constansts of a class
     *
     * @return array
     */
    public function requiredFields2array(): array
    {
        $arr = array();

        if (isset($this->requiredFields) && !empty($this->requiredFields))        {
            foreach ($this->requiredFields as $field)            {
                $arr[$this->getConstant($field)] = $this->getPostVariable($this->getConstant($field));
            }
        }
        return $arr;
    }


    /**
     * Insert Data to log file
     *
     * @param string $table
     * @param string $details
     * @param object $data
     * @param string $action
     * @param string $userId
     * @return void
     */
    public function log(string $table, string $title, string $details, string $action, int $userId = 0): void
    {
        try {
            $userId = $userId < 0 ?: 0;
            // load mysql
            $this->brdb->insertLog($table, $title, $details, $action, $userId);
        } catch (Exception $e) {
            echo sprintf("Please report this to an admin:<br>Table: %s<br>Title: %s<br>Details: %s<br>Action: %s<br>UserId: %d<br>Error-Message: %s", $table, $title, $details, $action, $userId, $e->getMessage());
            exit(99);
        }
    }

    private function getSetting(string $name): array
    {
        try
        {
            if (!isset($name) || strlen($name) == 0)
            {
                throw new Exception("Cannot get Variable from Content. Not set or empty");
            }

            $data = $this->brdb->getSetting($name);
            if (!isset($data) || !is_array($data)) {
                throw new Exception(sprintf("Cannot find Setting %s", $name));
            }
            return $data;
        }
        catch (Exception $e)        {
            $title=sprintf("Cannot find Setting: %s", $name);
            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
        }
    }

    private function getSettingByDataType(string $name, string $dataType): ?string
    {
        try {
            $data = $this->getSetting($name);
            if (!isset($data["dataType"]) || $data["dataType"] != $dataType)            {
                throw new Exception(sprintf("Except %s. Found ???", $dataType));
            }
                       return $data["value"];
        }
        catch (Exception $e)        {
            $title   = sprintf("Cannot find Setting '%s' datatype '%s'", $name, $dataType);
            $message = $e->getMessage();
            $this->log("Settings", $title, $message, "DB-QUERY");
            return null;
        }    }

    public function getSettingString(string $name): ?string    {
        try {
            return strval($this->getSettingByDataType($name, "string"));
        }
        catch (Exception $e)        {
            $title = sprintf("Cannot cast Setting: '%s'", $name);            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return "";
            unset($title, $e);
        }
    }


    public function getSettingBool(string $name): ?bool    {
        try {
            return boolval($this->getSettingByDataType($name, "bool"));
        }
        catch (Exception $e)        {
            $title = sprintf("Cannot cast Setting: %s", $name);            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
            unset($title, $e);
        }
    }

    /**
     * get int of a setting element
     *
     * @param string $name
     * @return integer|null
     */
    public function getSettingInt(string $name): ?int    {
        try {
            return intval($this->getSettingByDataType($name, "int"));
        }
        catch (Exception $e)        {
            $title = sprintf("Cannot cast Setting: %s", $name);            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
            unset($title, $e);
        }
    }

    /**
     * get array of a settings element
     *
     * @param string $name
     * @return array|null
     */
    public function getSettingArray(string $name): ?array    {
        try {
            return unserialize($this->getSettingByDataType($name, "array"));
        }
        catch (Exception $e)        {
            $title = sprintf("Cannot cast Setting: %s", $name);            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
            unset($title, $e);
        }
    }

    public static function escapeInput(string $data): string
    {
        if (is_array($data))
        {
            return $data;
            $tmp = array();
            foreach($data as $item)
            {
                $tmp[] = $this->escapeInput($item);
            }
            $data = $tmp;

        }
        else
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
        }

        return $data;
    }

    /**
     * Get Method
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        $value = isset($_GET[$key]) ? $_GET[$key] : "";

        return self::escapeInput($value);
    }

    public function customRedirectArray(?array $url): void
    {
        try {
            $this->customRedirectString($this->linkTo($url));
        }        catch (Exception $e)        {
            sprintf("HARD ERROR WITH %s", serialize($url));
            exit(98);
        }
    }

    public function customRedirectString(string $url): void    {
        if (strlen($url) == 0) {
            throw new Exception(sprintf("Url '%s' is empty", $url));
        }
        header_remove();
        header("HTTP/1.1 303 See Other");
        header("Location: ". $url);
        echo "Exit of the PRG pattern...";
        return;
    }

    function secure_array(&$array): void
    {
    // this function secures the content of an array against SQL injection and HTML code injection attacks
    // it works for arrays of any number of dimensions, recursively for each dimension
        if (isset($array)) {
            foreach ($array as $key => $value) {
                if (is_array($array[$key])) {
                    // if element is array, then go to next dimension
                    $this->secure_array($value);
                } else {
                    // if element is a normal variable, clean it up
                    // replace this with mysql / PDO real escape string function depending on which database connector you are using
                    #$array[$key] = $brdb->real_escape_string($array[$key]);
                    $array[$key] = strip_tags($value);
                }
            }
        }
    }

    function cmpSortAgeClass(string $a, string $b): int
    {
        $aC = substr($a,0,1);
        $bC = substr($b,0,1);

        if ($aC = $bC) {
            $aCR = substr($a,1);
            $bCR = substr($b,1);
            if ($aCR == $bCR) {
                return 0;
            }
            return ($aCR < $bCR) ? -1 : 1;
        }

        if ($aC == "U") {
            return -1;
        } else if ($aC == "O") {
            return 1;
        }
    }

    /**
     * Get All Tournament classes
     *
     * @return array
     */
    public function getTournamentAgeClass(): array
    {
        return $this->getSettingArray("TOURNAMENT_CLASSES");
    }


       function formatClassification(string $data): ?string
    {
        $tmpArr = unserialize($data);
        $i = 0;
        if (!is_array($tmpArr) || empty($tmpArr))        {
            return $data;
        }

        $tmpArrC = count($tmpArr);
        if ($tmpArrC > 2)        {
            foreach($tmpArr as $temp)            {
                if (strpos($temp, "U") === 0 || strpos($temp, "O") === 0)                {
                   $i++;
                }
            }
        }

        if ($i == $tmpArrC)        {
            $ageGroup = reset($tmpArr) ."-". end($tmpArr);
        }        else        {
            if (is_array($tmpArr) && !empty($tmpArr))            {
                $ageGroup= implode(", ", $tmpArr);
            }            else            {
                $ageGroup = $tmpArr;
            }
        }

        return $ageGroup;
    }

    function formatDiscipline(string $data): string    {
        return implode(", ", unserialize($data));
    }

    //////////////////////////////////////// ** MODE

    public function getTournamentModeArr(): array
    {
        $modes = $this->getSettingArray("TOURNAMENT_MODES");
        if (is_array($modes))        {
            asort($modes);
        }
               return $modes;
    }

    public function getTournamentType(): array
    {
        $modes = $this->getSettingArray("TOURNAMENT_TYPES");
        if (is_array($modes))        {
            asort($modes);
        }
        return $modes;
    }

    /**
     * Send mail
     *
     * @param [type] $to
     * @param [type] $name
     * @param [type] $subject
     * @param [type] $preheader
     * @param [type] $content
     * @param boolean $assign
     * @param boolean $template
     * @return void
     */
    public function sendMail($to, $name, $subject, $preheader, $content, $assign = false, $template = false) {
        return $this->sendHtmlMail($to, $name, $subject, $preheader, $content, $assign, $template);
    }

    public function isUserGod(int $userId):bool    {
        return in_array($userId, $this->getGod());
    }

    /**
     * Get List of User who are god!
     */
    public function getGod(): ?array    {
        return $this->getSettingArray("GOD_USER");
    }

    public function isProductiv(): bool
    {
        return $this->getSettingString("STAGE") == "productive";
    }

    public function isDeployment(): bool
    {
        return $this->getSettingString("STAGE") == "deployment";
    }

    public function getHomeClub(): int
    {
        return 2; #$this->getSettingString("STAGE");
    }

    /**
     * Send Html-Mail
     *
     * @param [type] $email
     * @param [type] $name
     * @param [type] $subject
     * @param [type] $preheader
     * @param [type] $content
     * @param boolean $assign
     * @param boolean $template
     * @return void
     */
    private function sendHtmlMail($email, $name, $subject, $preheader,  $content, $assign = false, $template = false) {

        // message
        $smarty = new Smarty();
        $smarty->setTemplateDir(  BASE_DIR ."templates");
        $smarty->setCompileDir(  BASE_DIR ."templates_c");
        $smarty->setConfigDir(  BASE_DIR ."configs");
        $smarty->assign(array(
            "content"   => $content,
            "preheader" => $preheader,
        ));

        if (isset($assign)) {
            $smarty->assign($assign);
        }

        if (isset($template) && $template == true && file_exists($smarty->getTemplateDir(0) . $template)) {
            $tmpl = $template;
        } else {
            $tmpl = "htmlmail/mail.tpl"; #"htmlmail/request_password.tpl";
        }

        $mailContent = $smarty->fetch($tmpl);

        $txtContent = strip_tags($content);

        try {
            $mail = new Message();

            $mailFrom = $this->getSettingInt("SENDER_MAIL");
            $mailTo = sprintf("%s <%s>", $name, $email);

            // preparation
            $mail->setFrom($mailFrom)
                ->addTo($mailTo)
                ->setSubject($subject)
                ->setBody($txtContent)
                ->setHtmlBody($mailContent);

            $mailer = new SendmailMailer;

            $mailer->send($mail);
            return true;

        } catch (Exception $e){
            $this->log($this->__TABLE__, sprintf("Cannot send mail: %s ",$e->getMessage()), "", "MAIL");
            return false;
        }
    }

    function getGoogleLatAndLng(string $address): array
    {
        $prepAddr  = str_replace(" ","+",$address);
        $key       = $this->getSettingInt("GOOGLE_MAPS_KEY");
        $geocode   = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=". $key ."&address=". $prepAddr ."&sensor=false");
        $output    = json_decode($geocode);
        $latitude  = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        return array("lat" => $latitude, "lng" => $longitude);
    }

    /**
     * create link to url
     *
     * @param array $data
     * @return string
     */
    function linkTo(array $data): string
    {
        if (isset($data) && is_array($data) && count($data) > 0)        {
            if (!isset($data["page"]) || !file_exists(  BASE_DIR ."/pages/". $data["page"]) )            {
                return "#";
            }

            $urlArr = array();
            foreach ($data as $key => $value)            {
                if ($key == "page")                {
                    continue;
                }
                $urlArr[] = $key ."=". $value;

            }
            $addParams = "";
            if (is_array($urlArr) && count($urlArr) > 0)            {
                $addParams = "?". implode("&", $urlArr);
            }
            $baseUrl = $this->getBaseUrl();

            return $baseUrl ."/pages/". $data["page"] . $addParams;

        }
               return "#";
    }

    /**
     * get Base url
     *
     * @return string
     */
    public function getBaseUrl(): ?string
    {
        return $this->getSettingString("SITE_URL");
    }

    /**
     * return true if maintenance is active
     *
     * @return boolean
     */
    public function isMaintenance(): bool
    {
        return $this->getSettingString("DEVELOPER_IP") ? false : $this->getSettingBool("MAINTENANCE_STATUS");
    }

    /**
     * return ip address from user
     *
     * @return string
     */
    public function getUserIPAdress(): string    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))        {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }        elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))        {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }        else        {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        return $ip;
    }


    public function infoLog(string $message): void
    {
        error_log($message);
    }

    public function getPageination($active = 0, $maxRows) {
        $key = 0;
        $page = array();

        do {
            $page[] = array(
              "status" => ($key == $active ? "active" : ""),
                "id"     => ++$key,
            );
        } while ($maxRows < 0);

        return $page;
    }

    public function dump($var) {
        echo "<pre>";
        var_dump($var);
        exit(1);
    }

    /**
     * generate User Password
     *
     * @param string $password
     * @return string
     */
    public function createPasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * valid password with hash
     *
     * @param string $password
     * @param string $hash
     * @return void
     */
    public function validPassword(string $password, string $hash): bool    {
        return password_verify($password, $hash);
    }

    /**
     * Valid email from user
     *
     * @param string $email
     * @return boolean
     */
    public function validEMail(string $email): bool    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) == true ? true : false;
    }
}

class PrgPattern {

    private array $registeredPrgElements;

    public function __construct()    {
        $this->registeredPrgElements= array();
    }

    public function registerPrg(IPrgPatternElement $prgElement): void
    {
        array_push($this->registeredPrgElements, $prgElement);
    }

    public function hasStatus(): bool
    {
        foreach ($this->registeredPrgElements as $prgElement)        {
            if ($prgElement->hasStatus())            {
                return true;
            }
        }

        return false;
    }

    public function getRegisteredPrgElements(): ?array
    {
        return $this->registeredPrgElements;
    }

    public function processPRG(): void
    {
        // Decide if the current call is a post, then we have to process it
        if ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_POST)        {
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
        } elseif ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_GET)        {
            foreach ($this->registeredPrgElements as $prgElement)            {
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
    protected function processRedirect(): void
    {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$_SERVER[Http::SERVER_REQUEST_URI]);
        exit("Exit of the PRG pattern...");
    }

    protected function customRedirect(string $url): void
    {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$url);
        echo "Exit of the PRG pattern...";
        return;
    }
}

