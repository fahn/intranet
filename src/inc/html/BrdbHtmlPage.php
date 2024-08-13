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
namespace Badtra\Intranet\Html;


use \Badtra\Intranet\Html\HtmlPageProcessor;
use \Badtra\Intranet\Logic\PrgPatternElementLogin;
use \Badtra\Intranet\Logic\PrgPattern;
use \Badtra\Intranet\DB\BrankDB;

use \Badtra\Intranet\Widget\TournamentWidget;
use \Badtra\Intranet\Widget\RankingWidget;



class BrdbHtmlPage extends HtmlPageProcessor
{

    // smarty
    protected \Smarty $smarty;

    // content
    protected string $content;

    // stage
    protected string $stage;

    protected BrankDB $brdb;
    protected PrgPattern $prgPattern;
    protected PrgPatternElementLogin $prgPatternElementLogin;

    protected int $id;
    protected string $action;
    protected string $page;

    protected bool $isUserLoggedIn;


    protected string $version;


    public function __construct()
    {


       
        /* SQL CONNECTION */
        $this->brdb = new BrankDB();

        /* Login pattern */
        $this->prgPatternElementLogin = new PrgPatternElementLogin();

       
        $this->prgPattern = new PrgPattern();
        $this->prgPattern->registerPrg($this->prgPatternElementLogin);

        

        try {
            $this->id     = $this->prgPatternElementLogin->getGetVariableInteger("id");
            $this->action = $this->prgPatternElementLogin->getGetVariableString("action");
            $this->page   = $this->prgPatternElementLogin->getGetVariableInteger("page");
        } catch (\Exception $e) {
            $details = sprintf("Cannot transfer GET-VAR");
            $message = sprintf("Message: %s", $e->getMessage());
            $this->prgPatternElementLogin->log("GENERELL", $details, $message, "GET");
            unset($details, $message);
        }

        
        // load smarty
        $this->smarty = new \Smarty();

        $this->smarty->registerPlugin('modifier', 'strtotime', 'strtotime');
        $this->smarty->registerPlugin('modifier', 'count', 'count');
        $this->smarty->registerPlugin('modifier', 'intval', 'intval');


        $this->version = $this->prgPatternElementLogin->getSettingString("VERSION");

        // set smarty settings
        $this->smarty->setTemplateDir(__BASE_DIR__ ."/templates");
        $this->smarty->setCompileDir(__BASE_DIR__  ."/templates_c");
        $this->smarty->setConfigDir(__BASE_DIR__  ."/smarty/configs");
        $this->smarty->setCacheDir(__BASE_DIR__ . '/cache');


        if ($this->prgPatternElementLogin->isDeployment())
        {
            $this->smarty->clearAllCache();
            $this->smarty->force_compile  = true;
            $this->smarty->debugging      = false;
            $this->smarty->setErrorReporting(E_ALL);
            $this->smarty->setDebugging(true);
            $this->smarty->caching        = false;
            $this->smarty->cache_lifetime = 0;
            $this->version .="-dev";
        } else {
            // remove notice
            $this->smarty->error_reporting = E_ALL & ~E_NOTICE;
        }

        // check maintenance
        if ($this->prgPatternElementLogin->isMaintenance()) {
            $this->prgPatternElementLogin->customRedirectArray(
                array(
                    "page" => "index.php",
                    "action" => "maintenance",
                )
            );
        }


        // get Messages
        $this->getMessages();

        // load version

        $this->smarty->assign([
            "pageTitle" => $this->prgPatternElementLogin->getSettingString("SITE_NAME"),
            "logoTitle" => $this->prgPatternElementLogin->getSettingString("SITE_NAME"),
            "baseUrl"   => $this->prgPatternElementLogin->getSettingString("BADTRA_URL"),
            "version"   => $this->version,
        ]);

        $badtra = [
            "copyright"      => $this->prgPatternElementLogin->getSettingBool("BADTRA_COPYRIGHT"),
            "url"            => $this->prgPatternElementLogin->getSettingString("BADTRA_URL"),
            "docs"           => $this->prgPatternElementLogin->getSettingString("BADTRA_MANUAL"),
        ];


        $this->smarty->assign([
                // rights
                // ini values
                "rankingEnable"      => $this->prgPatternElementLogin->getSettingBool("RANKING_ENABLE"),
                // @TODO
                "cupEnable"          => true,
                "tournamentEnable"   => $this->prgPatternElementLogin->getSettingBool("TOURNAMENT_ENABLE"),
                "faqEnabled"         => $this->prgPatternElementLogin->getSettingBool("FAQ_ENABLE"),
                "social"             => $this->prgPatternElementLogin->getSettingArray("SOCIAL_LINK"),
                "newsEnable"         => $this->prgPatternElementLogin->getSettingBool("NEWS_ENABLE"),
                "badtra"             => $badtra,
                #$notification->getNotification(),
        ]);

        // check if user is logged in
        $this->isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
        // @TODO REMOVE
        $this->isUserLoggedIn = 1;
        $this->loadUserLoggingContent();


        // $basename = basename($_SERVER["PHP_SELF"]);
        $actionArray = ["login", "register", "requestPassword", "changePassword", "maintenance"];

        if ($this->isUserLoggedIn === false && in_array($this->action, $actionArray)) {

                // get REFERENCE URL and save to session
                $_SESSION["ref"] = sprintf("%s://%s/%s",
                    isset($_SERVER["HTTPS"]) ? "https" : "http",
                    $_SERVER["HTTP_HOST"],
                    $_SERVER["REQUEST_URI"]
                );

                /* $this->prgPatternElementLogin->customRedirectArray(
                    array(
                        "page" => "index.php",
                        "action" => "login",
                    )
                );
                */
        }
        

        


        $this->prgPattern->processPRG();




        return; 
    }//end __construct()

    private function loadUserLoggingContent () {
        if (isset($this->isUserLoggedIn) && $this->isUserLoggedIn === true) {
            $user = $this->prgPatternElementLogin->getLoggedInUser();
            // get User Image
            $currentUserName  = $user->getFullName();
            $currentUserImage = $user->getUserThumbnailImage();
            $this->smarty->registerObject("user", $user);
            $this->smarty->assign([
                "currentUserName"    => $currentUserName,
                "currentUserImage"   => $currentUserImage,
                "isUserLoggedIn"     => $this->isUserLoggedIn,
                "isAdmin"            => $this->prgPatternElementLogin->getLoggedInUser()->isAdmin(),
                "isReporter"         => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
                "userId"             => $this->prgPatternElementLogin->getLoggedInUser()->getId(),
            ]);

            //$randkedWidget    = new \Badtra\Intranet\Widget\RankingWidget($user->getUserId());
            //$tournamentWidget = new \Badtra\Intranet\Widget\TournamentWidget();
            //$staffWidget      = new \Badtra\Intranet\Widget\TeamWidget();
            //$bdayWidget       = new \Badtra\Intranet\Widget\BdayWidget();
            //$newsWidget       = new \Badtra\Intranet\Widget\NewsWidget();


            $this->smarty->assign([
                //"widgetRankingLatestGames"    => $randkedWidget->showWidget("latestGames"),
                //"widgetUpcomingTournaments"   => $tournamentWidget->showWidget("upcomingTournaments"),
                // "widgetShowTeam"              => $staffWidget->showWidget("showTeam"),
                // "widgetShowBdays"             => $bdayWidget->showWidget("nextBdays"),
                // "widgetLatestNews"            => $newsWidget->showWidget("latestNews"),
            ]);
            unset($randkedWidget, $tournamentWidget, $staffWidget, $bdayWidget, $newsWidget);
        }
    }


    public function processPage() {
        //phpinfo();
        
        //$this->loadContent();
        //$this->defaultView();
        //echo $this->loginView();

        //$this->smarty->display('default.tpl');



        // switch($this->action) {
        //     case "login":
        //         echo $this->loginView();
        //         break;
        //     case "register":
        //         $register = new \Badtra\Intranet\Html\RegistrationPage();
        //         echo $register->registerView();
        //         break;
        //     case "requestPassword":
        //         echo $this->requestPasswordView();
        //         break;
        //     case "changePassword":
        //         echo $this->changePasswordView();
        //         break;
        //     case "maintenance":
        //         $maintenance = new \Badtra\Intranet\Html\MaintenancePage();
        //         echo $maintenance->defaultView();
        //         break;
        //     default:
                echo $this->defaultView();
        //         break;
        // }

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
    protected function htmlBody() 
    {
        
        $isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
       
        // get Messages
        $this->getMessages();

        if ($isUserLoggedIn)
        {
            $this->smarty->assign(array(
                    "content"      => $this->loadContent(),
                    #"notification" => $this->getNotification(),
            ));
            $this->smarty->display("default.tpl");
        }
       
    }

    protected function getMessages() 
    {
        if ($this->prgPattern->hasStatus()) {
            foreach ($this->prgPattern->getRegisteredPrgElements() as $prg) {
                $messages[] = $prg->getStatusMessage();
            }
            $this->smarty->assign("messages", $messages);
        }
    }

    private function loadContent() {
        //$userPattern   = new \Badtra\Intranet\Logic\PrgPatternElementUser($this->prgPatternElementLogin);
        //$user = $this->prgPatternElementLogin->getLoggedInUser();

        // load Widgets 
//        $rankingWidget    = new RankingWidget($user->getID());



        

        //return $this->smarty->fetch("default.tpl");
    }

    /**
     * @Route("/", name="blog_home_index")
     *
     * @return string
     */
    public function defaultView():string
    {

        $tournamentWidget = new TournamentWidget();
        $rankingWidget    = new RankingWidget();
        $team = new \Badtra\Intranet\Widget\TeamWidget($this->smarty, $this->brdb);
        $news = new \Badtra\Intranet\Widget\NewsWidget($this->smarty, $this->brdb);
        
        $this->smarty->assign([
            "widgetRankingLatestGames"    => $rankingWidget->showWidget("latestGames"),
            "widgetUpcomingTournaments"   => $tournamentWidget->upcomingTournamentView(),
            "widgetShowTeam"              => $team->showWidget("showTeam"),
            // "widgetShowBdays"             => \Badtra\Intranet\Widget\BdayWidget::showWidget("nextBdays"),
            "widgetLatestNews"            => $news->showWidget("latestNews"),
        ]);
        

        return $this->smarty->fetch("default.tpl");
    }
    
    public function loginView(): string {
        $links = [
            "imprint"         => $this->prgPatternElementLogin->getSettingString("IMPRINT_LINK"),
            "disclaimer"      => $this->prgPatternElementLogin->getSettingString("DISCLAIMER_LINK"),
            "registerEnabled" => $this->prgPatternElementLogin->getSettingBool("REGISTER_ENABLE"),
        ];
        //$this->smarty->assign();
        $this->smarty->assign([
            "links"   => $links,
        ]);
        return $this->showDefault($this->smarty->fetch('login/login_form.tpl'));     
    }

    public function requestPasswordView(): string {
        $token = $this->prgPatternElementLogin->getGetVariable("token");
        $mail  = $this->prgPatternElementLogin->getGetVariable("mail");

        return $this->showDefault($this->smarty->fetch('login/request_password.tpl'));       
    }

    public function changePasswordView(): string {
        return $this->showDefault($this->smarty->fetch('login/change_password.tpl'));       
    }

    private function showDefault(string $content) {
        
        $this->smarty->assign([
            'content' => $content,
        ]);

        return $this->smarty->fetch("page_login.tpl"); 
    }

    public function smartyFetchWrap(string $filename) 
    {
        //return $this->smarty->fetch("page_wrap_header.tpl") . $this->smarty->fetch($filename) . $this->smarty->fetch("page_wrap_footer.tpl"); 

        return $this->smarty->fetch($filename); 
    }
}//end class
