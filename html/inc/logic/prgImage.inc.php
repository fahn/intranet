<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/
include_once 'prgPattern.inc.php';

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

    public function __construct(PrgPatternElementLogin $prgElementLogin) {
        parent::__construct("image");

        $this->prgElementLogin = $prgElementLogin;
    }

    public function processPost() {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array('reporter', 'admin'), 'or');
    }


    /**
     *
     * {@inheritDoc}
     * @see IPrgPatternElement::processGet()
     */
    public function processGet() {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array('admin'));

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
        
        $servPath = BASE_DIR .'/';
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
        $this->customRedirectArray(array('page' => 'adminAllImages.php'));
        return true;
    }
    
    
    public function getUserImages() {
        return $this->brdb->getUserImages();
    }
}
?>
