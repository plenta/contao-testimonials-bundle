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
use Contao\Model\Collection;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Plenta\ContaoTestimonialsBundle\Model\TestimonialsModel;

class Testimonial
{
    public function __construct(
        protected ContaoFramework $framework,
        protected Connection $connection
    ) {
    }

    public function getTestimonialById(int $id): ?array
    {
        return TestimonialsModel::findByPk($id);
    }

    public function getTestimonialsByArchive(int $pid, int $limit, bool $random = false, $categories = ''): ?Collection
    {
        $columns = ['pid = ?', 'published = ?'];
        $values = [$pid, 1];

        if (!empty($categories) && \is_array($categoryArr = StringUtil::deserialize($categories))) {
            $criteria = [];

            foreach ($categoryArr as $category) {
                $criteria[] = 'category LIKE CONCAT("%",?,"%")';
                $values[] = $category;
            }

            $columns[] = '('.implode(' OR ', $criteria).')';
        }

        $options = [];

        if ($limit) {
            $options['limit'] = $limit;
        }

        if ($random) {
            $options['order'] = 'RAND()';
        }

        return TestimonialsModel::findBy($columns, $values, $options);
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
