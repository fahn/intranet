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
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementImage extends APrgPatternElement {

    private $brdb;

    protected $prgElementLogin;
    
    const _USER_IMAGE_PATH_   = "/static/img/user/";

    public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("image");
        $this->brdb = $brdb;
        $this->prgElementLogin = $prgElementLogin;
    }

    public function processPost() {
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        $isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if (!$this->prgElementLogin->isUserLoggedIn()) {
            return;
        }

        if (!$isUserReporter) {
            return;
        }
    }


    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        
        $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        $isUserAdmin     = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        // Don't process the posts if no user is logged in!
        // otherwise well formed post commands could trigger database actions
        // without theoretically having access to it.
        if ( !$this->prgElementLogin->isUserLoggedIn() || !$isUserAdmin ) {
            return;
        }
        
        #die("12345");
        
        $action = strval(trim($this->getGetVariable('action')));

        switch ($action) {
            case 'delete':
                $this->processGetDeleteImage($this->getGetVariable('id'));
                break;
                
            default:
                break;
        }
        return;
    }
    
    private function processGetDeleteImage($image) {
        if ( empty($image)) {
            $this->setFailedMessage("kein Bild angegeben.");
            return;
        }
        if ( in_array($image, $this->getUserImages())) {
            $this->setFailedMessage("Bild ist noch aktiv");
            return;
        }
        
        $servPath = $_SERVER['BASE_DIR'] .'/';
        $imagePath = $servPath . self::_USER_IMAGE_PATH_ . $image;
        $thumnailImagePath = $servPath . self::_USER_IMAGE_PATH_ .'/thumb_'. $image;
        
        
        if (!file_exists($imagePath)) {
            $this->setFailedMessage("Das Bild existiert nicht.");
            return;
        }
        
        if (!@unlink($imagePath)) {
            $this->setFailedMessage("Das Bild konnte nicht gelöscht werden.");
            return;
        }
        
        if (!@unlink($thumnailImagePath)) {
            $this->setFailedMessage("Das Thumbanil konnte nicht gelöscht werden.");
            return;
        }

        $this->setSuccessMessage("Das Bild wurde gelöscht.");
        Tools::customRedirect(array('page' => 'adminAllImages.php'));
        return true;
    }
    
    
    public function getUserImages() {
        $res = $this->brdb->selectAllUser();
        $data = array();
        if (!$this->brdb->hasError()) {
            while($row = $res->fetch_assoc()) {
                if($row['image']) {
                    $data[] = $row['image'];
                }
            }
        }

        return $data;
    }
}
?>
