<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class AreaPriceRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(EntityManagerInterface $entityManager, Connection $connection)
    {
        $this->entityManager = $entityManager;
        $this->connection = $connection;
    }

    public function importWorksheet(Worksheet $worksheet): void
    {
        // lower performance cost: https://stackoverflow.com/a/35792352/1348344
        $this->connection->getConfiguration()->setSQLLogger(null);

        $worksheetRows = $worksheet->toArray();

        // headlines are not needed, just informative
        unset($worksheetRows);
    }
}
