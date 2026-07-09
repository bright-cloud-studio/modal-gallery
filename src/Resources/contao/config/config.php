<?php

/* Back end modules */
$GLOBALS['TL_LANG']['MOD']['modal_gallery'][0] = "Modal Gallery";
$GLOBALS['BE_MOD']['modal_gallery']['modal_gallery'] = array(
	'tables' => array('tl_modal_gallery', 'tl_modal_gallery_slide'),
	'icon'   => 'system/modules/modal_gallery/assets/icons/modal_gallery.png',
	'exportLocations' => array('Bcs\Backend\ModalGalleryBackend', 'exportModalGallery')
);
$GLOBALS['BE_MOD']['modal_gallery']['category_room'] = array(
	'tables' => array('tl_category_room'),
	'icon'   => 'system/modules/modal_gallery/assets/icons/slide_category.png',
	'exportLocations' => array('Bcs\Backend\CategoryRoom', 'exportRoomCategories')
);
$GLOBALS['BE_MOD']['modal_gallery']['category_product'] = array(
	'tables' => array('tl_category_product'),
	'icon'   => 'system/modules/modal_gallery/assets/icons/slide_category.png',
	'exportLocations' => array('Bcs\Backend\CategoryProduct', 'exportProductCategories')
);

/* Front end modules */
$GLOBALS['FE_MOD']['modal_gallery']['modal_gallery_module'] 	= 'Bcs\Module\ModalGalleryModule';

/* Models */
$GLOBALS['TL_MODELS']['tl_modal_gallery']			= 'Bcs\Model\ModalGallery';
$GLOBALS['TL_MODELS']['tl_modal_gallery_slide']			= 'Bcs\Model\ModalGallerySlide';
$GLOBALS['TL_MODELS']['tl_category_room']			= 'Bcs\Model\CategoryRoom';
$GLOBALS['TL_MODELS']['tl_category_product']			= 'Bcs\Model\CategoryProduct';
