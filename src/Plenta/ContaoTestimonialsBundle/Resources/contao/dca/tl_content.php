<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoTestimonialsBundle\Controller\ContentElement\TestimonialContentElementController;

$GLOBALS['TL_DCA']['tl_content']['palettes'][TestimonialContentElementController::TYPE] = '
    {type_legend},type,headline;
    {testimonial_legend},testimonial_source,testimonial_archive,testimonialId;
    {image_legend},size,floating;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonial_source'] = [
    'exclude' => true,
    'inputType' => 'radio',
    'default' => 'random',
    'options' => ['random', 'single'],
    'reference' => &$GLOBALS['TL_LANG']['TESTIMONIAL'],
    'eval' => ['submitOnChange' => true],
    'sql' => "varchar(12) NOT NULL default 'default'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonial_archive'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_testimonials_archive.title',
    'eval' => ['chosen' => true, 'mandatory' => false, 'tl_class' => 'w50 wizard'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['testimonialId'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['plenta.testimonials.listener.data_container', 'onTestimonialsOptionsCallback'],
    'eval' => ['chosen' => true, 'mandatory' => false, 'tl_class' => 'w50'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
    'relation' => ['table' => 'tl_testimonials', 'type' => 'hasOne', 'load' => 'lazy'],
];
