<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Plenta\ContaoTestimonialsBundle\Helper\Testimonial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestimonialFrontendModuleController extends AbstractFrontendModuleController
{
    public const TYPE = 'plenta_testimonials';

    private Testimonial $testimonial;

    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $items = [];

        $testimonials = $this->testimonial->getTestimonialsByArchive(
            (int) $model->plenta_testimonials_archive,
            (int) $model->plenta_testimonials_limit,
            (bool) $model->plenta_testimonials_random
        );

        if (null !== $testimonials) {
            foreach ($testimonials as $testimonial) {
                $items[] = [
                    'name' => $testimonial['name'],
                    'company' => $testimonial['company'],
                    'department' => $testimonial['department'],
                    'testimonial' => $testimonial['testimonial'],
                ];
            }
        }

        $template->items = $items;

        return $template->getResponse();
    }
}
