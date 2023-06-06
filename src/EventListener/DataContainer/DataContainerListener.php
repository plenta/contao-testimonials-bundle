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

class DataContainerListener
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
        return '<div class="tl_content_left">'.$arrRow['identifier'].'</div>';
    }
}
