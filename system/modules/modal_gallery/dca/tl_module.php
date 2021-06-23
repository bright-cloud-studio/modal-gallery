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
	'exclude' 			=> true,
	'inputType' 		=> 'select',
	'default'			=> 'name',
	'options' 			=> 
		array(
			'alias'			=> 'Alias', 
			'name'			=> 'Name', 
			'first_name'	=> 'First Name', 
			'last_name'		=> 'Last Name', 
			'title'			=> 'Title', 
			'company'		=> 'Company'
		),
	'eval' 				=> array('tl_class'=>'clr w50'),
	'sql' 				=> "varchar(32) NOT NULL default 'name'"
);
