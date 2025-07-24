<?php

declare(strict_types=1);

namespace Bcs\\Module;

use Contao\\FrontendModule;
use Contao\\FrontendTemplate;
use Contao\\Database;
use Bcs\\Model\\ModalGallerySlide;
use Bcs\\Model\\ModalGalleryModel;

class ModalGalleryModule extends FrontendModule
{
    protected static string $strTemplate = 'modal_gallery_module';

    public function generate(): string
    {
        if (TL_MODE === 'BE') {
            $template = new \\Contao\\BackendTemplate('be_wildcard');
            $template->wildcard = '### MODAL GALLERY ###';
            $template->title    = $this->headline;
            $template->id       = $this->id;
            $template->link     = $this->name;
            $template->href     = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;

            return $template->parse();
        }

        return parent::generate();
    }

    protected function compile(): void
    {
        if (!$this->selectedGallery) {
            $this->Template->empty = 'No slides: select a gallery in module settings.';
            return;
        }

        $gallery = ModalGalleryModel::findByPk((int) $this->selectedGallery);
        if (null === $gallery) {
            $this->Template->empty = 'Gallery not found.';
            return;
        }

        $options = ['order' => 'sorting ASC'];
        $slides  = ModalGallerySlide::findBy(
            ['pid=?', 'published=?'],
            [(int) $gallery->id, '1'],
            $options
        );

        if (null === $slides) {
            $this->Template->empty = 'No published slides in this gallery.';
            return;
        }

        $thumbs            = [];
        $slidesArr         = [];
        $categoriesRoom    = [];
        $categoriesProduct = [];
        $entryId           = 1;

        foreach ($slides as $slide) {
            $data = [
                'id'                => $entryId,
                'slide_image'       => $slide->slide_image,
                'slide_name'        => $slide->slide_name,
                'slide_image_url'   => $slide->slide_image_url,
                'hotspot_links'     => \\StringUtil::deserialize($slide->hotspot_links, true),
                'categories_room'   => \\StringUtil::deserialize($slide->category_room, true),
                'categories_product'=> \\StringUtil::deserialize($slide->category_product, true),
                'size_thumb'        => \\StringUtil::deserialize($gallery->slide_thumb_image_size, true),
                'size_slide'        => \\StringUtil::deserialize($gallery->slide_image_size, true),
                'hotspot_icon'      => $gallery->hotspot_icon,
            ];

            $tplThumb = new FrontendTemplate(
                $this->entry_customItemTpl ?: 'item_slide_thumb'
            );
            $tplThumb->setData($data);
            $thumbs[$entryId] = $tplThumb->parse();

            $tplSlide = new FrontendTemplate(
                $this->entry_customItemTpl ?: 'item_slide'
            );
            $tplSlide->setData($data);
            $slidesArr[$entryId] = $tplSlide->parse();
            $entryId++;
        }

        $db    = Database::getInstance();
        $rowsR = $db->prepare("SELECT * FROM tl_category_room WHERE published=1 ORDER BY sorting")
                   ->execute();
        while ($rowsR->next()) {
            $cat = ['id'=>$rowsR->id,'alias'=>$rowsR->alias,'name'=>$rowsR->name];
            $tplCat = new FrontendTemplate($this->entry_customItemTpl ?: 'item_modal_category');
            $tplCat->setData($cat);
            $categoriesRoom[] = $tplCat->parse();
        }

        $rowsP = $db->prepare("SELECT * FROM tl_category_product WHERE published=1 ORDER BY sorting")
                   ->execute();
        while ($rowsP->next()) {
            $cat = ['id'=>$rowsP->id,'alias'=>$rowsP->alias,'name'=>$rowsP->name];
            $tplCat = new FrontendTemplate($this->entry_customItemTpl ?: 'item_modal_category');
            $tplCat->setData($cat);
            $categoriesProduct[] = $tplCat->parse();
        }

        $this->Template->thumbs             = $thumbs;
        $this->Template->slides             = $slidesArr;
        $this->Template->categories_room    = $categoriesRoom;
        $this->Template->categories_product = $categoriesProduct;
    }
}
