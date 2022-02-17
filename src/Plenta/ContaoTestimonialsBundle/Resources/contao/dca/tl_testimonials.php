<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_testimonials'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_testimonials_archive',
        'enableVersioning' => true,
        'markAsCopy' => 'name',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['identifier'],
            'panelLayout' => 'filter;limit',
            'headerFields' => ['title', 'tstamp'],
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
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'icon'                => 'invisible.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => ['tl_testimonials', 'toggleIcon']
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage'],
        'default' => '{testimonial_legend},identifier,name,company,department,testimonial,rating;{image_legend},addImage;{publish_legend},published',
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
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'identifier' => [
            'exclude' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'name' => [
            'exclude' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'company' => [
            'exclude' => true,
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
            'options' => [1,2,3,4,5],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default ''",
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
            'flag' => 1,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];

class tl_testimonials extends \Contao\Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('Contao\BackendUser', 'User');
    }

    /**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (Contao\Input::get('cid'))
        {
            $this->toggleVisibility(Contao\Input::get('cid'), (Contao\Input::get('state') == 1), (func_num_args() <= 12 ? null : func_get_arg(12)));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the cid, so hacking attempts are logged
        if (!$this->User->hasAccess('tl_testimonials::published', 'alexf'))
        {
            return '';
        }

        // Disable the button if the element type is not allowed
        if (!$this->User->hasAccess($row['type'], 'elements'))
        {
            return Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
        }

        $href .= '&amp;id=' . Contao\Input::get('id') . '&amp;cid=' . $row['id'] . '&amp;state=' . $row['published'];

        if ($row['published'])
        {
            $icon = 'visible.svg';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . Contao\StringUtil::specialchars($title) . '" data-tid="cid"' . $attributes . '>' . Contao\Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"') . '</a> ';
    }

    /**
     * Toggle the visibility of an element
     *
     * @param integer              $intId
     * @param boolean              $blnVisible
     * @param Contao\DataContainer $dc
     *
     * @throws Contao\CoreBundle\Exception\AccessDeniedException
     */
    public function toggleVisibility($intId, $blnVisible, Contao\DataContainer $dc=null)
    {
        // Set the ID and action
        Contao\Input::setGet('id', $intId);
        Contao\Input::setGet('act', 'toggle');

        if ($dc)
        {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (is_array($GLOBALS['TL_DCA']['tl_testimonials']['config']['onload_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_testimonials']['config']['onload_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$this->User->hasAccess('tl_testimonials::published', 'alexf'))
        {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to show/hide content element ID ' . $intId . '.');
        }

        $objRow = $this->Database->prepare("SELECT * FROM tl_testimonials WHERE id=?")
            ->limit(1)
            ->execute($intId);

        if ($objRow->numRows < 1)
        {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Invalid testimonial ID ' . $intId . '.');
        }

        // Set the current record
        if ($dc)
        {
            $dc->activeRecord = $objRow;
        }

        $objVersions = new Contao\Versions('tl_testimonials', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_testimonials']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_testimonials']['fields']['published']['save_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                }
                elseif (is_callable($callback))
                {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE tl_testimonials SET tstamp=$time, published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
            ->execute($intId);

        if ($dc)
        {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (is_array($GLOBALS['TL_DCA']['tl_testimonials']['config']['onsubmit_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_testimonials']['config']['onsubmit_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();

        if ($dc)
        {
            $dc->invalidateCacheTags();
        }
    }
}