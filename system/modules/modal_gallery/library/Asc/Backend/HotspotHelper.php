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

use Contao\DataContainer;
use Contao\Image;

class HotspotHelper
{
    public function onSelectHotspotImage(DataContainer $dc)
    {
        echo "Hotspot X: ";
        echo "Hotspot Y: ";
        return '<a href="#">something</a>';
    }
}
