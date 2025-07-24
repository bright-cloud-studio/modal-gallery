<?php

/**
 * Bright Cloud Studio's Modal Gallery
 *
 * Copyright (C) 2023 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/modal-gallery
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
**/

 
/* Table tl_slide_category */
$GLOBALS['TL_DCA']['tl_category_room'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'alias' => 'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('name'),
            'flag'                    => 1,
            'panelLayout'             => 'filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('name'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'export' => array
            (
                'label'               => 'Export Room Categories CSV',
                'href'                => 'key=exportRoomCategories',
                'icon'                => 'system/modules/modal_gallery/assets/icons/export.svg'
            ),
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
                'label'               => &$GLOBALS['TL_LANG']['tl_category_room']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_category_room']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_category_room']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
              'label'               => &$GLOBALS['TL_LANG']['tl_category_room']['toggle'],
              'icon'                => 'visible.gif',
              'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
              'button_callback'     => array('Bcs\Backend\CategoryRoom', 'toggleIcon')
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_category_room']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{category_legend},name,alias;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
	
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
	'sorting' => array
	(
		'sql'                     => "int(10) unsigned NOT NULL default '0'"
	),
	'alias' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_category_room']['alias'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'search'                  => true,
		'eval'                    => array('unique'=>true, 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
		'save_callback' => array
		(
			array('Bcs\Backend\CategoryRoom', 'generateAlias')
		),
		'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"

	),
	'name' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_category_room']['name'],
		'inputType'               => 'text',
		'default'				  => '',
		'search'                  => true,
		'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'published' => array
	(
		'exclude'                 => true,
		'label'                   => &$GLOBALS['TL_LANG']['tl_category_room']['published'],
		'inputType'               => 'checkbox',
		'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
		'sql'                     => "char(1) NOT NULL default ''"
	)		
    )
);
