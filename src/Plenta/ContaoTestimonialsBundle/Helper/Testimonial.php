<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Helper;

use Doctrine\DBAL\Connection;

class Testimonial
{
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getTestimonialById(int $id): ?array
    {
        $testimonial = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial, addImage, singleSRC')
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

    public function getTestimonialsByArchive(int $pid, int $limit, bool $random = false): ?array
    {
        $testimonials = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial, addImage, singleSRC')
            ->from('tl_testimonials')
            ->where('pid=:pid')
            ->setParameter('pid', $pid)
            ;

        if (0 !== $limit) {
            $testimonials->setMaxResults($limit);
        }

        if (true === $random) {
            $testimonials->orderBy('RAND()');
        }

        $result = $testimonials->execute()->fetchAllAssociative();

        if (false !== $result) {
            return $result;
        }

        return null;
    }
}