<?php





$GLOBALS['TL_DCA']['tl_content']['palettes']['testimonial_content_element'] = '
    {type_legend},type;
    {testimonial_legend},testimonial_source,testimonial_archive,testimonialId;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonial_source'] = [
    'exclude' => true,
    'inputType' => 'radio',
    //'options_callback' => array('tl_news', 'getSourceOptions'),
    //'reference' => &$GLOBALS['TL_LANG']['tl_news'],
    'eval' => ['submitOnChange' => true],
    'sql' => "varchar(12) NOT NULL default 'default'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonial_archive'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_testimonials_archive.title',
    'eval' => ['chosen' => true, 'mandatory' => false, 'tl_class' => 'w50 wizard'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonialId'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_testimonials.name',
    'eval' => ['chosen' => true, 'mandatory' => false, 'tl_class' => 'w50'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['table' => 'tl_testimonials', 'type' => 'hasOne', 'load' => 'lazy'],
];
