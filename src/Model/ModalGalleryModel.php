<?php

namespace Bcs\\Model;

use Contao\\Model;

class ModalGalleryModel extends Model
{
    protected static $strTable = 'tl_modal_gallery';

    public static function getGalleryOptions(): array
    {
        $arrOptions = [];
        $objGalleries = static::findAll(['order'=>'title ASC']);
        if (null !== $objGalleries) {
            while ($objGalleries->next()) {
                $arrOptions[$objGalleries->id] = $objGalleries->title;
            }
        }
        return $arrOptions;
    }
}
