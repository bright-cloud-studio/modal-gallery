<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['modal_gallery'] =
    '{title_legend},name,headline,type;{config_legend},selectedGallery,entry_customItemTpl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['selectedGallery'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['selectedGallery'],
    'inputType' => 'select',
    'options_callback' => ['Bcs\\Model\\ModalGalleryModel', 'getGalleryOptions'],
    'eval'      => ['mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'],
    'sql'       => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['entry_customItemTpl'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['entry_customItemTpl'],
    'inputType' => 'select',
    'options_callback' => ['\\Contao\\Backend', 'getTplSelector'],
    'eval'      => ['tl_class'=>'w50', 'includeBlankOption'=>true],
    'sql'       => "varchar(64) NOT NULL default ''",
];
