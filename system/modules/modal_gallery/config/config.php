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


/* Back end modules */
$GLOBALS['TL_LANG']['MOD']['modal_gallery'][0] = "Modal Gallery";
$GLOBALS['BE_MOD']['modal_gallery']['modal_gallery'] = array(
	'tables' => array('tl_modal_gallery', 'tl_modal_gallery_slide'),
	'icon'   => 'system/modules/modal_gallery/assets/icons/modal_gallery.png',
	'exportLocations' => array('Bcs\Backend\ModalGalleryBackend', 'exportModalGallery')
);
$GLOBALS['BE_MOD']['modal_gallery']['slide_category'] = array(
	'tables' => array('tl_slide_category'),
	'icon'   => 'system/modules/modal_gallery/assets/icons/slide_category.png',
	'exportLocations' => array('Bcs\Backend\SlideCategory', 'exportSlideCategories')
);

/* Front end modules */
$GLOBALS['FE_MOD']['modal_gallery']['modal_gallery_module'] 	= 'Bcs\Module\ModalGalleryModule';

/* Models */
$GLOBALS['TL_MODELS']['tl_modal_gallery']			= 'Bcs\Model\ModalGallery';
$GLOBALS['TL_MODELS']['tl_modal_gallery_slide']			= 'Bcs\Model\ModalGallerySlide';
$GLOBALS['TL_MODELS']['tl_slide_category']			= 'Bcs\Model\SlideCategory';

/* Add Backend JS */
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_JAVASCRIPT']['smg']			= 'system/modules/modal_gallery/assets/js/modal_gallery_backend.js';
	$GLOBALS['TL_CSS'][]					= 'system/modules/modal_gallery/assets/css/modal_gallery_backend.css';
}
