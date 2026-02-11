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
        $details = [];

        foreach (['name', 'company'] as $field) {
            if (!empty($arrRow[$field])) {
                $details[] = $this->translator->trans('tl_testimonials.'.$field.'.0', [], 'contao_tl_testimonials').': '.$arrRow[$field];
            }
        }

        $details[] = $this->translator->trans('tl_testimonials.rating.0', [], 'contao_tl_testimonials').': '.$this->generateRatingStarsByNumber((int) $arrRow['rating']);

        return sprintf(
            '<div class="tl_content_left">%s<br><span class="tl_gray">%s</span></div>',
            $arrRow['identifier'],
            implode(', ', $details)
        );
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
