<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoTestimonialsBundle\Controller\FrontendModule\TestimonialFrontendModuleController;
use Plenta\ContaoTestimonialsBundle\Enum\SortingOption;

$GLOBALS['TL_DCA']['tl_module']['palettes'][TestimonialFrontendModuleController::TYPE] =
    '{title_legend},name,headline,type;
    {config_legend},plenta_testimonials_archive,plenta_testimonials_random,plenta_testimonials_sorting,plenta_testimonials_limit,plenta_testimonials_categories,imgSize,
    plenta_testimonials_addImages,plenta_testimonials_addRatings;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID'
;

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_archive'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_testimonials_archive.title',
    'eval' => [
        'chosen' => true,
        'mandatory' => true,
        'tl_class' => 'w50 wizard',
    ],
    'sql' => 'int(10) unsigned NOT NULL default 0',
    'relation' => [
        'type' => 'hasOne',
        'load' => 'lazy',
    ],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_random'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w50 cbx m12',
    ],
    'sql' => "char(1) NOT NULL default ''",
];


$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_sorting'] = [
    'exclude' => true,
    'inputType' => 'select',
    'default' => SortingOption::getDefault()->name,
    'eval' => [
        'chosen' => true,
        'mandatory' => true,
        'tl_class' => 'w50 wizard',
    ],
    'options_callback' => ['plenta.testimonials.listener.data_container', 'onPlentaTestimonialsSortingOption'],
    'sql' => ['type' => 'string', 'length' => 32, 'default' => SortingOption::getDefault()->name],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_limit'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
        'maxlength' => 5,
        'rgxp' => 'natural',
        'tl_class' => 'clr w50',
    ],
    'sql' => 'smallint(5) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_categories'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'foreignKey' => 'tl_testimonials_category.title',
    'eval' => [
        'multiple' => true,
        'tl_class' => 'clr',
    ],
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_addImages'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'clr w50 m12',
    ],
    'sql' => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_addRatings'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w50 m12',
    ],
    'sql' => "char(1) NOT NULL default '1'",
];
