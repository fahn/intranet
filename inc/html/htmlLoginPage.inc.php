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

include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/html/htmlPage.inc.php';

// Logic
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgLogin.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgUser.inc.php';

// load widgets
require_once $_SERVER['BASE_DIR'] .'/inc/widget/ranking.widget.php';
require_once $_SERVER['BASE_DIR'] .'/inc/widget/tournament.widget.php';
require_once $_SERVER['BASE_DIR'] .'/inc/widget/team.widget.php';
require_once $_SERVER['BASE_DIR'] .'/inc/widget/bday.widget.php';


// notification
#include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlNotification.inc.php';



/**
 * Implements an html page with login capabilities.
 * It also provides a protected content are which is only
 * displayed on sucessful login. the class also handles the login
 * and logout dialog. All pages that use this class and derive from it
 * will have support for the login functionality
 *
 * @author philipp
 *
 */
abstract class AHtmlLoginPage extends HtmlPageProcessor {
    protected $brdb;
    protected $prgPattern;
    protected $prgPatternElementLogin;
    protected $tools;

    /**
     * Standard Constructor for the HTML Login page. It
     * takes care of creating the connection to the data base
     * and stores the DB connection object into the member variable
     * $brdb. it is protected and therefore accessible by all derived
     * classes (pages)
     */
    public function __construct() {
        parent::__construct();


        /* TOOLS */
        $this->tools = new Tools();



        if ( $this->tools->maintenance()) {
          $this->tools->customRedirect('maintenance.php');
        }



        /* SQL CONNECTION */
        $this->brdb = new BrankDB();

        $this->prgPatternElementLogin = new PrgPatternElementLogin($this->brdb);

        $this->prgPattern = new PrgPattern();
        $this->prgPattern->registerPrg($this->prgPatternElementLogin);


        /* LOAD SETTINGS */
        //$this->settings = $this->loadSettings();

        /* goto Login */
        $basename = basename($_SERVER['PHP_SELF']);

        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
        #die(print_r($this->prgPatternElementLogin->getLoggedInUser()));
        #die(print_r($this->prgPatternElementLogin->isUserLoggedIn()));
        if($basename != "index.php" && $isUserLoggedIn === false) {
            $_SESSION['ref'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $this->tools->customRedirect(array(
              'page' => 'index.php',
            ));
            die();
        }
    }

    private function loadSettings() {
        $data = array();
        $res = $this->brdb->loadSettings();
        while($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }



    public function processPage() {
        $this->getMessages();

        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
        if($isUserLoggedIn AND $this->smarty) {
            $currentUserName = $this->prgPatternElementLogin->getLoggedInUser()->getFullName();

            $user = $this->prgPatternElementLogin->getLoggedInUser();
            $this->smarty->registerObject('user', $user);

            // Notification
            #$notification = new Notification();


            $this->smarty->assign(array(
                    'currentUserName'    => $currentUserName,
                    'isUserLoggedIn'     => $isUserLoggedIn,
                    'isAdmin'            => $this->prgPatternElementLogin->getLoggedInUser()->isAdmin(),
                    'isReporter'         => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
                    'userId'             => $this->prgPatternElementLogin->getLoggedInUser()->getId(),
                    'rankingEnable'      => $this->tools->getIniValue('RankingEnabled'),
                    'tournamentEnable'   => $this->tools->getIniValue('tournamentEnable'),
                    'faqEnabled'         => $this->tools->getIniValue('faqEnabled'),
                    'social'             => $this->tools->getIniValue('Social'),
                    'notificationEnable' => $this->tools->getIniValue('notificationEnable'),
                    'newsEnable'         => $this->tools->getIniValue('newsEnable'),
                    #$notification->getNotification(),
            ));
        }

        // Call all prgs and process them all
        $this->prgPattern->processPRG();
        parent::processPage();
    }



    /**
     * This method hands back the currently logged in user
     * @return User the currently logged in user or null in
     * noone is logged in
     */
    public function getLoggedInUser() {
        return $this->prgPatternElementLogin->getLoggedInUser();
    }

    /**
     * This method handles the display of the content
     * depending on the current user being logged in.
     * The method also provides rendering of the protected
     * content. This content is onyl displayed if a user is
     * successfully logged in and other additional criteria
     * are met.
     */
    protected function htmlBody() {
        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
        $this->getMessages();

        if($isUserLoggedIn) {
            $this->smarty->assign(array(
                    'content'      => $this->loadContent(),
                    #'notification' => $this->getNotification(),
            ));
            $this->smarty->display('index.tpl');
        } else {
            $action = $this->tools->get("action");
            $request = isset($action) ? $action : '';

            switch ($request) {
              case 'request_password':
                $this->content = $this->smarty->fetch('login/request_password.tpl');
                break;

              case 'change_password':
                  $token = $this->tools->get("token");
                  $mail  = $this->tools->get("mail");
                  $this->smarty->assign(array(
                      'token' => $token,
                      'mail'  => $mail,
                  ));
                  $this->content = $this->smarty->fetch('login/change_password.tpl');
                  break;

              case 'register':
                  require_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlSupport.inc.php';
                  $support = new brdbHtmlSupport();
                  $this->content = $support->register();
                  break;

              default:
                // if there is no user logged in, then show the content to
                // to perform a new login
                $variableNameEmail              = $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
                $variableNameEmailValue         = $this->prgPatternElementLogin->safeGetSessionVariable(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
                $variableNamePassw              = $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_PASSWORD);
                $variableNameAction             = $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_ACTION);
                $variableNameActionLogin        = PrgPatternElementLogin::FORM_LOGIN_ACTION_LOGIN;

                $this->smarty->assign(array(
                    'variableNameEmail'       => $variableNameEmail,
                    'variableNamePassw'       => $variableNamePassw,
                    'formTO'                  => '',
                    'variableNameAction'      => $variableNameAction,
                    'variableNameActionLogin' => $variableNameActionLogin,
                    'imprint'                 => $this->tools->getIniValue('imprint'), # ["Links"]
                    'disclaimer'              => $this->tools->getIniValue('disclaimer'), #["Links"]
                    'registerEnabled'         => $this->tools->getIniValue('registerEnabled') == 'on' ? true : false,

                ));

                $this->content =  $this->smarty->fetch('login/login_form.tpl');
                break;
            }

            $this->smarty->assign(array(
              'content' => $this->content,
            ));
            $this->smarty->display('login.tpl');
        }
    }

    protected function getMessages() {
        if ($this->prgPattern->hasStatus()) {
            foreach ($this->prgPattern->getRegisteredPrgElements() as $prg) {
                $messages[] = $prg->getStatusMessage();
            }
            $this->smarty->assign('messages', $messages);
        }
    }

    private function loadContent() {
        $user   = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $userId = $this->prgPatternElementLogin->getLoggedInUser();

        // load Widgets
        $tournamentWidget = new tournamentWidget();
        $rankingWidget    = new rankingWidget();
        $teamWidget       = new teamWidget();
        $bdayWidget       = new bdayWidget();

        $this->smarty->assign(array(
            'widgetRankingLatestGames'    => $rankingWidget->showWidget('latestGames'),
            'widgetUpcomingTournaments'   => $tournamentWidget->showWidget('upcomingTournaments'),
            'widgetShowTeam'              => $teamWidget->showWidget('showTeam'),
            'widgetShowBdays'             => $bdayWidget->showWidget('nextBdays'),

        ));

        return $this->smarty->fetch('default.tpl');
    }
}
?>
