<?php

use Contao\Backend;
use Contao\BackendUser;
use Contao\DC_Table;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;
use Bcs\ModalGallerySlide;

/**************************************************/
/* UPDATE TO BE "MODAL GALLERY" AKA THE TOP LEVEL */
/**************************************************/


/**
 * Bright Cloud Studio's Modal Gallery
 *
 * Copyright (C) 2021 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/modal-gallery
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
**/

/* Table tl_modal_gallery */
$GLOBALS['TL_DCA']['tl_modal_gallery'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'			=> DC_Table::class,
		'enableVersioning'		=> true,
		'ctable'			=> array('tl_modal_gallery_slide'),
		'switchToEdit'			=> true,
		'markAsCopy'			=> 'title',
		'sql' => array
		(
			'keys' => array
			(
				'id' 		=> 	'primary',
				'alias' 	=>	'index'
			)
		)
	),
 
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'			=> 1,
			'fields'		=> array('title'),
			'flag'			=> 1,
			'panelLayout'		=> 'filter;search,limit'
        ),
		'label' => array
		(
		    'fields'                  => array('title'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['edit'],
				'href'                => 'table=tl_modal_gallery_slide',
				'icon'                => 'edit.svg'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['editheader'],
				'href'                => 'table=tl_modal_gallery&amp;act=edit',
				'icon'                => 'header.svg'
			),
		    'copy' => array
		    (
    			'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['copy'],
    			'href'                => 'act=copy',
    			'icon'                => 'copy.gif'
		    ),
		    'delete' => array
		    (
    			'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['delete'],
    			'href'                => 'act=delete',
    			'icon'                => 'delete.gif',
    			'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
		    ),
		    'toggle' => array
		    (
    			'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['toggle'],
    			'icon'                => 'visible.gif',
    			'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
    			'button_callback'     => array('Bcs\Backend\ModalGalleryBackend', 'toggleIcon')
		    ),
		    'show' => array
		    (
    			'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['show'],
    			'href'                => 'act=show',
    			'icon'                => 'show.gif'
		    )
		)
    	),
 
    // Palettes
    'palettes' => array
    (
        '__selector__'                => array('template_style'),
        'default'                     => '{slide_legend},title,hotspot_icon,slide_image_size,slide_thumb_image_size;{style_legend},template_style;{publish_legend},published;'
    ),
    // Subpalettes
    'subpalettes' => array
    (
        'template_style_accordion'    => '{accordion_legend},accordion_active_percentage;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
		    'sql'                     	=> "int(10) unsigned NOT NULL auto_increment"
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
    		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['alias'],
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
    	'title' => array
    	(
    		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['modal_gallery_title'],
    		'inputType'               => 'text',
    		'default'                 => '',
    		'search'                  => true,
    		'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    		'sql'                     => "varchar(255) NOT NULL default ''"
    	),
    	'hotspot_icon' => array
    	(
    		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['modal_gallery_hotspot_icon'],
    		'inputType'               => 'text',
    		'default'                 => '',
    		'search'                  => true,
    		'eval'                    => array('mandatory'=>true, 'useRawRequestData'=>true, 'tl_class'=>'w50'),
    		'sql'                     => "varchar(255) NOT NULL default ''"
    	),
    	'slide_image_size' => array
    	(
    		'label'                 => &$GLOBALS['TL_LANG']['tl_modal_gallery']['slide_image_size'],
    		'exclude'               => true,
    		'inputType'             => 'imageSize',
    		'options_callback' => static function () {
                return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
            },
    		'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    		'eval'                  => [
    			'rgxp'=>'natural',
    			'includeBlankOption'=>true,
    			'nospace'=>true,
    			'helpwizard'=>true,
    			'tl_class'=>'clr long'
    		],
    		'sql'                   => ['type' => 'string', 'length' => 64, 'default' => '']
    	),
    	'slide_thumb_image_size' => array
    	(
    		'label'                 => &$GLOBALS['TL_LANG']['tl_modal_gallery']['slide_thumb_image_size'],
    		'exclude'               => true,
    		'inputType'             => 'imageSize',
    		'options_callback' => static function () {
                return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
            },
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


        'template_style' => array
    	(
    		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['slider_style'],
            'inputType'               => 'select',
            'options'   => array(
                'vertical'   => 'Vertical',
                'horizontal' => 'Horizontal',
                'accordion'  => 'Accordion',
                'comparison' => 'Comparison'
            ),
            'eval'                    => array('mandatory'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50', 'includeBlankOption'=>false),
            'sql'                     => array('type'=>'string', 'length'=>255, 'default'=>'vertical')
    	),
        'accordion_active_percentage' => array
    	(
    		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['accordion_active_percentage'],
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>100, 'tl_class'=>'w50 clr'),
            'sql'                     => array('type'=>'string', 'length'=>100, 'default'=>'80')
    	),

        
    	'published' => array
    	(
    		'exclude'                 => true,
    		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['published'],
    		'inputType'               => 'checkbox',
    		'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
    		'sql'                     => "char(1) NOT NULL default ''"
    	)		
    )
);


class tl_modal_gallery extends Backend
{
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a>';
	}
}
