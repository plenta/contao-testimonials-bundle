<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class ContentTypeMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getName(): string
    {
        return 'Plenta Testimonials 1.1.3 Update';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_content');

        if (!isset($columns['type'])) {
            return false;
        }

        if (true === (bool) $this->connection
                ->executeQuery("
                    SELECT EXISTS(
                        SELECT id
                        FROM tl_content
                        WHERE
                            type = 'testimonial_content_element'
                    )
                ")
                ->fetchOne()
        ) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $stmt = $this->connection->executeQuery("
            UPDATE
                tl_content
            SET
                type = 'plenta_testimonial'
            WHERE
                type = 'testimonial_content_element'
        ");

        return $this->createResult(
            true,
            'Renamed '.$stmt->rowCount().' entries.'
        );
    }
}
