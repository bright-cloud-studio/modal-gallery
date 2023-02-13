<?php

/**
 * Bright Cloud Studio's Modal Gallery
 *
 * Copyright (C) 2021 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/modal-gallery
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
**/

  
namespace Bcs\Module;
 
use Bcs\Model\ModalGallerySlide;
 
class ModalGalleryModule extends \Contao\Module
{
 
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'modal_gallery_module';
 
	protected $arrStates = array();
 
	/**
	 * Initialize the object
	 *
	 * @param \ModuleModel $objModule
	 * @param string       $strColumn
	 */
	public function __construct($objModule, $strColumn='main')
	{
		parent::__construct($objModule, $strColumn);
		//$this->arrStates = Locations::getStates();
	}
	
    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['locations_list'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
 
        return parent::generate();
    }
 
 
    /**
     * Generate the module
     */
    protected function compile()
    {

	    // add our css
	     if (!in_array('system/modules/modal_gallery/assets/css/modal_gallery.css', $GLOBALS['TL_CSS'])) { 
			$GLOBALS['TL_CSS'][] = 'system/modules/modal_gallery/assets/css/modal_gallery.css';
		}
	    // add our js
	    $GLOBALS['TL_BODY'][] = '<script src="system/modules/modal_gallery/assets/js/modal_gallery.js"></script>';

	    // Sort our Listings based on the 'last_name' field
        $options = [
            'order' => 'id ASC'
        ];
        
		$objSlides = ModalGallerySlide::findBy('published', '1', $options);
		
		
		// Return if no pending items were found
		if (!$objSlides)
		{
			$this->Template->empty = 'No Slides Found';
			return;
		}
        
        $arrThumbs = array();
        $arrSlides = array();
        
		$entry_id = 0;
		$gal = 0;
        foreach ($objSlides as $slide)
		{
		    $arrSlide = array();
            // Set values for template
            
            $arrSlide['id']                     = $slide->id;
            $arrSlide['slide_image']            = $slide->slide_image;
            $arrSlide['slide_name']             = $slide->slide_name;
            $arrSlide['slide_image_url']        = $slide->slide_image_url;
            
            $arrSlide['hotspot_links'] = unserialize($slide->hotspot_links);
            $arrSlide['categories'] = unserialize($slide->category);

            
            
            $this->import('Database');
    		$result = $this->Database->prepare("SELECT * FROM tl_modal_gallery WHERE id='".$this->selectedGallery."'")->execute();
    		while($result->next())
    		{
    			$arrSlide['size_thumb'] = unserialize($result->slide_thumb_image_size);
    			$arrSlide['size_slide'] = unserialize($result->slide_image_size);
    			$arrSlide['hotspot_icon'] = $result->hotspot_icon;
    		}

            // Generate as "List"
            $strListTemplate = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_slide_thumb');
            $objListTemplate = new \FrontendTemplate($strListTemplate);
            $objListTemplate->setData($arrSlide);
            $arrThumbs[$entry_id] = $objListTemplate->parse();

            // Generate as "List"
            $strListTemplate = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_slide');
            $objListTemplate = new \FrontendTemplate($strListTemplate);
            $objListTemplate->setData($arrSlide);
            $arrSlides[$entry_id] = $objListTemplate->parse();

            
            $entry_id++;
		}
        
        $this->Template->thumbs = $arrThumbs;
        $this->Template->slides = $arrSlides;
	}

} 
