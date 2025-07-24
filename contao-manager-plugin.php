<?php

use Contao\\ManagerPlugin\\Bundle\\Config\\BundleConfig;
use Contao\\ManagerPlugin\\Bundle\\BundlePluginInterface;
use Bcs\\ModalGalleryBundle;
use Contao\\CoreBundle\\ContaoCoreBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(\\Contao\\ManagerPlugin\\Bundle\\BundleConfig $config): array
    {
        return [
            BundleConfig::create(ModalGalleryBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}