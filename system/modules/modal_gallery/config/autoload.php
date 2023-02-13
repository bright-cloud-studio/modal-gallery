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

/* Register the classes */
ClassLoader::addClasses(array
(
	'Bcs\Module\ModalGalleryModule' 	=> 'system/modules/modal_gallery/library/Bcs/Module/ModalGalleryModule.php',
	'Bcs\Model\ModalGallery' 		=> 'system/modules/modal_gallery/library/Bcs/Model/ModalGallery.php',
	'Bcs\Model\ModalGallerySlide' 		=> 'system/modules/modal_gallery/library/Bcs/Model/ModalGallerySlide.php',
	'Bcs\Model\SlideCategory' 		=> 'system/modules/modal_gallery/library/Bcs/Model/SlideCategory.php',
	'Bcs\Backend\HotspotHelper' 		=> 'system/modules/modal_gallery/library/Bcs/Backend/HotspotHelper.php'
));

/* Register the templates */
TemplateLoader::addFiles(array
(
	'modal_gallery_module' 			=> 'system/modules/modal_gallery/templates/modules',
	'item_slide' 				=> 'system/modules/modal_gallery/templates/items',
	'picture_default_modal_thumb' 		=> 'system/modules/modal_gallery/templates/items'
));
