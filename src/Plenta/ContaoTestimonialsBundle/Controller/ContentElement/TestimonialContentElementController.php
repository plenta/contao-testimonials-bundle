<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Connection;

/**
* @ContentElement(category="texts")
*/
class TestimonialContentElementController extends AbstractContentElementController
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $testimonial = $this->getRandomTestimonialByArchive((int) $model->testimonial_archive);

        if (null !== $testimonial) {
            $template->name = $testimonial['name'];
            $template->company = $testimonial['company'];
            $template->department = $testimonial['department'];
            $template->testimonial = $testimonial['testimonial'];
        }

        return $template->getResponse();
    }

    protected function getRandomTestimonialByArchive(int $pid): ?array
    {
        $records = $this->connection->fetchAllAssociative(
            'SELECT name, company, department, testimonial FROM tl_testimonials WHERE pid=? ORDER BY RAND() LIMIT 1',
            [$pid]
        );

        if (is_array($records)) {
            return $records[0];
        }

        return null;
    }
}
