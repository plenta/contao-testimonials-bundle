<?php

declare(strict_types=1);

namespace Plenta\ContaoTestimonialsBundle\Migration;


use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Plenta\ContaoTestimonialsBundle\Enum\SortingOption;


class RandomSortingMigration extends AbstractMigration
{

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_module'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_module');

        if (!isset($columns['plenta_testimonials_random'])) {
            return false;
        }

        return true;
    }

    public function run(): MigrationResult
    {
        if (!isset($columns['plenta_testimonials_sorting'])) {
            $this->connection->executeStatement("ALTER TABLE tl_module ADD plenta_testimonials_sorting VARCHAR(32) COLLATE ascii_bin NOT NULL DEFAULT :default", [
                'default' => SortingOption::getDefault()->name
            ]);
        }

        $this->connection->executeStatement(
            "UPDATE tl_module SET plenta_testimonials_sorting = :sorting WHERE plenta_testimonials_random = '1'",
            [
                'sorting' => SortingOption::random->name,
            ]
        );

        $this->connection->executeStatement(
            "ALTER TABLE tl_module DROP COLUMN plenta_testimonials_random"
        );

        return $this->createResult(true, 'Migration completed.');
    }
}