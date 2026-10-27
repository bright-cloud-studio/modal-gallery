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
 
namespace Bcs\Backend;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;

use Bcs\Model\ModalGallery;
use Bcs\Model\ModalGallerySlide;
use Contao\Database;

class ModalGallerySlideBackend extends Backend
{
	
	public function cutPage($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->hasAccess($row['type'], 'alpty') && $this->User->isAllowed(BackendUser::CAN_EDIT_PAGE_HIERARCHY, $row)) ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
	}
	public function pastePage(DataContainer $dc, $row, $table, $cr, $arrClipboard=null)
	{
		$disablePA = false;
		$disablePI = false;

		// Disable all buttons if there is a circular reference
		if ($arrClipboard !== false && (($arrClipboard['mode'] == 'cut' && ($cr == 1 || $arrClipboard['id'] == $row['id'])) || ($arrClipboard['mode'] == 'cutAll' && ($cr == 1 || in_array($row['id'], $arrClipboard['id'])))))
		{
			$disablePA = true;
			$disablePI = true;
		}

		// Prevent adding non-root pages on top-level
		if (empty($row['pid']) && Input::get('mode') != 'create')
		{
			$objPage = Database::getInstance()->prepare("SELECT * FROM " . $table . " WHERE id=?")
									  ->limit(1)
									  ->execute(Input::get('id'));

			if ($objPage->type != 'root')
			{
				$disablePA = true;

				if ($row['id'] == 0)
				{
					$disablePI = true;
				}
			}
		}

		// Check permissions if the user is not an administrator
		if (!$this->User->isAdmin)
		{
			// Disable "paste into" button if there is no permission 2 (move) or 1 (create) for the current page
			if (!$disablePI)
			{
				if (!$this->User->isAllowed(BackendUser::CAN_EDIT_PAGE_HIERARCHY, $row) || (Input::get('mode') == 'create' && !$this->User->isAllowed(BackendUser::CAN_EDIT_PAGE, $row)))
				{
					$disablePI = true;
				}
			}

			// Disable "paste after" button if there is no permission 2 (move) or 1 (create) for the parent page
			if (!$disablePA)
			{
				/** @var PageModel $objModel */
				$objModel = Model::getClassFromTable($table);

				if (($objPage = $objModel::findById($row['pid'])) !== null && (!$this->User->isAllowed(BackendUser::CAN_EDIT_PAGE_HIERARCHY, $objPage->row()) || (Input::get('mode') == 'create' && !$this->User->isAllowed(BackendUser::CAN_EDIT_PAGE, $objPage->row()))))
				{
					$disablePA = true;
				}
			}

			// Disable "paste after" button if the parent page is a root page and the user is not an administrator
			if (!$disablePA && ($row['pid'] < 1 || in_array($row['id'], $dc->rootIds)))
			{
				$disablePA = true;
			}
		}

		$return = '';

		// Return the buttons
		$imagePasteAfter = Image::getHtml('pasteafter.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id']));
		$imagePasteInto = Image::getHtml('pasteinto.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][1], $row['id']));

		if ($row['id'] > 0)
		{
			$return = $disablePA ? Image::getHtml('pasteafter_.svg') . ' ' : '<a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=1&amp;pid=' . $row['id'] . (!is_array($arrClipboard['id']) ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteAfter . '</a> ';
		}

		return $return . ($disablePI ? Image::getHtml('pasteinto_.svg') . ' ' : '<a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=2&amp;pid=' . $row['id'] . (!is_array($arrClipboard['id']) ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][$row['id'] > 0 ? 1 : 0], $row['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteInto . '</a> ');
	}

    
	public function listSlides($row)
	{
		// Show a small preview of the slide image
		$thumb = '';
		if ($row['slide_image'])
		{
			$figure = System::getContainer()->get('contao.image.studio')
				->createFigureBuilder()
				->fromUuid($row['slide_image'])
				->setSize(array(80, 50, 'crop'))
				->buildIfResourceExists();

			if ($figure !== null)
			{
				$thumb = '<img src="' . $figure->getImage()->getImageSrc() . '" width="80" height="50" alt="">';
			}
		}

		// Count the hotspots that have been given a title
		$hotspots = count(array_filter(StringUtil::deserialize($row['hotspot_links'], true), static fn ($hotspot) => trim((string) ($hotspot['hotspot_title'] ?? '')) !== ''));

		// Flag slides that couldn't be published as they are
		$missing = $this->getMissingForPublish($row);
		$badge = empty($missing) ? '' : ' <span class="mg_incomplete" title="' . StringUtil::specialchars(implode(', ', $missing)) . '">' . $GLOBALS['TL_LANG']['tl_modal_gallery_slide']['incomplete'] . '</span>';

		return '<div class="tl_content_left mg_slide_row">' . $thumb . '<span><strong>' . StringUtil::specialchars($row['slide_name']) . '</strong>' . $badge . '<br><span class="tl_gray">' . sprintf($GLOBALS['TL_LANG']['tl_modal_gallery_slide']['hotspotCount'], $hotspots) . "</span></span></div>\n";
	}

	/**
	 * Save callback for "published": slides can be saved as drafts, but only published once complete.
	 *
	 * Flow:
	 * 1. Always allow unpublishing
	 * 2. In the edit form, check the submitted values rather than the stored ones
	 * 3. If anything is missing, show the error on the field (edit form) or as a message (list toggle)
	 */
	public function checkPublish($varValue, DataContainer $dc)
	{
		// Unpublishing is always allowed
		if (!$varValue)
		{
			return $varValue;
		}

		$row = $dc->getCurrentRecord();

		// The edit form saves every field in one request, so the stored record is still the old one
		if (Input::get('act') == 'edit')
		{
			foreach (array('slide_image', 'caption_title', 'category_room', 'category_product', 'hotspot_links') as $field)
			{
				if (Input::post($field) !== null)
				{
					$row[$field] = Input::post($field);
				}
			}
		}

		// Return if the slide is complete
		$missing = $this->getMissingForPublish($row);
		if (empty($missing))
		{
			return $varValue;
		}

		$message = sprintf($GLOBALS['TL_LANG']['tl_modal_gallery_slide']['publishMissing'], implode(', ', $missing));

		// Core's list toggle doesn't catch exceptions, so report it there and leave the slide unpublished
		if (Input::get('act') == 'toggle')
		{
			Message::addError($message);
			return '';
		}

		throw new \Exception($message);
	}

	/**
	 * Return the labels of everything a slide still needs before it can be published.
	 */
	public function getMissingForPublish(array $row): array
	{
		$labels = $GLOBALS['TL_LANG']['tl_modal_gallery_slide'];
		$missing = array();

		if (!$row['slide_image'])
		{
			$missing[] = $labels['slide_image'][0];
		}

		// Only the Accordion and Comparison layouts show the caption title
		$gallery = ModalGallery::findByPk($row['pid']);
		if ($gallery !== null && in_array($gallery->template_style, array('accordion', 'comparison')) && trim((string) $row['caption_title']) === '')
		{
			$missing[] = $labels['caption_title'][0];
		}

		// The front end filters need at least one category of each type
		if (!array_filter(StringUtil::deserialize($row['category_room'], true)))
		{
			$missing[] = $labels['category_room'][0];
		}
		if (!array_filter(StringUtil::deserialize($row['category_product'], true)))
		{
			$missing[] = $labels['category_product'][0];
		}

		// Need at least one hotspot, and every row filled in so none render empty on the front end
		$hotspots = StringUtil::deserialize($row['hotspot_links'], true);
		$complete = !empty($hotspots);
		foreach ($hotspots as $hotspot)
		{
			foreach (array('hotspot_x', 'hotspot_y', 'hotspot_title', 'hotspot_text') as $key)
			{
				if (trim(strip_tags((string) ($hotspot[$key] ?? ''))) === '')
				{
					$complete = false;
				}
			}
		}
		if (!$complete)
		{
			$missing[] = $labels['hotspot_links'][0];
		}

		return $missing;
	}
	
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;
		
		// Generate an alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(StringUtil::restoreBasicEntities($dc->activeRecord->name));
		}

		$objAlias = Database::getInstance()->prepare("SELECT id FROM tl_modal_gallery_slide WHERE id=? OR alias=?")
								   ->execute($dc->id, $varValue);

		// Check whether the page alias exists
		if ($objAlias->numRows > 1)
		{
			if (!$autoAlias)
			{
				throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
			}

			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}
	
	public function getRoomCategories(DataContainer $dc) { 
		$cats = array();
		
		$result = Database::getInstance()->prepare("SELECT * FROM tl_category_room WHERE published=1")->execute();
		while($result->next())
		{
			$cats = $cats + array($result->alias => $result->name);
		}

		return $cats;
	}
	public function getProductCategories(DataContainer $dc) { 
		$cats = array();
		
		$result = Database::getInstance()->prepare("SELECT * FROM tl_category_product WHERE published=1")->execute();
		while($result->next())
		{
			$cats = $cats + array($result->alias => $result->name);
		}

		return $cats;
	}
}
