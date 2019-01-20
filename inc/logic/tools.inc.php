<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.    *
 *                                                        *
 * Copyright 2017                                        *
 *                                                        *
 * All Rights Reserved                                    *
 *                                                        *
 * Copying, distribution, usage in any form is not         *
 * allowed without  written permit.                        *
 *                                                        *
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)    *
 *                                                        *
 ********************************************************/

# 
define("__PFAD__", dirname(__FILE__) ."/../");

include_once __PFAD__ .'/smarty/libs/Smarty.class.php';

use Nette\Mail\SendmailMailer;
use Nette\Mail\Message;
/**
 * Anti SQL Injection method. All use rinput should pass this method
 * @param String $data the inupt data as String
 * @return string the returned and filtered string
 */
class Tools {
    /* Private */
    /** Tournament mode **/
    private $ageClassArr = array('U9', 'U11', 'U13', 'U15', 'U17', 'U19', 'U22', 'O19', 'O35', 'O40', 'O45', 'O50', 'O55', 'O60', 'O65', 'O70', 'O75');
    private $modeArr     = array('HE', 'JE', 'DE', 'ME', 'HD', 'JD', 'DD', 'MD', 'GD');


    private $ini;

    public function __construct() {
      $this->ini = $this->getIni();
    }

    public static function escapeInput($data) {
        if(is_array($data)) {
            return $data;
            $tmp = array();
            foreach($data as $key => $item) {
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

        return $this->escapeInput($value);
    }

    public function customRedirect($url) {
        if(isset($url) && is_array($url)) {
          $this->customRedirect($this->linkTo($url));
        } else {
            header("HTTP/1.1 303 See Other");
            header("Location: ". $url);
            exit("Exit of the PRG pattern...");
        }
    }

    public function getIni() {
      if ($this->get("stage") == 'debug') {
        $file = __PFAD__ .'/inc/config_debug.ini';
      } else {
        $file = __PFAD__ .'/inc/config.ini';
      }

      return parse_ini_file($file);
    }

    function secure_array(&$array) {
    // this function secures the content of an array against SQL injection and HTML code injection attacks
    // it works for arrays of any number of dimensions, recursively for each dimension
        if (isset($array)) {
            foreach ($array as $key => $value) {
                if (is_array($array[$key])) {
                    // if element is array, then go to next dimension
                    secure_array($array[$key]);
                } else {
                    // if element is a normal variable, clean it up
                    // replace this with mysql / PDO real escape string function depending on which database connector you are using
                    #$array[$key] = $brdb->real_escape_string($array[$key]);
                    $array[$key] = strip_tags($array[$key]);
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
        //sort($this->ageClassArr);
        usort($this->ageClassArr, array('Tools','cmpSortAgeClass'));
        return $this->ageClassArr;
    }



    function formatClassification($data) {
        $tmpArr = unserialize($data);
        $i = 0;
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
            $ageGroup= implode(", ", $tmpArr);
        }

        return $ageGroup;
    }

    function formatDiscipline($data) {
        return implode(", ", unserialize($data));
    }

    //////////////////////////////////////// ** MODE

    public function getModeArr() {
        sort($this->modeArr);
        return $this->modeArr;
    }

    //
    public function sendMail($to, $name, $subject, $preheader, $content, $assign = false, $template = false) {
      return $this->sendHtmlMail($to, $name, $subject, $preheader, $content, $assign, $template);
    }

    private function sendHtmlMail($email, $name, $subject, $preheader,  $content, $assign = false, $template = false) {

      // message
      $smarty = new Smarty();
      $smarty->setTemplateDir( __PFAD__ .'smarty/templates');
      $smarty->setCompileDir( __PFAD__ .'smarty/templates_c');
      $smarty->setConfigDir( __PFAD__ .'smarty/configs');
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
      $from = sprintf("%s <%s>", $this->ini['senderName'], $this->ini['senderMail']);
      $mail->setFrom($from)
          ->addTo($email)
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
        $geocode   = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='. $this->ini['GoogleMapsKey'] .'&address='. $prepAddr .'&sensor=false');
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
        if(!isset($data['page']) || !file_exists( __PFAD__ .'/pages/'. $data['page']) ) {
          return "#";
        }

        $urlArr = array();
        foreach ($data as $key => $value) {
          if($key != 'page') {
            $urlArr[] = $key .'='. $value;
          }
        }
        $addParams = "";
        if(count($urlArr) > 0) {
            $addParams = "?". implode("&", $urlArr);
        }

        return $this->getBaseUrl() . $data['page'] . $addParams;

      }

      return "#";
    }

    /**
    * return baseUrl from file
    */
    public function getBaseUrl() {
        return $this->ini['baseUrl'];
    }

/*
    public function getSetting($settings, $value) {
        $this->settings = $settings;
        if(isset($this->settings) && isset($value)) {
            return $this->settings($value);
        }

        return false;
    }
    */
}

?>
