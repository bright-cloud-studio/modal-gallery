<?php

/***************************************/
/* UPDATE TO BE "MODAL GALLERY SLIDE" */
/***************************************/

/**
 * Bright Cloud Studio's Modal Gallery
 *
 * Copyright (C) 2021 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/modal-gallery
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
**/

/* Table tl_modal_gallery_slide */
$GLOBALS['TL_DCA']['tl_modal_gallery_slide'] = array
(
    // Config
	'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
	'ptable'			=> 'tl_modal_gallery',
        'sql' => array
        (
            'keys' => array
            (
                'id'					=> 	'primary',
                'alias'					=>  'index',
		'pid,published'				=> 'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'panelLayout'             => 'filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('slide_name'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
	    (
		'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['toggle'],
		'icon'                => 'visible.gif',
		'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
		'button_callback'     => array('Bcs\Backend\ModalGalleryBackend', 'toggleIcon')
	    ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{slide_legend},slide_name,hotspot_icon,slide_image,slide_image_size,slide_image_meta;{hotspot_legend},hotspot_links;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
		'sql'                     	=> "int(10) unsigned NOT NULL auto_increment"
        ),
	'pid' => array
	    (
		    'foreignKey'		=> 'tl_modal_gallery.title',
		    'sql'			=> "int(10) unsigned NOT NULL default 0",
		    'relation'			=> array('type'=>'belongsTo', 'load'=>'lazy')
	    ),
        'tstamp' => array
        (
		'sql'                     	=> "int(10) unsigned NOT NULL default '0'"
        ),
	'sorting' => array
	(
		'sql'                    	=> "int(10) unsigned NOT NULL default '0'"
	),
	'alias' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['alias'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'search'                  => true,
		'eval'                    => array('unique'=>true, 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
		'save_callback' => array
		(
			array('Bcs\Backend\ModalGalleryBackend', 'generateAlias')
		),
		'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"

	),
	'slide_name' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['slide_name'],
		'inputType'               => 'text',
		'default'		 => '',
		'search'                  => true,
		'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'hotspot_icon' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspot_icon'],
		'inputType'               => 'text',
		'default'		 => '',
		'search'                  => true,
		'eval'                    => array('mandatory'=>true, 'useRawRequestData'=>true, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'slide_image' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['slide_image'],
		'inputType'               => 'fileTree',
		'default'		  => '',
		'search'                  => true,
		'eval' => [
			'tl_class' => 'clr',
			'mandatory' => true, 
			'fieldType' => 'radio', 
			'filesOnly' => true, 
			'mandatory' => true
		],
		'sql'                    => ['type' => 'binary', 'length' => 16, 'notnull' => false, 'fixed' => true]
	),
	'slide_image_size' => array
	(
		'label'                 => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['slide_image_size'],
		'exclude'               => true,
		'inputType'             => 'imageSize',
		'options'               => \Contao\System::getImageSizes(),
		'reference'             => &$GLOBALS['TL_LANG']['MSC'],
		'eval'                  => [
			'rgxp'=>'natural',
			'includeBlankOption'=>true,
			'nospace'=>true,
			'helpwizard'=>true,
			'tl_class'=>'long'
		],
		'sql'                   => ['type' => 'string', 'length' => 64, 'default' => '']
	),
	'slide_image_meta' => array
	(
		'label'                 => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['slide_image_meta'],
		'inputType'             => 'metaWizard',
		'options'               => \Contao\System::getImageSizes(),
		'reference'             => &$GLOBALS['TL_LANG']['MSC'],
		'eval'                  => [
			'allowHtml'=>true,
			'nospace'=>true,
			'metaFields'    => array
			(
				'title'           => 'maxlength="255"',
				'alt'             => 'maxlength="255"',
				'link'            => array('attributes'=>'maxlength="255"', 'dcaPicker'=>true),
				'caption'         => array('type'=>'textarea')
			),
			'helpwizard'=>true,
			'tl_class'=>'long',
			'dcaPicker'=>true
		],
		'sql'                   => "blob NULL"
	),
	'hotspot_links' => array
		(
			'label'					=> $GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspot_links'],
			'inputType'				=> 'multiColumnWizard',
			'eval' => array
			(
				'columnFields'			=> array
				(
					'hotspot_x' => array
					(
						'label'					=> $GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspot_x'],
						'inputType'				=> 'text',
						'eval'                   		=> array('mandatory'=>true,'tl_class'=>'w50','columnPos'=>'group1'),
					),
					'hotspot_y' => array
					(
						'label'					=> $GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspot_y'],
						'inputType'				=> 'text',
						'eval'                   		=> array('mandatory'=>true, 'tl_class'=>'w50','columnPos'=>'group1'),
					),
					'hotspot_title' => array
					(
						'label'					=> $GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspot_title'],
						'inputType'				=> 'text',
						'eval'                   		=> array('mandatory'=>true, 'tl_class'=>'long','columnPos'=>'group2'),
					),
					'hotspot_text' => array
					(
						'label'					=> $GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspot_text'],
						'inputType'				=> 'textarea',
						'eval'                	=> array('mandatory'=>true, 'rte'=>'tinyMCE','tl_class'=>'long','columnPos'=>'group2'),
					),
				),
			),
			'sql'					=> "blob NULL",
		),
	'published' => array
	(
		'exclude'                 => true,
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery_slide']['published'],
		'inputType'               => 'checkbox',
		'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
		'sql'                     => "char(1) NOT NULL default ''"
	)		
    )
);
