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

        return parent::generate();
    }

    /**
     * Compile the module
     */
    protected function compile()
    {
        // add our CSS
       $GLOBALS['TL_CSS']['modal_css'] = 'bundles/bcsmodalgallery/css/modal_gallery.css';


        // add our JS
        $GLOBALS['TL_BODY']['modal_js'] = '<script src="bundles/bcsmodalgallery/js/modal_gallery.js"></script>';

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
        $arrCategoriesRoom    = [];
        $arrCategoriesProduct = [];

        $entry_id = 1;

        // Loop through slides
        foreach ($objSlides as $slide)
        {
            $arrSlide = [];

            // Set values for template
            $arrSlide['id']              = $entry_id;
            $arrSlide['slide_image']     = $slide->slide_image;
            $arrSlide['slide_name']      = $slide->slide_name;
            $arrSlide['slide_image_url'] = $slide->slide_image_url;

            $arrSlide['hotspot_links']      = unserialize($slide->hotspot_links);
            $arrSlide['categories_room']    = unserialize($slide->category_room);
            $arrSlide['categories_product'] = unserialize($slide->category_product);

            // Fetch gallery settings (thumb & slide sizes, hotspot icon)
            $this->import('Database');
            $result = $this->Database
                           ->prepare("SELECT * FROM tl_modal_gallery WHERE id=?")
                           ->execute($this->selectedGallery);

            if ($result->numRows)
            {
                $arrSlide['size_thumb']   = unserialize($result->slide_thumb_image_size);
                $arrSlide['size_slide']   = unserialize($result->slide_image_size);
                $arrSlide['hotspot_icon'] = $result->hotspot_icon;
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
        $this->import('Database');
        $result = $this->Database->prepare("SELECT * FROM tl_category_room")->execute();
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
        $result = $this->Database->prepare("SELECT * FROM tl_category_product")->execute();
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
        $this->Template->categories_room   = $arrCategoriesRoom;
        $this->Template->categories_product= $arrCategoriesProduct;
    }
}
