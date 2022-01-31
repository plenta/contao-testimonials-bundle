<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoTestimonialsBundle\Controller\FrontendModule\TestimonialFrontendModuleController;

$GLOBALS['TL_DCA']['tl_module']['palettes'][TestimonialFrontendModuleController::TYPE] =
    '{title_legend},name,headline,type;
    {config_legend},plenta_testimonials_archive,plenta_testimonials_random,plenta_testimonials_limit;
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
        'tl_class' => 'w50 wizard'
    ],
    'sql' => 'int(10) unsigned NOT NULL default 0',
    'relation' => [
        'type' => 'hasOne',
        'load' => 'lazy'
    ],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_random'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w50 m12',
    ],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plenta_testimonials_limit'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
        'maxlength' => 5,
        'rgxp' => 'natural',
        'tl_class' => 'w50',
    ],
    'sql' => 'smallint(5) unsigned NOT NULL default 0',
];

