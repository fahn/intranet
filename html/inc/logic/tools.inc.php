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

include_once BASE_DIR .'/smarty/libs/Smarty.class.php';
include_once BASE_DIR .'/inc/db/brdb.inc.php';
include_once BASE_DIR .'/inc/exception/badtra.exception.php';

# libary
require_once BASE_DIR .'/vendor/autoload.php';

use Nette\Mail\SendmailMailer;
use Nette\Mail\Message;

/**
 * Anti SQL Injection method. All use rinput should pass this method
 * @param String $data the inupt data as String
 * @return string the returned and filtered string
 */
class Tools {
    /* Private */
    private $ageClassArr = array("U9", "U11", "U13", "U15", "U17", "U19", "U22", "O19", "O35", "O40", "O45", "O50", "O55", "O60", "O65", "O70", "O75");
    private $modes = array("HE", "JE", "DE", "ME", "HD", "JD", "DD", "MD", "GD");
    private $ini;

    private $brdb;

    public function __construct() {
        // load ini
        $this->ini = self::getIni();

        // set class for tournmanet
        # = $this->getIniValue("classes"); //["classes"];
        #echo($this->ageClassArr);
        #die();
        #usort($this->ageClassArr, array('Tools','cmpSortAgeClass'));

    }

    public static function escapeInput($data) {
        if(is_array($data)) {
            return $data;
            $tmp = array();
            foreach($data as $item) {
                $tmp[] = $this->escapeInput($item);
            }
            $data = $tmp;

        } else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
        }

        return $data;
    }

    public function get($key) {
        $value = isset($_GET[$key]) ? $_GET[$key] : '';

        return self::escapeInput($value);
    }

    public function customRedirect($url) {
        if(isset($url) && is_array($url)) {
          self::customRedirect(self::linkTo($url));
        } else {
            header_remove();
            header("HTTP/1.1 303 See Other");
            header("Location: ". $url);
            echo "Exit of the PRG pattern...";
            return;
        }
    }

    /**
     * Load config.ini file
     *
     * @return void
     */
    private function getIni() {
        $file =  BASE_DIR .'/inc/config.ini';
        
        return parse_ini_file($file, true);
    }

    /**
     * get value from ini file
     *
     * @param string $val
     * @return void
     */
    public function getIniValue($val = "") {
      $ini = self::getIni();

      if ($val == false || !is_array($ini) || count($ini) < 1) {
        return false;
      }
      if ( ! empty($ini[$val]) ) {
          $returnValues = $ini[$val];
      } else {
          $returnValues = array_column($ini, $val);
      }

      if (! is_array($returnValues) || empty($returnValues)) {
        return false;
      }

      return count($returnValues) > 1 ? $returnValues : $returnValues[0];

    }


    function secure_array(&$array) {
    // this function secures the content of an array against SQL injection and HTML code injection attacks
    // it works for arrays of any number of dimensions, recursively for each dimension
        if (isset($array)) {
            foreach ($array as $key => $value) {
                if (is_array($array[$key])) {
                    // if element is array, then go to next dimension
                    secure_array($value);
                } else {
                    // if element is a normal variable, clean it up
                    // replace this with mysql / PDO real escape string function depending on which database connector you are using
                    #$array[$key] = $brdb->real_escape_string($array[$key]);
                    $array[$key] = strip_tags($value);
                }
            }
        }
    }

    function cmpSortAgeClass($a, $b) {
        $aC = substr($a,0,1);
        $bC = substr($b,0,1);

        if($aC = $bC) {
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


    public function getAgeClassArray() {
        return $this->ageClassArr;
    }



    function formatClassification($data) {
        $tmpArr = unserialize($data);
        $i = 0;
        if (!is_array($tmpArr) || empty($tmpArr)) {
            return $data;
        }

        $tmpArrC = count($tmpArr);
        if ($tmpArrC > 2) {
            foreach($tmpArr as $temp) {
                if (strpos($temp, 'U') === 0 || strpos($temp, 'O') === 0) {
                   $i++;
                }
            }
        }
        if($i == $tmpArrC) {
            $ageGroup = reset($tmpArr) ."-". end($tmpArr);
        } else {
            if (is_array($tmpArr) && !empty($tmpArr)) {
                $ageGroup= implode(", ", $tmpArr);
            } else {
                $ageGroup = $tmpArr;
            }
        }

        return $ageGroup;
    }

    function formatDiscipline($data) {
        return implode(", ", unserialize($data));
    }

    //////////////////////////////////////// ** MODE

    public function getModeArr() {
        if (is_array($this->modes)) {
            asort($this->modes);
        }
        return $this->modes;
    }

    /**
     * send mail
     */
    public function sendMail($to, $name, $subject, $preheader, $content, $assign = false, $template = false) {
        return $this->sendHtmlMail($to, $name, $subject, $preheader, $content, $assign, $template);
    }

    private function sendHtmlMail($email, $name, $subject, $preheader,  $content, $assign = false, $template = false) {

      // message
      $smarty = new Smarty();
      $smarty->setTemplateDir(  BASE_DIR .'templates');
      $smarty->setCompileDir(  BASE_DIR .'templates_c');
      $smarty->setConfigDir(  BASE_DIR .'configs');
      $smarty->assign(array(
          'content'   => $content,
          'preheader' => $preheader,
      ));

      if(isset($assign)) {
        $smarty->assign($assign);
      }

      if(isset($template) && $template == true && file_exists($smarty->getTemplateDir(0) . $template)) {
          $tmpl = $template;
      } else {
          $tmpl = 'htmlmail/mail.tpl'; #'htmlmail/request_password.tpl';
      }

      $mailContent = $smarty->fetch($tmpl);

      $txtContent = strip_tags($content);

      $mail = new Message;

      $from = sprintf("%s %s", $this->getIniValue('senderName'), $this->getIniValue('senderMail'));
      $mailFrom = self::getIniValue('senderMail');

      $mailTo = sprintf("%s <%s>", $name, $email);

      // preparation
      $mail->setFrom($mailFrom)
          ->addTo($mailTo)
          ->setSubject($subject)
          ->setBody($txtContent)
          ->setHtmlBody($mailContent);

      $mailer = new SendmailMailer;

      try {
        $mailer->send($mail);
        return true;
      } catch (Exception $e) {
        return false;
      }

    }

    function getGoogleLatAndLng($address) {
        $prepAddr  = str_replace(' ','+',$address);
        $key       = $this->getIniValue('GoogleMapsKey');
        $geocode   = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='. $key .'&address='. $prepAddr .'&sensor=false');
        $output    = json_decode($geocode);
        $latitude  = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;
        return array('lat' => $latitude, 'lng' => $longitude);
    }

    /**
      Link to subpage
    */
    function linkTo($data) {
      if(isset($data) && is_array($data) && count($data) > 0) {
        if(!isset($data['page']) || !file_exists(  BASE_DIR .'/pages/'. $data['page']) ) {
          return "#";
        }

        $urlArr = array();
        foreach ($data as $key => $value) {
            if($key == 'page') {
                continue;
            }
            $urlArr[] = $key .'='. $value;

        }
        $addParams = "";
        if(is_array($urlArr) && count($urlArr) > 0) {
            $addParams = "?". implode("&", $urlArr);
        }

        return self::getBaseUrl() . $data['page'] . $addParams;

      }
      $this->infoLog(printf("Found no data: %s", var_dump($data)));
      return "#";
    }

    /**
    * return baseUrl from file
    */
    public function getBaseUrl() {
        return self::getIniValue('baseUrl');
    }


    public function isMaintenance() {
      $status = self::getIniValue("Maintenance");

      $devIPAddress = $status["devIP"];

      if ($devIPAddress == $this->getUserIPAdress()) {
          return false;
      }

      if ( ! empty($status)  && $status['maintenance'] == "on") {
        return true;
      }

      return false;
    }

    private function getUserIPAdress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function infoLog($message) {
        return error_log($message);
    }

    public function log($table, $details, $data, $action, $userId = NULL) {
        try {
            // load mysql
            $this->brdb = new BrankDB();
            $this->brdb->insertLog($table, $details, serialize($data), $action, $userId);
        } catch (Exception $e) {
            echo 'Please report this to an admin:<br>';
            echo '<pre>';
            echo 'UserId:'. $userId;
            echo 'Action:'. $action;
            echo 'Table'.   $table;
            echo 'Details'. $details;
            echo 'Data:'. $data;
            echo '</pre>';
            die();
        }
    }

    public function getPageination($active = 0, $maxRows) {
        $key = 0;
        $page = array();

        do {
            $page[] = array(
              'status' => ($key == $active ? 'active' : ''),
                'id'     => ++$key,
            );
        } while ($maxRows < 0);

        return $page;
    }

    public function dump($var) {
        echo "<pre>";
        var_dump($var);
        die();
    }

    public function createPassword() {

    }

    public function createPasswordHash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function validPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function validEMail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    /*
    public function isUserGod() {
        return $this->isUserGod($this->prgPatternElementLogin->getLoggedInUser()->userId);
    }*/

    public function isUserGod($userId) {
        return in_array($userId, $this->getGod());
    }

    /**
     * Get List of User who are god!
     */
    public function getGod() {
        return $this->getIniValue("God");
    }

}

?>
