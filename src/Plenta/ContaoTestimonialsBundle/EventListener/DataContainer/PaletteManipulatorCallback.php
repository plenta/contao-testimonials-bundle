<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\EventListener\DataContainer;

use Contao\ContentModel;
use Contao\DataContainer;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Symfony\Component\HttpFoundation\RequestStack;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * @Callback(table="tl_content", target="config.onload")
 */
class PaletteManipulatorCallback
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(DataContainer $dc = null): void
    {
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $element = ContentModel::findById($dc->id);

        if (null === $element || 'testimonial_content_element' !== $element->type) {
            return;
        }

        if ('random' === $element->testimonial_source) {
            PaletteManipulator::create()
                ->removeField('testimonialId', 'testimonial_legend')
                ->applyToPalette('testimonial_content_element', 'tl_content')
            ;
        }

        if ('single' === $element->testimonial_source) {
            PaletteManipulator::create()
                ->removeField('testimonial_archive', 'testimonial_legend')
                ->applyToPalette('testimonial_content_element', 'tl_content')
            ;
        }
    }
}