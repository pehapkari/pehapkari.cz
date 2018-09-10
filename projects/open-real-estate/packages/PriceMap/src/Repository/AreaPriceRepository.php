<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use OpenRealEstate\PriceMap\Entity\AreaPrice;
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
        unset($worksheetRows[0]);

        foreach ($worksheetRows as $key => $areaPriceArray) {
            $areaPrice = new AreaPrice();
            $areaPrice->setCity($areaPriceArray[0]);
            $areaPrice->setCityCode((int) $areaPriceArray[1]);
            $areaPrice->setRegion($areaPriceArray[2]);
            $areaPrice->setRegionCode((int) $areaPriceArray[3]);
            $areaPrice->setFlatPrice((float) $areaPriceArray[4]);
            $areaPrice->setHousePrice((float) $areaPriceArray[5]);
            $areaPrice->setLandPrice((float) $areaPriceArray[6]);

            $this->entityManager->persist($areaPrice);

            if ($key % 25 === 0) {
                $this->entityManager->flush();
            }
        }
    }
}
