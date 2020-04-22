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
require_once "htmlPage.inc.php";

// DB
require_once BASE_DIR ."/inc/db/brdb.inc.php";

// Logic
require_once BASE_DIR ."/inc/logic/prgLogin.inc.php";
require_once BASE_DIR ."/inc/logic/prgUser.inc.php";
require_once BASE_DIR ."/inc/logic/prgSettings.inc.php";

// load widgets
require_once BASE_DIR ."/inc/widget/ranking.widget.php";
require_once BASE_DIR ."/inc/widget/tournament.widget.php";
require_once BASE_DIR ."/inc/widget/team.widget.php";
require_once BASE_DIR ."/inc/widget/bday.widget.php";
require_once BASE_DIR ."/inc/widget/news.widget.php";

// notification
//require_once BASE_DIR ."/inc/html/brdbHtmlNotification.inc.php";

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
abstract class AHtmlLoginPage extends HtmlPageProcessor
{
    protected BrankDB $brdb;
    protected PrgPattern $prgPattern;
    protected PrgPatternElementLogin $prgPatternElementLogin;

    protected int $id;
    protected string $action;
    protected string $page;

    /**
     * Standard Constructor for the HTML Login page. It
     * takes care of creating the connection to the data base
     * and stores the DB connection object into the member variable
     * $brdb. it is protected and therefore accessible by all derived
     * classes (pages)
     */
    public function __construct()
    {
        parent::__construct();
       
        /* SQL CONNECTION */
        $this->brdb = new BrankDB();

        /* Login pattern */
        $this->prgPatternElementLogin = new PrgPatternElementLogin($this->brdb);
       
        $this->prgPattern = new PrgPattern();
        $this->prgPattern->registerPrg($this->prgPatternElementLogin);

        try {
            $this->id     = intval(trim($this->prgPatternElementLogin->getGetVariable("id")));
            $this->action = strval(trim($this->prgPatternElementLogin->getGetVariable("action")));
            $this->page = intval(trim($this->prgPatternElementLogin->getGetVariable("page")));
        } catch (Exception $e) {
            $details = sprintf("Cannot transfer GET-VAR");
            $message = sprintf("Message: %s", $e->getMessage());
            $this->prgPatternElementLogin->log("GENERELL", $details, $message, "GET");
            unset($details, $message);
        }

        // load smarty
        $this->smarty = new Smarty();



        // check maintenance
        #if ($this->prgPatternElementLogin->isMaintenance()) {
        #    $this->prgPatternElementLogin->customRedirectArray("maintenance.php");
        #}

        /* LOAD SETTINGS */
        //$this->settings = $this->brdb->loadAllSettings();

        /* goto Login */
        $basename = basename($_SERVER["PHP_SELF"]);

        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();

        if ($basename != "index.php" && $isUserLoggedIn === false)
        {
            $_SESSION["ref"] = (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $this->PrgPatternElementLogin->customRedirectArray(array(
              "page" => "index.php",
            ));
        }
        return;
    }

    public function processPage() {
        $this->getMessages();

        // load version
        $this->version = $this->prgPatternElementLogin->getSettingString("VERSION");

       

        $this->smarty->setTemplateDir(BASE_DIR ."/templates");
        $this->smarty->setCompileDir(BASE_DIR  ."/templates_c");
        $this->smarty->setConfigDir(BASE_DIR  ."/smarty/configs");

       

        if ($this->prgPatternElementLogin->isDeployment())
        {
            $this->smarty->setCacheDir(BASE_DIR  ."/cache");
            // @TODO: set debug bar
            $this->smarty->clear_all_cache();
            $this->smarty->force_compile  = true;
            $this->smarty->debugging      = false;
            $this->smarty->caching        = false;
            $this->smarty->cache_lifetime = 0;
            $$this->version .="-dev";
        }
        else
        {
            // remove notice
            $this->smarty->error_reporting = E_ALL & ~E_NOTICE;
        }

        $this->smarty->assign(array(
            "pageTitle" => $this->prgPatternElementLogin->getSettingString("SITE_NAME"),
            "logoTitle" => $this->prgPatternElementLogin->getSettingString("SITE_NAME"),
            "baseUrl"   => $this->prgPatternElementLogin->getSettingString("BADTRA_URL"),
            "version"   => $this->version,
        ));

        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
        if ($isUserLoggedIn AND $this->smarty)
        {
            // user
            $user = $this->prgPatternElementLogin->getLoggedInUser();
            // get User Image
            $currentUserName  = $user->getFullName();
            $currentUserImage = $user->getUserThumbnailImage();
            $this->smarty->registerObject("user", $user);

            // Notification
            #$notification = new Notification();

            $badtra = array();
            /*
                "copyright"      => $this->prgPatternElementLogin->getSettingBool("BADTRA_COPYRIGHT"),
                "url"            => $this->prgPatternElementLogin->getSettingString("BADTRA_URL"),
                "docs"           => $this->prgPatternElementLogin->getSettingString("BADTRA_MANUAL"),
            );*/


            $this->smarty->assign(array(
                    "currentUserName"    => $currentUserName,
                    "currentUserImage"   => $currentUserImage,
                    "isUserLoggedIn"     => $isUserLoggedIn,
                    // rights
                    "isAdmin"            => $this->prgPatternElementLogin->getLoggedInUser()->isAdmin(),
                    "isReporter"         => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
                    "userId"             => $this->prgPatternElementLogin->getLoggedInUser()->getId(),
                    // ini values
                    "rankingEnable"      => $this->prgPatternElementLogin->getSettingBool("RANKING_ENABLE"),
                    // @TODO
                    "cupEnable"          => true,
                    "tournamentEnable"   => $this->prgPatternElementLogin->getSettingBool("TOURNAMENT_ENABLE"),
                    "faqEnabled"         => $this->prgPatternElementLogin->getSettingBool("FAQ_ENABLE"),
                    //"social"             => $this->prgPatternElementLogin->getSettingArray("SOCIAL_LINK"),
                    "newsEnable"         => $this->prgPatternElementLogin->getSettingBool("NEWS_ENABLE"),
                    "Badtra"             => $badtra,
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
     * @Route("/")
     */
    protected function htmlBody() {
        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
       
        // get Messages
        $this->getMessages();

        if ($isUserLoggedIn)
        {
            $this->smarty->assign(array(
                    "content"      => $this->loadContent(),
                    #"notification" => $this->getNotification(),
            ));
            $this->smarty->display("index.tpl");
        }
        else
        {
            $action = $this->prgPatternElementLogin->getGetVariable("action");
            $request = isset($action) ? $action : "";

           

            $links = array(
                "imprint"         => $this->prgPatternElementLogin->getSettingString("IMPRINT_LINK"),
                "disclaimer"      => $this->prgPatternElementLogin->getSettingString("DISCLAIMER_LINK"),
                "registerEnabled" => $this->prgPatternElementLogin->getSettingBool("REGISTER_ENABLE"),
            );

            switch ($request)
            {
                case "request_password":
                    $this->content = $this->smarty->fetch("login/request_password.tpl");
                    break;

                case "change_password":
                    $token = $this->PrgPatternElementLogin->getGetVariable("token");
                    $mail  = $this->PrgPatternElementLogin->getGetVariable("mail");
                    $this->smarty->assign(array(
                        "token" => $token,
                        "mail"  => $mail,
                    ));
                    $this->content = $this->smarty->fetch("login/change_password.tpl");
                    break;

                case "register":
                    require_once BASE_DIR ."/inc/html/brdbHtmlSupport.inc.php";
                    $support = new brdbHtmlSupport();
                    $this->content = $support->register();
                    break;

                default:
                    $this->smarty->assign("links", $links);
                    $this->content =  $this->smarty->fetch("login/login_form.tpl");
                    break;
            }

            $this->smarty->assign(array(
                "content" => $this->content,
            ));
            $this->smarty->display("login.tpl");
        }
    }

    protected function getMessages() {
        if ($this->prgPattern->hasStatus()) {
            foreach ($this->prgPattern->getRegisteredPrgElements() as $prg) {
                $messages[] = $prg->getStatusMessage();
            }
            $this->smarty->assign("messages", $messages);
        }
    }

    private function loadContent() {
        $userPattern   = new PrgPatternElementUser($this->prgPatternElementLogin);
        $user = $this->prgPatternElementLogin->getLoggedInUser();

        // load Widgets
        $tournamentWidget = new TournamentWidget();
        $rankingWidget    = new RankingWidget($user->getID());
        $teamWidget       = new TeamWidget();
        $bdayWidget       = new BdayWidget();
        $newsWidget       = new NewsWidget();

        $this->smarty->assign(array(
            "widgetRankingLatestGames"    => $rankingWidget->showWidget("latestGames"),
            "widgetUpcomingTournaments"   => $tournamentWidget->showWidget("upcomingTournaments"),
            "widgetShowTeam"              => $teamWidget->showWidget("showTeam"),
            "widgetShowBdays"             => $bdayWidget->showWidget("nextBdays"),
            "widgetLatestNews"            => $newsWidget->showWidget("latestNews"),

        ));

        return $this->smarty->fetch("default.tpl");
    }

   
}

