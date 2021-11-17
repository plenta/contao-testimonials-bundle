<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['testimonial_content_element'] = '
    {type_legend},type;
    {text_legend),testimonial_archive;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonial_archive'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_testimonials_archive.title',
    'eval' => ['chosen'=>true, 'mandatory' => true, 'tl_class'=>'w50 wizard'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['type'=>'hasOne', 'load'=>'lazy'],
];
