<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\EventListener\DataContainer;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataContainerListener
{

    public function __construct(
        private Connection $connection,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return array
     */
    public function onTestimonialsOptionsCallback(): array
    {
        $testimonials = [];
        $results = $this->connection->fetchAllAssociative('SELECT t.id AS id, t.identifier AS identifier, a.title AS parent FROM tl_testimonials AS t LEFT JOIN tl_testimonials_archive AS a ON t.pid = a.id WHERE t.published=1 ORDER BY t.identifier ASC');

        if (\is_array($results)) {
            foreach ($results as $result) {
                $testimonials[$result['parent']][$result['id']] = $result['identifier'];
            }
        }

        return $testimonials;
    }

    public function listTestimonials(array $arrRow): string
    {
        $label = $arrRow['identifier'];

        $details = [];

        if ($arrRow['name']) {
            $details[] = $this->translator->trans('tl_testimonials.name.0', [], 'contao_tl_testimonials') . ': ' . $arrRow['name'];
        }

        if ($arrRow['company']) {
            $details[] = $this->translator->trans('tl_testimonials.company.0', [], 'contao_tl_testimonials') . ': '.  $arrRow['company'];
        }

        $details[] = $this->translator->trans('tl_testimonials.rating.0', [], 'contao_tl_testimonials') . ': '.$this->generateRatingStarsByNumber((int) $arrRow['rating']);

        if (!empty($details)) {
            $label .= '<br><span class="tl_gray">';
            $label .= implode(', ', $details);
            $label .= '</span>';
        }


        return '<div class="tl_content_left">'.$label.'</div>';
    }

    public function generateRatingStarsByNumber(int $rating): string
    {
        $label = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $label .= '★';
            } else {
                $label .= '☆';
            }
        }

        return $label;
    }
}
