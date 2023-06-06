<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Helper;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

class Testimonial
{
    public function __construct(
        protected ContaoFramework $framework,
        protected Connection $connection
    ) {
    }

    public function getTestimonialById(int $id): ?array
    {
        $testimonial = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial, rating, addImage, singleSRC')
            ->from('tl_testimonials')
            ->where('id=:id')
            ->andWhere('published=:published')
            ->setParameter('id', $id)
            ->setParameter('published', 1)
            ->executeQuery()
            ->fetchAssociative()
        ;

        if (false !== $testimonial) {
            return $testimonial;
        }

        return null;
    }

    public function getTestimonialsByArchive(int $pid, int $limit, bool $random = false, $categories = ''): ?array
    {
        $testimonials = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial, rating, addImage, singleSRC')
            ->from('tl_testimonials')
            ->where('pid=:pid')
            ->andWhere('published=:published')
            ->setParameter('pid', $pid)
            ->setParameter('published', 1)
        ;

        if (!empty($categories) && \is_array($categoryArr = StringUtil::deserialize($categories))) {
            $criteria = [];

            foreach ($categoryArr as $category) {
                $criteria[] = "categories LIKE '%\"".$category."\"%'";
            }
            $testimonials->andWhere(implode(' OR ', $criteria));
        }

        if (0 !== $limit) {
            $testimonials->setMaxResults($limit);
        }

        if (true === $random) {
            $testimonials->orderBy('RAND()');
        }

        $result = $testimonials->executeQuery()->fetchAllAssociative();

        if (false !== $result) {
            return $result;
        }

        return null;
    }

    public function getRating(int $rating): array
    {
        $result = [];

        for ($i = 1; $i <= 5; ++$i) {
            $result[] = [
                'id' => $i,
                'checked' => ($i <= $rating) ? true : false,
            ];
        }

        return $result;
    }
}
