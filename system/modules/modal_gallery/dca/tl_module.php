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

/* Add a palette to tl_module */
$GLOBALS['TL_DCA']['tl_module']['palettes']['modal_gallery_module'] 		= '{title_legend},name,headline,type;{select_gallery_legend},selectedGallery;{template_legend:hide},customTpl;{expert_legend:hide},guests,cssID,space';

// Sort Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['selectedGallery'] = array
(
	'label' 			=> &$GLOBALS['TL_LANG']['tl_module']['selectedGallery'],
	'inputType' 			=> 'select',
	'options_callback'		  => array('Bcs\Backend\ModalGalleryBackend', 'getGalleries'),
	'eval' 				=> array('tl_class'=>'clr w50'),
	'sql' 				=> "varchar(255) NOT NULL default ''"
);
