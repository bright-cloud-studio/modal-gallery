<?php

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
        'dataContainer'			=> 'Table',
        'enableVersioning'		=> true,
	'ctable'			=> array('tl_modal_gallery_slide'),
	'switchToEdit'			=> true,
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
			'panelLayout'		=> 'filter;search,limit'
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
			'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['edit'],
			'href'                => 'table=tl_modal_gallery_slide',
			'icon'                => 'edit.svg'
		),
		'editheader' => array
		(
			'label'               => &$GLOBALS['TL_LANG']['tl_modal_gallery']['editheader'],
			'href'                => 'act=edit',
			'icon'                => 'header.svg',
			'button_callback'	=> array('tl_modaL_gallery', 'editHeader')
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
        'default'                     => '{slide_legend},title,modal_gallery_name,hotspot_icon;{publish_legend},published;'
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
	'title' => array
	(
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
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
	'modal_gallery_name' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['modal_gallery_name'],
		'inputType'               => 'text',
		'default'		 => '',
		'search'                  => true,
		'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'hotspot_icon' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_modal_gallery']['hotspot_icon'],
		'inputType'               => 'text',
		'default'		 => '',
		'search'                  => true,
		'eval'                    => array('mandatory'=>true, 'useRawRequestData'=>true, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
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
		return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' . Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
	}
}
