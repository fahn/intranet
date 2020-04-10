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
$path=dirname(dirname(__FILE__));
require($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgImage.inc.php';

class BrdbHtmlAdminAllImagesPage extends BrdbHtmlPage {
  private $prgPatternElementImage;
  
  const _USER_IMAGE_PATH_   = "/static/img/user/";

  //
  private $_page = "";

  const MAX_ENTRIES = 50;

    public function __construct($page = null) {
        parent::__construct();

        $this->prgPatternElementImage = new PrgPatternElementImage($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementImage);

        if ($page != null) {
             $this->_page = $page;
        }

        # load links
        $links = array(
            'add' => $this->prgPatternElementImage->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->prgPatternElementImage->linkTo(array('page' => $this->_page, 'action' => 'add')),
        );

        $this->smarty->assign('links', $links);
    }


    public function htmlBody() {
        switch ($this->action) {
          default:
            $content = $this->loadContent();
            break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent() {
        $this->smarty->assign(array(
            'images'      => $this->scanDirectory(),
        ));
        return $this->smarty->fetch('images/adminList.tpl');
    }
    
    private function scanDirectory() {
        $userImages = $this->prgPatternElementImage->getUserImages();
        
        $servPath = BASE_DIR .'/';
        $images = glob($servPath . self::_USER_IMAGE_PATH_ . "/*");
        
        $data = array();
        foreach($images as $image) {
            $tmpImage = str_replace($servPath, "", $image);
            $query = "thumb_";
            if (substr(basename($tmpImage), 0, strlen($query)) === $query) {
                continue;
            }
            
            // deletelink
            $searchString = "default";
            $pos = stripos($tmpImage, $searchString);
            
            $deleteLink = $this->prgPatternElementImage->linkTo(array('page' => $this->_page, 'action' => 'delete', 'id' => basename($tmpImage)));
            if ($pos !== false) {
                $deleteLink = "";
            }
            $data[] = array(
                'image'      => $tmpImage,
                'deleteLink' => $deleteLink,
                'active'     => in_array(basename($tmpImage), $userImages) ? 1 : 0,
            );
        }
        
        return $data;
    }
}

?>
