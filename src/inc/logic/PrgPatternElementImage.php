<?php
/*******************************************************************************
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
namespace Badtra\Intranet\Logic;
require_once "PrgPattern.php";

class PrgPatternElementImage extends APrgPatternElement
{

    protected $prgElementLogin;
   
    const _USER_IMAGE_PATH_ = "/static/img/user/";


    public function __construct(PrgPatternElementLogin $prgElementLogin)
    {
        parent::__construct("image");

        $this->prgElementLogin = $prgElementLogin;
    }//end __construct()


    public function processPost(): void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(["reporter", "admin"], "or");
    }//end processPost()


    /**
     * {@inheritDoc}
     *
     * @see IPrgPatternElement::processGet()
     */
    public function processGet(): void
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(["admin"]);

        $action = strval(trim($this->getGetVariable("action")));
        switch ($action) {
            case "delete":
                $this->processGetDeleteImage($this->getGetVariable("id"));
                break;
               
            default:
                break;
        }
    }//end processGet()


    /**
     * Delete image/Thumbnail from Server
     *
     * @param  string $image
     * @return boolean
     */
    private function processGetDeleteImage(string $image):bool
    {
        try {
            if (!isset($image)) {
                throw new \Exception("kein Bild angegeben.");
            }
            if (in_array($image, $this->getUserImages())) {
                throw new \Exception("Bild ist noch aktiv");
            }
           
            $servPath          = BASE_DIR."/";
            $imagePath         = $servPath.self::_USER_IMAGE_PATH_.$image;
            $thumnailImagePath = $servPath.self::_USER_IMAGE_PATH_."/thumb_".$image;
           
           
            if (!file_exists($imagePath)) {
                throw new \Exception("Das Bild existiert nicht.");
            }
           
            if (!@unlink($imagePath)) {
                throw new \Exception("Das Bild konnte nicht gelöscht werden.");
            }
           
            if (!@unlink($thumnailImagePath)) {
                throw new \Exception("Das Thumbanil konnte nicht gelöscht werden.");
            }
   
            $this->setSuccessMessage("Das Bild wurde gelöscht.");
            $this->customRedirectArray(["page" => "adminAllImages.php"]);
            return true;
        } catch (\Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot delete Image. %s Details %s", $image, $e->getMessage()), "", "GET");
            $this->setFailedMessage($e->getMessage());
            return false;
        }//end try

    }//end processGetDeleteImage()


    /**
     * get all User Images
     *
     * @return array
     */
    public function getUserImages():?array
    {
        return $this->brdb->getUserImages();
    }//end getUserImages()
}//end class
