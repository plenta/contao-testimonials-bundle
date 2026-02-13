<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\ModuleModel;
use Plenta\ContaoTestimonialsBundle\Enum\SortingOption;
use Plenta\ContaoTestimonialsBundle\Helper\Testimonial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\Twig\FragmentTemplate;

#[AsFrontendModule(type: self::TYPE, category: 'miscellaneous')]
class TestimonialFrontendModuleController extends AbstractFrontendModuleController
{
    public const TYPE = 'plenta_testimonials';


    public function __construct(
        private readonly Testimonial $testimonial
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $items = [];

        $testimonials = $this->testimonial->getTestimonialsByArchive(
            (int) $model->plenta_testimonials_archive,
            (int) $model->plenta_testimonials_limit,
            $model->plenta_testimonials_sorting,
            $model->plenta_testimonials_categories
        );

        $this->tagResponse($testimonials);

        if (null !== $testimonials) {
            foreach ($testimonials as $testimonial) {
                $testimonialImage = null;

                if (true === (bool) $model->plenta_testimonials_addImages && true === (bool) $testimonial->addImage) {
                    $testimonialImage = $testimonial->singleSRC;
                }

                $items[] = [
                    'name' => $testimonial->name,
                    'company' => $testimonial->company,
                    'department' => $testimonial->department,
                    'testimonial' => $testimonial->testimonial,
                    'rating' => $this->testimonial->getRating((int) $testimonial->rating),
                    'image' => $testimonialImage,
                    'model' => $testimonial,
                ];
            }
        }

        $template->set('imgSize', $model->imgSize);
        $template->set('addRatings', $model->plenta_testimonials_addRatings);
        $template->set('items', $items);

        $response = $template->getResponse();

        if (SortingOption::random->name === $model->plenta_testimonials_sorting) {
            $response->setMaxAge(time());
        }

        return $response;
    }
}
