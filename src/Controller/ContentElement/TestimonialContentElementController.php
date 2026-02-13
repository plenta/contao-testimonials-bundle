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
use Contao\CoreBundle\Twig\FragmentTemplate;
use Plenta\ContaoTestimonialsBundle\Helper\Testimonial;
use Plenta\ContaoTestimonialsBundle\Enum\SortingOption;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: self::TYPE, category: 'miscellaneous')]
class TestimonialContentElementController extends AbstractContentElementController
{
    public const TYPE = 'plenta_testimonial';

    public function __construct(
        protected Testimonial $testimonial
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ('single' === (string) $model->testimonial_source) {
            $testimonial = $this->testimonial->getTestimonialById((int) $model->testimonialId);
            $this->tagResponse($testimonial);
        } else {
            $testimonial = $this->testimonial->getTestimonialsByArchive(
                (int) $model->testimonial_archive,
                1,
                SortingOption::random->name,
                $model->plenta_testimonials_categories
            )[0] ?? null;
        }

        if (null !== $testimonial) {
            $template->set('name', $testimonial->name);
            $template->set('company', $testimonial->company);
            $template->set('department', $testimonial->department);
            $template->set('testimonial', $testimonial->testimonial);
            $template->set('rating', $this->testimonial->getRating((int) $testimonial->rating));
        }

        $template->set('addImage', false);

        if (true === (bool) $model->testimonial_addImages && $testimonial->addImage && $testimonial->singleSRC) {
            $template->set('addImage', true);
            $template->set('singleSRC', $testimonial->singleSRC);
        }

        $template->set('model', $testimonial);
        $template->set('size', $model->size);
        $template->set('addRating', $model->testimonial_addRatings);

        $response = $template->getResponse();

        if ('random' === (string) $model->testimonial_source) {
            $response->setMaxAge(time());
        }

        return $response;
    }
}
