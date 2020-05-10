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

use Symfony\Component\Routing\Annotation\Route;

require_once BASE_DIR .'/vendor/autoload.php';

class BrdbHtmlPage extends \Badtra\Intranet\Html\HtmlPageProcessor
{

    // smarty
    protected \Smarty $smarty;

    // content
    protected string $content;

    // stage
    protected string $stage;

    protected \Badtra\Intranet\DB\BrankDB $brdb;
    protected \Badtra\Intranet\Logic\PrgPattern $prgPattern;
    protected \Badtra\Intranet\Logic\PrgPatternElementLogin $prgPatternElementLogin;

    protected int $id;
    protected string $action;
    protected string $page;

    protected bool $isUserLoggedIn;


    public function __construct()
    {
       
        /* SQL CONNECTION */
        $this->brdb = new \Badtra\Intranet\DB\BrankDB();

        /* Login pattern */
        $this->prgPatternElementLogin = new \Badtra\Intranet\Logic\PrgPatternElementLogin($this->brdb);

       
        $this->prgPattern = new \Badtra\Intranet\Logic\PrgPattern();
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

        $this->version = $this->prgPatternElementLogin->getSettingString("VERSION");

        // set smarty settings
        $this->smarty->setTemplateDir(BASE_DIR ."/templates");
        $this->smarty->setCompileDir(BASE_DIR  ."/templates_c");
        $this->smarty->setConfigDir(BASE_DIR  ."/smarty/configs");


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
                //"social"             => $this->prgPatternElementLogin->getSettingArray("SOCIAL_LINK"),
                "newsEnable"         => $this->prgPatternElementLogin->getSettingBool("NEWS_ENABLE"),
                "badtra"             => $badtra,
                #$notification->getNotification(),
        ]);

        $this->isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();

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

            $randkedWidget    = new \Badtra\Intranet\Widget\RankingWidget($user->getUserId());
            $tournamentWidget = new \Badtra\Intranet\Widget\TournamentWidget();
            $staffWidget      = new \Badtra\Intranet\Widget\TeamWidget();
            $bdayWidget       = new \Badtra\Intranet\Widget\BdayWidget();
            $newsWidget       = new \Badtra\Intranet\Widget\NewsWidget();


            $this->smarty->assign([
                "widgetRankingLatestGames"    => $randkedWidget->showWidget("latestGames"),
                "widgetUpcomingTournaments"   => $tournamentWidget->showWidget("upcomingTournaments"),
                "widgetShowTeam"              => $staffWidget->showWidget("showTeam"),
                "widgetShowBdays"             => $bdayWidget->showWidget("nextBdays"),
                "widgetLatestNews"            => $newsWidget->showWidget("latestNews"),
            ]);
            unset($randkedWidget, $tournamentWidget, $staffWidget, $bdayWidget, $newsWidget);
        }


        $this->prgPattern->processPRG();


        /*
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



        // check maintenance
        #if ($this->prgPatternElementLogin->isMaintenance()) {
        #    $this->prgPatternElementLogin->customRedirectArray("maintenance.php");
        #}

        /* LOAD SETTINGS */
        //$this->settings = $this->brdb->loadAllSettings();

        /* goto Login */
        /*
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
        */
    }//end __construct()


    public function processPage() {}



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
        /*
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
      */  
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



        

        return $this->smarty->fetch("default.tpl");
    }

    /**
     * @Route("/", name="blog_home_index")
     *
     * @return string
     */
    public function defaultView():string
    {
        /*
        $this->smarty->assign([
            "widgetRankingLatestGames"    => \Badtra\Intranet\Widget\RankingWidget::showWidget("latestGames"),
            "widgetUpcomingTournaments"   => \Badtra\Intranet\Widget\TournamentWidget::showWidget("upcomingTournaments"),
            "widgetShowTeam"              => \Badtra\Intranet\Widget\TeamWidget::showWidget("showTeam"),
            "widgetShowBdays"             => \Badtra\Intranet\Widget\BdayWidget::showWidget("nextBdays"),
            "widgetLatestNews"            => \Badtra\Intranet\Widget\NewsWidget::showWidget("latestNews"),
        ]);
        */

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
        $token = $this->PrgPatternElementLogin->getGetVariable("token");
        $mail  = $this->PrgPatternElementLogin->getGetVariable("mail");

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
        return $this->smarty->fetch("page_wrap_header.tpl") . $this->smarty->fetch($filename) . $this->smarty->fetch("page_wrap_footer.tpl"); 
    }
}//end class
