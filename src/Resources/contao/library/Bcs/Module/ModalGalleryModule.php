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

use Bcs\Model\ModalGallery;
use Bcs\Model\ModalGallerySlide;

use Contao\Database;
use Contao\System;
use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Contao\StringUtil;

class ModalGalleryModule extends \Contao\Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'modal_gallery_module';
    protected $modal_gallery;

    /**
     * Initialize the object
     *
     * @param \ModuleModel $objModule
     * @param string       $strColumn
     */
    public function __construct($objModule, $strColumn='main')
    {
        parent::__construct($objModule, $strColumn);
    }

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
        {
            $objTemplate = new \Contao\BackendTemplate('be_wildcard');


            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['locations_list'][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;

            return $objTemplate->parse();
        }
        
        // Change our templates based on the style
        $this->modal_gallery = ModalGallery::findByPk($this->selectedGallery);
        if (!$this->customTpl && $this->modal_gallery !== null && $this->modal_gallery->template_style != 'vertical')
        {
            $this->strTemplate = 'modal_gallery_module_' . $this->modal_gallery->template_style;
        }
    
        return parent::generate();
    }

    /**
     * Compile the module
     */
    protected function compile()
    {
        // add our CSS
       $GLOBALS['TL_CSS']['modal_css'] = 'bundles/bcsmodalgallery/css/modal_gallery.css';


        // Change our script depending on the style
        if($this->modal_gallery->template_style == 'accordion')
            $GLOBALS['TL_BODY']['modal_js'] = '<script src="bundles/bcsmodalgallery/js/modal_gallery_accordion.js"></script>';
        if($this->modal_gallery->template_style == 'comparison')
            $GLOBALS['TL_BODY']['modal_js'] = '<script src="bundles/bcsmodalgallery/js/modal_gallery_comparison.js"></script>';
        else if($this->modal_gallery->template_style == 'vertical' || $this->modal_gallery->template_style == 'horizontal')
            $GLOBALS['TL_BODY']['modal_js'] = '<script src="bundles/bcsmodalgallery/js/modal_gallery_horizontal_vertical.js"></script>';

        // 1) Ensure a gallery is selected
        if (!$this->selectedGallery)
        {
            $this->Template->empty = 'No Slides Found';
            return;
        }

        // 2) Prepare options for sorting
        $options = [
            'order' => 'sorting ASC'
        ];

        // 3) Fetch only slides belonging to the selected gallery AND published
        $objSlides = ModalGallerySlide::findBy(
            ['pid=?', 'published=?'],
            [(int) $this->selectedGallery, '1'],
            $options
        );

        // 4) Bail out if no slides found
        if (!$objSlides)
        {
            $this->Template->empty = 'No Slides Found';
            return;
        }

        $arrThumbs            = [];
        $arrSlides            = [];
        
        $slides_comparison    = [];
        
        $arrCategoriesRoom    = [];
        $arrCategoriesProduct = [];

        $entry_id = 1;
        
        //$modal_gallery = Database::getInstance()->prepare("SELECT * FROM tl_modal_gallery WHERE id=?")->execute($this->selectedGallery);
        
        // Loop through slides
        foreach ($objSlides as $slide)
        {

            $new_slide = [
                'title' => $slide->caption_title,
                'text'  => $slide->caption_body,
                'image' => $slide->slide_image_url,
                'alt'   => 'TEST 123'
            ];
            
            $hotspot = [];
            $new_slide['hotspots'] = []; // start with an empty array
            foreach (unserialize($slide->hotspot_links) as $hotspot_data) {
                $hotspot = [
                    'top'   => $hotspot_data['hotspot_y'] . '%',
                    'left'  => $hotspot_data['hotspot_x'] . '%',
                    'title' => $hotspot_data['hotspot_title'],
                    'body'  => $hotspot_data['hotspot_text'],
                ];
                $new_slide['hotspots'][] = $hotspot;
            }
            
            $slides_comparison[] = $new_slide;

            $arrSlide = [];

            // Set values for template
            $arrSlide['id']              = $entry_id;
            
            $arrSlide['slide_image']     = $slide->slide_image;
            $arrSlide['slide_name']      = $slide->slide_name;
            $arrSlide['slide_image_url'] = $slide->slide_image_url;

            $arrSlide['hotspot_links']      = unserialize($slide->hotspot_links);
            $arrSlide['categories_room']    = unserialize($slide->category_room);
            $arrSlide['categories_product'] = unserialize($slide->category_product);

            if ($this->modal_gallery)
            {
                $arrSlide['size_thumb']   = unserialize($this->modal_gallery->slide_thumb_image_size);
                $arrSlide['size_slide']   = unserialize($this->modal_gallery->slide_image_size);
                $arrSlide['hotspot_icon'] = $this->modal_gallery->hotspot_icon;
            }

            // Generate thumb template
            $strThumbTpl = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_slide_thumb');
            $objThumbTpl = new \Contao\FrontendTemplate($strThumbTpl);
            $objThumbTpl->setData($arrSlide);
            $arrThumbs[$entry_id] = $objThumbTpl->parse();

            // Generate slide template
            $strSlideTpl = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_slide');
            $objSlideTpl = new \Contao\FrontendTemplate($strSlideTpl);
            $objSlideTpl->setData($arrSlide);
            $arrSlides[$entry_id] = $objSlideTpl->parse();

            $entry_id++;
        }

        // Build “Room” category filter
        $result = Database::getInstance()->prepare("SELECT * FROM tl_category_room")->execute();
        $cat_id = 1;

        while ($result->next())
        {
            if ($result->published)
            {
                $arrCategory = [
                    'id'    => $result->id,
                    'alias' => $result->alias,
                    'name'  => $result->name,
                ];

                $strCatTpl = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_modal_category');
                $objCatTpl = new \Contao\FrontendTemplate($strCatTpl);
                $objCatTpl->setData($arrCategory);
                $arrCategoriesRoom[$cat_id] = $objCatTpl->parse();
                $cat_id++;
            }
        }

        // Build “Product” category filter
        $result = Database::getInstance()->prepare("SELECT * FROM tl_category_product")->execute();
        $cat_id = 1;

        while ($result->next())
        {
            if ($result->published)
            {
                $arrCategory = [
                    'id'    => $result->id,
                    'alias' => $result->alias,
                    'name'  => $result->name,
                ];

                $strCatTpl = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_modal_category');
                $objCatTpl = new \Contao\FrontendTemplate($strCatTpl);
                $objCatTpl->setData($arrCategory);
                $arrCategoriesProduct[$cat_id] = $objCatTpl->parse();
                $cat_id++;
            }
        }

        // Assign to main template
        $this->Template->thumbs            = $arrThumbs;
        $this->Template->slides            = $arrSlides;

        $this->Template->slides_serialized = $jsonResult = json_encode($slides_comparison);
        $this->Template->accordion_active_percentage = $this->modal_gallery->accordion_active_percentage;
        
        $this->Template->categories_room   = $arrCategoriesRoom;
        $this->Template->categories_product= $arrCategoriesProduct;
        
        $this->Template->gallery_style = $this->strTemplate;
        
        //echo "Template: " . $this->strTemplate;
        //die();
        
    }
}
