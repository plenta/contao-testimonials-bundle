<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Plenta\ContaoTestimonialsBundle\Helper\Testimonial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: self::TYPE, category: 'miscellaneous')]
class TestimonialContentElementController extends AbstractContentElementController
{
    public const TYPE = 'plenta_testimonial';

    public function __construct(protected Testimonial $testimonial)
    {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        if ('single' === (string) $model->testimonial_source) {
            $testimonial = $this->testimonial->getTestimonialById((int) $model->testimonialId);
        } else {
            $testimonial = $this->testimonial->getTestimonialsByArchive(
                (int) $model->testimonial_archive,
                1,
                true,
                $model->plenta_testimonials_categories
            )[0] ?? null;
        }

        if (null !== $testimonial) {
            $template->name = $testimonial->name;
            $template->company = $testimonial->company;
            $template->department = $testimonial->department;
            $template->testimonial = $testimonial->testimonial;
            $template->rating = $this->testimonial->getRating((int) $testimonial->rating);
        }

        $template->addImage = false;

        if (true === (bool) $model->testimonial_addImages && $testimonial->addImage && $testimonial->singleSRC) {
            $template->addImage = true;
            $template->singleSRC = $testimonial->singleSRC;
        }

        $template->model = $testimonial;

        $template->addRating = $model->testimonial_addRatings;

        return $template->getResponse();
    }
}
