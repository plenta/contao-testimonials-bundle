<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
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
        return 'Plenta Testimonials 1.1.2 Update';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_content');

        if (!isset($columns['type'])) {
            return false;
        }

        if (
            !$this->connection
                ->query("
                    SELECT EXISTS(
                        SELECT id
                        FROM tl_content
                        WHERE
                            type = 'testimonial_content_element'
                    )
                ")
                ->fetchColumn()
        ) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $stmt = $this->connection->execute("
            UPDATE
                tl_content
            SET
                type = 'plenta_testimonial'
            WHERE
                type = 'testimonial_content_element'
        ");

        return $this->createResult(
            true,
            'Combined '. $stmt->rowCount().' customer names.'
        );
    }
}