<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\EventListener\DataContainer;

use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

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

        if (null === $element || 'plenta_testimonial' !== $element->type) {
            return;
        }

        if ('random' === $element->testimonial_source) {
            PaletteManipulator::create()
                ->removeField('testimonialId', 'testimonial_legend')
                ->applyToPalette('plenta_testimonial', 'tl_content')
            ;
        }

        if ('single' === $element->testimonial_source) {
            PaletteManipulator::create()
                ->removeField('testimonial_archive', 'testimonial_legend')
                ->applyToPalette('plenta_testimonial', 'tl_content')
            ;
        }
    }
}
