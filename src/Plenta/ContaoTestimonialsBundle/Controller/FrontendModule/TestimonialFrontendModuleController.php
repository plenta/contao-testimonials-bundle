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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestimonialFrontendModuleController extends AbstractFrontendModuleController
{
    public const TYPE = 'plenta_testimonials';

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $items = [];

        $items[] = [
            'name' => 'bbbb'
        ];

        //@Todo Items mit Daten füttern.

        $template->items = $items;

        return $template->getResponse();
    }
}
