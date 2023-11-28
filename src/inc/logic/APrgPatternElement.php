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

if (defined("BASE_DIR") === false) {
    define("BASE_DIR", $_SERVER["DOCUMENT_ROOT"]);
}

require_once BASE_DIR."/vendor/autoload.php";

use Nette\Mail\SendmailMailer;
use Nette\Mail\Message;

interface IPrgPatternElement
{

    /**
     * Method to check if there is a status in the session
     *
     * @return boolean true in case there is a sucess or fail status
     */
    public function hasStatus();


    /**
     * Use this method to check for a Success status
     *
     * @return boolean true in case there is a success status
     */
    public function hasStatusSuccess();


    /**
     * Use this method to check for a failed status
     *
     * @return boolean true in case there is a failed status in the session
     */
    public function hasStatusFailed();


    /**
     * Call this method to return the status message in the session
     *
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


}//end interface


/**
 * Common methods for handling status messages
 * through http post method handling. This class provides
 * functionality to store errors in from such post processing
 * methods into the current session
 *
 * @author philipp
 */
abstract class APrgPatternElement implements IPrgPatternElement
{
    const __TABLE__ = "Unkown";

    const PRG_METHOD_STATUS         = "PostMethodStatus";
    const PRG_METHOD_MESSAGE        = "PostMethodMessage";
    const PRG_METHOD_STATUS_SUCCESS = "success";
    const PRG_METHOD_STATUS_FAILED  = "danger";
    const PRG_METHOD_STATUS_NONE    = "none";

    private string $sessionPrefix;

    private array $variableNameArray;

    // database
    protected \Badtra\Intranet\DB\BrankDB $brdb;


    /**
     * The constructor to set a prefix for the session variables
     *
     * @param string $sessionPrefix the prefix to be used for this object
     */
    public function __construct(string $sessionPrefix)
    {
        // Pick Up the session and clear the status
        $this->sessionPrefix     = $sessionPrefix;
        $this->variableNameArray = [];

        // load db
        $this->brdb = new \Badtra\Intranet\DB\BrankDB();

    }//end __construct()


    public function getSessionPrefix(): string
    {
        return $this->sessionPrefix;
    }//end getSessionPrefix()


    public function __destruct()
    {
        if ($_SERVER[Http::SERVER_REQUEST_METHOD] === Http::REQUEST_METHOD_GET) {
            $this->clearStatus();
        }
    }//end __destruct()


    /**
     * Use this method to set a post method succes status
     *
     * @param string $message The message to be placed in the session
     */
    protected function setSuccessMessage(string $message): void
    {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)]  = self::PRG_METHOD_STATUS_SUCCESS;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = $message;
    }//end setSuccessMessage()


    /**
     * Use this method to set a post method failed status into the session
     *
     * @param string $message the message to be stored with the session
     */
    protected function setFailedMessage(string $message): void
    {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)]  = self::PRG_METHOD_STATUS_FAILED;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = $message;
    }//end setFailedMessage()


    /**
     * Use this method to clear the session status and message of the last called post method.
     * Pay attention to this method. It should usually be called once a page has been rendered to
     * not transmit a status to the next page when using a link.
     */
    protected function clearStatus(): void
    {
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)]  = self::PRG_METHOD_STATUS_NONE;
        $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)] = "";
    }//end clearStatus()


    /**
     * Method to check if there is a status in the session
     *
     * @return boolean true in case there is a sucess or fail status
     */
    public function hasStatus(): bool
    {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] != self::PRG_METHOD_STATUS_NONE;
        }
        return false;
    }//end hasStatus()


    /**
     * Use this method to check for a Success status
     *
     * @return boolean true in case there is a success status
     */
    public function hasStatusSuccess(): bool
    {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] == self::PRG_METHOD_STATUS_SUCCESS;
        }
        return false;
    }//end hasStatusSuccess()


    /**
     * Use this method to check for a failed status
     *
     * @return boolean true in case there is a failed status in the session
     */
    public function hasStatusFailed(): bool
    {
        if (isset($_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)])) {
            return $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)] == self::PRG_METHOD_STATUS_FAILED;
        }
        return false;
    }//end hasStatusFailed()


    /**
     * Call this method to return the status message in the session
     *
     * @return string the message stored in the session
     */
    public function getStatusMessage(): array
    {
        return [
            "type"    => $_SESSION[$this->getPrefixedName(self::PRG_METHOD_STATUS)],
            "message" => $_SESSION[$this->getPrefixedName(self::PRG_METHOD_MESSAGE)],
        ];
    }//end getStatusMessage()


    /**
     * {@inheritDoc}
     *
     * @see IPrgPatternElement::processPost()
     */
    abstract public function processPost();


    /**
     * {@inheritDoc}
     *
     * @see IPrgPatternElement::processGet()
     */
    public function processGet()
    {

    }//end processGet()


    public function getPrefixedName(string $variableName): string
    {
        $prefixedVariableName = $this->sessionPrefix.ucfirst(trim($variableName));
        return lcfirst($prefixedVariableName);
    }//end getPrefixedName()


    protected function registerPostSessionVariable(string $variableName): void
    {
        array_push($this->variableNameArray, $variableName);
    }//end registerPostSessionVariable()


    protected function registerPostSessionVariables(array $varArray): void
    {
        if (isset($varArray) && !empty($varArray)) {
            foreach ($varArray as $item) {
                try {
                    $this->registerPostSessionVariable($item);
                } catch (\Exception $e) {
                    print_r(sprintf("Cannot register POST-Variable: %s, Message: %s ", $item, $e->getMessage()));
                }
            }
        }
    }//end registerPostSessionVariables()


    /**
     * {@inheritDoc}
     *
     * @see IPrgPatternElement::copyPostToSession()
     */
    public function copyPostToSession():void
    {
        foreach ($this->variableNameArray as $variableName) {
            if ($this->issetPostVariable($variableName)) {
                $variablePostValue = $this->getPostVariable($variableName);
                $this->setSessionVariable($variableName, $variablePostValue);
            }
        }
    }//end copyPostToSession()


    protected function clearSessionVariables(): void
    {
        foreach ($this->variableNameArray as $variableName) {
            $this->unsetSessionVariable($variableName);
        }
    }//end clearSessionVariables()


    public function issetSessionVariable(string $variableName): bool
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return isset($_SESSION[$prefixedVariableName]);
    }//end issetSessionVariable()


    public function unsetSessionVariable(string $variableName): void
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        unset($_SESSION[$prefixedVariableName]);
    }//end unsetSessionVariable()


    public function getSessionVariable(string $variableName): string
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return $this->escapeInput($_SESSION[$prefixedVariableName]);
    }//end getSessionVariable()


    public function setSessionVariable(string $variableName, string $value): void
    {
        $prefixedVariableName            = $this->getPrefixedName($variableName);
        $_SESSION[$prefixedVariableName] = $value;
    }//end setSessionVariable()


    public function issetPostVariable(string $variableName): bool
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        return isset($_POST[$prefixedVariableName]);
    }//end issetPostVariable()


    public function unsetPostVariable(string $variableName): void
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        unset($_POST[$prefixedVariableName]);
    }//end unsetPostVariable()


    public function getPostVariable(string $variableName): ?string
    {
        $prefixedVariableName = $this->getPrefixedName($variableName);
        if (isset($_POST) && isset($_POST[$prefixedVariableName])) {
            $data = $_POST[$prefixedVariableName];
            if (is_array($data)) {
                throw new \Exception(sprintf("Cannot cast array to string. Fieldname: %s", $variableName));
            }
           
            return $data = $this->escapeInput($data);
        }

        return null;
    }//end getPostVariable()


    /**
     * recusive method: if input is an array, then     *
     *
     * @param array   $arr
     * @param integer $count - min 0 max 5     * @return array
     */
    public function escapeInputArray(array $arr, int $count = 0): array
    {
        $tmp = [];
        if ($count > 5) {
            throw new \Exception("Array to deep");
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $value = $this->escapeInputArray($value, ++$count);
            } else {
                $value = $this->escapeInput($value);
            }

            $tmp[$this->escapeInput($key)] = $value;
        }
        return $tmp;

    }//end escapeInputArray()


    /**
     * return POST-var as String
     *
     * @param  string $variableName
     * @return string|null
     */
    public function getPostVariableString(string $variableName): ?string
    {
        return strval($this->escapeInput($this->getPostVariable($variableName)));
    }//end getPostVariableString()


    public function getAllPostVariable(): ?array
    {
        $tmpArr = [];
        if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $tmpArr = $this->getPostVariable($key);
            }
        }
        return $tmpArr;
        unset($tmpArr);
    }//end getAllPostVariable()


    public function getPostVariableInt(string $variableName): ?int
    {
        return intval(trim($this->getPostVariable($variableName)));
    }//end getPostVariableInt()


    public function safeGetSessionVariable(string $variableName, ?string $defaultValue = ""): string
    {
        if ($this->issetSessionVariable($variableName)) {
            return strval($this->getSessionVariable($variableName));
        } else {
            return $defaultValue;
        }
    }//end safeGetSessionVariable()


    public function getGetVariable(string $variableName): string
    {
        if (isset($_GET) && array_key_exists($variableName, $_GET)) {
            $value = $this->escapeInput($_GET[$variableName]);
        } else {
            $value = "";
        }

        return trim($value);
    }//end getGetVariable()

    public function getGetVariableString(string $variableName): string 
    {
        return strval($this->getGetVariable($variableName));
    }

    public function getGetVariableInteger(string $variableName): int 
    {
        return intval($this->getGetVariable($variableName));
    }


    public function issetGetVariable($variableName): bool
    {
        return isset($_GET[$variableName]);
    }//end issetGetVariable()


    private function getConstant(string $field): ?string
    {
        try {
            $oClass = new \ReflectionClass(__CLASS__);
            $array  = $oClass->getConstants();
            if (isset($array) && is_array($array) && in_array($field, $array)) {
                return $array[$field];
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }//end getConstant()


    /**
     * check if all requiredFields are in POST
     *
     * @param  array $requireFields
     * @return boolean
     */
    public function checkRequiredFields(array $requireFields = null): bool
    {
        $requireFields = $requireFields == null ? $this->requiredFields : $requireFields;
        try {
            if (isset($this->requiredFields) && !empty($this->requiredFields)) {
                foreach ($this->requiredFields as $field) {
                    if (!$this->issetPostVariable($this->getConstant($field))) {
                        return false;
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }//end checkRequiredFields()


    /**
     * Generate array of all constansts of a class
     *
     * @return array
     */
    public function requiredFields2array(): array
    {
        $arr = [];

        if (isset($this->requiredFields) && !empty($this->requiredFields)) {
            foreach ($this->requiredFields as $field) {
                $arr[$this->getConstant($field)] = $this->getPostVariable($this->getConstant($field));
            }
        }
        return $arr;
    }//end requiredFields2array()


    /**
     * Insert Data to log file
     *
     * @param  string $table
     * @param  string $details
     * @param  object $data
     * @param  string $action
     * @param  string $userId
     * @return void
     */
    public function log(string $table, string $title, string $details, string $action, int $userId = 0): void
    {
        try {
            $userId = $userId < 0 ?: 0;
            // load mysql
            $this->brdb->insertLog($table, $title, $details, $action, $userId);
        } catch (\Exception $e) {
            echo sprintf("Please report this to an admin:<br>Table: %s<br>Title: %s<br>Details: %s<br>Action: %s<br>UserId: %d<br>Error-Message: %s", $table, $title, $details, $action, $userId, $e->getMessage());
            exit(99);
        }
    }//end log()


    private function getSetting(string $name): array
    {
        try {
            if (!isset($name) || strlen($name) == 0) {
                throw new \Exception("Cannot get Variable from Content. Not set or empty");
            }

            $data = $this->brdb->getSetting($name);
            if (!isset($data) || !is_array($data)) {
                throw new \Exception(sprintf("Cannot find Setting %s", $name));
            }
            return $data;
        } catch (\Exception $e) {
            $title = sprintf("Cannot find Setting: %s", $name);
            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
        }
    }//end getSetting()


    private function getSettingByDataType(string $name, string $dataType): ?string
    {
        try {
            $data = $this->getSetting($name);
            if (!isset($data["dataType"]) || $data["dataType"] != $dataType) {
                throw new \Exception(sprintf("Except %s. Found ???", $dataType));
            }
                       return $data["value"];
        } catch (\Exception $e) {
            $title   = sprintf("Cannot find Setting '%s' datatype '%s'", $name, $dataType);
            $message = $e->getMessage();
            $this->log("Settings", $title, $message, "DB-QUERY");
            return null;
        }
    }//end getSettingByDataType()


    public function getSettingString(string $name): ?string
    {
        try {
            return strval($this->getSettingByDataType($name, "string"));
        } catch (\Exception $e) {
            $title = sprintf("Cannot cast Setting: '%s'", $name);
            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return "";
            unset($title, $e);
        }
    }//end getSettingString()


    public function getSettingBool(string $name): ?bool
    {
        try {
            return boolval($this->getSettingByDataType($name, "bool"));
        } catch (\Exception $e) {
            $title = sprintf("Cannot cast Setting: %s", $name);
            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
            unset($title, $e);
        }
    }//end getSettingBool()


    /**
     * get int of a setting element
     *
     * @param  string $name
     * @return integer|null
     */
    public function getSettingInt(string $name): ?int
    {
        try {
            return intval($this->getSettingByDataType($name, "int"));
        } catch (\Exception $e) {
            $title = sprintf("Cannot cast Setting: %s", $name);
            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
            unset($title, $e);
        }
    }//end getSettingInt()


    /**
     * get array of a settings element
     *
     * @param  string $name
     * @return array|null
     */
    public function getSettingArray(string $name): ?array
    {
        try {
            return unserialize($this->getSettingByDataType($name, "array"));
        } catch (\Exception $e) {
            $title = sprintf("Cannot cast Setting: %s", $name);
            $this->log("Settings", $title, $e->getMessage(), "DB-QUERY");
            return null;
            unset($title, $e);
        }
    }//end getSettingArray()


    /**
     * Escape input for xss.
     *
     * @param  string $value
     * @return string
     */
    public static function escapeInput(string $value): string
    {
        return trim(stripslashes(htmlspecialchars($value)));
    }//end escapeInput()


    public function customRedirectArray(?array $url): void
    {
        try {
            $this->customRedirectString($this->linkTo($url));
        } catch (\Exception $e) {
            sprintf("HARD ERROR WITH %s", serialize($url));
            exit(98);
        }
    }//end customRedirectArray()


    public function customRedirectString(string $url): void
    {
        if (strlen($url) == 0) {
            throw new \Exception(sprintf("Url '%s' is empty", $url));
        }
        header_remove();
        header("HTTP/1.1 303 See Other");
        header("Location: ".$url);
        echo "Exit of the PRG pattern...";
        return;
    }//end customRedirectString()


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
                    // $array[$key] = $brdb->real_escape_string($array[$key]);
                    $array[$key] = strip_tags($value);
                }
            }
        }
    }//end secure_array()


    function cmpSortAgeClass(string $a, string $b): int
    {
        $aC = substr($a, 0, 1);
        $bC = substr($b, 0, 1);

        if ($aC = $bC) {
            $aCR = substr($a, 1);
            $bCR = substr($b, 1);
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
    }//end cmpSortAgeClass()


    /**
     * Get All Tournament classes
     *
     * @return array
     */
    public function getTournamentAgeClass(): array
    {
        return $this->getSettingArray("TOURNAMENT_CLASSES");
    }//end getTournamentAgeClass()


    function formatClassification(string $data): ?string
    {
        $tmpArr = unserialize($data);
        $i      = 0;
        if (!is_array($tmpArr) || empty($tmpArr)) {
            return $data;
        }

        $tmpArrC = count($tmpArr);
        if ($tmpArrC > 2) {
            foreach ($tmpArr as $temp) {
                if (strpos($temp, "U") === 0 || strpos($temp, "O") === 0) {
                    $i++;
                }
            }
        }

        if ($i == $tmpArrC) {
            $ageGroup = reset($tmpArr)."-".end($tmpArr);
        } else {
            if (is_array($tmpArr) && !empty($tmpArr)) {
                $ageGroup = implode(", ", $tmpArr);
            } else {
                $ageGroup = $tmpArr;
            }
        }

        return $ageGroup;

    }//end formatClassification()


    function formatDiscipline(string $data): string
    {
        return implode(", ", unserialize($data));
    }//end formatDiscipline()


    // ** MODE
    public function getTournamentModeArr(): array
    {
        $modes = $this->getSettingArray("TOURNAMENT_MODES");
        if (is_array($modes)) {
            asort($modes);
        }
               return $modes;
    }//end getTournamentModeArr()


    public function getTournamentType(): array
    {
        $modes = $this->getSettingArray("TOURNAMENT_TYPES");
        if (is_array($modes)) {
            asort($modes);
        }
        return $modes;
    }//end getTournamentType()


    /**
     * Send mail
     *
     * @param  [type]  $to
     * @param  [type]  $name
     * @param  [type]  $subject
     * @param  [type]  $preheader
     * @param  [type]  $content
     * @param  boolean $assign
     * @param  boolean $template
     * @return void
     */
    public function sendMail($to, $name, $subject, $preheader, $content, $assign = false, $template = false)
    {
        return $this->sendHtmlMail($to, $name, $subject, $preheader, $content, $assign, $template);
    }//end sendMail()


    public function isUserGod(int $userId):bool
    {
        return in_array($userId, $this->getGod());
    }//end isUserGod()


    /**
     * Get List of User who are god!
     */
    public function getGod(): ?array
    {
        return $this->getSettingArray("GOD_USER");
    }//end getGod()


    public function isProductiv(): bool
    {
        return $this->getSettingString("STAGE") == "productive";
    }//end isProductiv()


    public function isDeployment(): bool
    {
        return $this->getSettingString("STAGE") == "deployment";
    }//end isDeployment()


    public function getHomeClub(): int
    {
        return 2;
        // $this->getSettingString("STAGE");
    }//end getHomeClub()


    /**
     * Send Html-Mail
     *
     * @param  [type]  $email
     * @param  [type]  $name
     * @param  [type]  $subject
     * @param  [type]  $preheader
     * @param  [type]  $content
     * @param  boolean $assign
     * @param  boolean $template
     * @return void
     */
    private function sendHtmlMail($email, $name, $subject, $preheader, $content, $assign = false, $template = false)
    {

        // message
        $smarty = new \Smarty();
        $smarty->setTemplateDir(BASE_DIR."templates");
        $smarty->setCompileDir(BASE_DIR."templates_c");
        $smarty->setConfigDir(BASE_DIR."configs");
        $smarty->assign(
            [
                "content"   => $content,
                "preheader" => $preheader,
            ]
        );

        if (isset($assign)) {
            $smarty->assign($assign);
        }

        if (isset($template) && $template == true && file_exists($smarty->getTemplateDir(0).$template)) {
            $tmpl = $template;
        } else {
            $tmpl = "htmlmail/mail.tpl";
            // "htmlmail/request_password.tpl";
        }

        $mailContent = $smarty->fetch($tmpl);

        $txtContent = strip_tags($content);

        try {
            $mail = new Message();

            $mailFrom = $this->getSettingInt("SENDER_MAIL");
            $mailTo   = sprintf("%s <%s>", $name, $email);

            // preparation
            $mail->setFrom($mailFrom)
                ->addTo($mailTo)
                ->setSubject($subject)
                ->setBody($txtContent)
                ->setHtmlBody($mailContent);

            $mailer = new SendmailMailer;

            $mailer->send($mail);
            return true;
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot send mail: %s ", $e->getMessage()), "", "MAIL");
            return false;
        }//end try
    }//end sendHtmlMail()


    function getGoogleLatAndLng(string $address): array
    {
        $prepAddr  = str_replace(" ", "+", $address);
        $key       = $this->getSettingInt("GOOGLE_MAPS_KEY");
        $geocode   = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=".$key."&address=".$prepAddr."&sensor=false");
        $output    = json_decode($geocode);
        $latitude  = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        return [
            "lat" => $latitude,
            "lng" => $longitude,
        ];
    }//end getGoogleLatAndLng()


    /**
     * create link to url
     *
     * @param  array $data
     * @return string
     */
    function linkTo(array $data): string
    {
        if (isset($data) && is_array($data) && count($data) > 0) {
            if (!isset($data["page"]) || !file_exists(BASE_DIR."/pages/".$data["page"])) {
                return "#";
            }

            $urlArr = [];
            foreach ($data as $key => $value) {
                if ($key == "page") {
                    continue;
                }
                $urlArr[] = $key."=".$value;
            }
            $addParams = "";
            if (is_array($urlArr) && count($urlArr) > 0) {
                $addParams = "?".implode("&", $urlArr);
            }
            $baseUrl = $this->getBaseUrl();

            return $baseUrl."/pages/".$data["page"].$addParams;
        }//end if
               return "#";
    }//end linkTo()


    /**
     * get Base url
     *
     * @return string
     */
    public function getBaseUrl(): ?string
    {
        return $this->getSettingString("SITE_URL");
    }//end getBaseUrl()


    /**
     * return true if maintenance is active
     *
     * @return boolean
     */
    public function isMaintenance(): bool
    {
        return $this->getSettingString("DEVELOPER_IP") ? false : $this->getSettingBool("MAINTENANCE_STATUS");
    }//end isMaintenance()


    /**
     * return ip address from user
     *
     * @return string
     */
    public function getUserIPAdress(): string
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        return $ip;
    }//end getUserIPAdress()


    public function infoLog(string $message): void
    {
        error_log($message);
    }//end infoLog()


    public function getPageination($active = 0, $maxRows)
    {
        $key  = 0;
        $page = [];

        do {
            $page[] = [
                "status" => ($key == $active ? "active" : ""),
                "id"     => ++$key,
            ];
        } while ($maxRows < 0);

        return $page;
    }//end getPageination()


    public function dump($var)
    {
        echo "<pre>";
        var_dump($var);
        exit(1);
    }//end dump()


    /**
     * generate User Password
     *
     * @param  string $password
     * @return string
     */
    public function createPasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }//end createPasswordHash()


    /**
     * valid password with hash
     *
     * @param  string $password
     * @param  string $hash
     * @return void
     */
    public function validPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }//end validPassword()


    /**
     * Valid email from user
     *
     * @param  string $email
     * @return boolean
     */
    public function validEMail(string $email): bool
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) == true ? true : false;
    }//end validEMail()
}//end class