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

use Contao\Template;
use Contao\ContentModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;

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
        if ('single' === (string) $model->testimonial_source) {
            $testimonial = $this->getTestimonialById((int) $model->testimonialId);
        } else {
            $testimonial = $this->getRandomTestimonialByArchive((int) $model->testimonial_archive);
        }

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
        $testimonial = $this->connection->fetchAllAssociative(
            'SELECT name, company, department, testimonial FROM tl_testimonials WHERE pid=? ORDER BY RAND() LIMIT 1',
            [$pid]
        );

        if (is_array($testimonial)) {
            return $testimonial[0];
        }

        return null;
    }

    protected function getTestimonialById(int $id): ?array
    {
        $testimonial = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial')
            ->from('tl_testimonials')
            ->where('id=:id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch()
            ;

        if (false !== $testimonial) {
            return $testimonial;
        }

        return null;
    }
}
