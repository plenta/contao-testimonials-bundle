<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Contao\DC_Table;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_testimonials'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_testimonials_archive',
        'enableVersioning' => true,
        'markAsCopy' => 'name',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
        'onsubmit_callback' => [
            ['plenta.testimonials.listener.data_container', 'onPlentaTestimonialsSubmit'],
        ]
    ],

    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'flag' => DataContainer::SORT_INITIAL_LETTERS_DESC,
            'fields' => [
                'sorting',
            ],
            'panelLayout' => 'sort;filter;search;limit',
            'headerFields' => [
                'title',
                'tstamp',
            ],
            'child_record_callback' => ['plenta.testimonials.listener.data_container', 'listTestimonials'],
            'child_record_class' => 'no_padding',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
                'primary' => true,
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
                'primary' => true,
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
                'primary' => true,
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage'],
        'default' => '
            {testimonial_legend},identifier,name,company,department,testimonial,rating,categories;
            {image_legend},addImage;
            {publish_legend},published,publishedAt
        ',
    ],

    'subpalettes' => [
        'addImage' => 'singleSRC',
    ],

    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_testimonials_archive.title',
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'sorting' => [
            'sorting' => true,
            'sql' => ['type' => 'integer', 'notnull' => true, 'unsigned' => true, 'default' => 0]
        ],
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'identifier' => [
            'exclude' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'name' => [
            'exclude' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'company' => [
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'search' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'clr w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'department' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'testimonial' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['mandatory' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'rating' => [
            'inputType' => 'select',
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'default' => 0,
            'options' => [0, 1, 2, 3, 4, 5],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '0'",
        ],
        'addImage' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'singleSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => [
                'filesOnly' => true,
                'fieldType' => 'radio',
                'mandatory' => true,
                'tl_class' => 'clr',
                'extensions' => Contao\Config::get('validImageTypes'),
            ],
            'sql' => 'binary(16) NULL',
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'toggle' => true,
            'flag' => 1,
            'inputType' => 'checkbox',
            'eval' => [
                'doNotCopy' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'publishedAt' => [
            'inputType' => 'text',
            'exclude' => true,
            'eval' => [
                'doNotCopy' => true,
                'rgxp' => 'datim',
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => [
                'type' => 'string',
                'notnull' => true,
                'default' => '',
                'platformOptions' => [
                    'collation' => 'ascii_bin',
                ],
                'length' => 10,
            ]
        ],
        'categories' => [
            'exclude' => true,
            'inputType' => 'checkboxWizard',
            'foreignKey' => 'tl_testimonials_category.title',
            'eval' => ['multiple' => true, 'tl_class' => 'clr'],
            'sql' => 'mediumtext NULL',
        ],
    ],
];
