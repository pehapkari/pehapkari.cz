<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nette\Utils\Strings;
use OpenRealEstate\PriceMap\Entity\PriceMap;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class PriceMapRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager, Connection $connection)
    {
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->entityRepository = $entityManager->getRepository(PriceMap::class);
    }

    public function importWorksheet(Worksheet $worksheet): void
    {
        // lower performance cost: https://stackoverflow.com/a/35792352/1348344
        $this->connection->getConfiguration()->setSQLLogger(null);

        $worksheetRows = $worksheet->toArray();

        // headlines are not needed, just informative
        unset($worksheetRows[0]);

        foreach ($worksheetRows as $key => $priceMapArray) {
            $areaPrice = new PriceMap();
            $areaPrice->setCity($priceMapArray[0]);
            $areaPrice->setCityCode((int) $priceMapArray[1]);
            $areaPrice->setRegion($priceMapArray[2]);
            $areaPrice->setRegionCode((int) $priceMapArray[3]);
            $areaPrice->setFlatPrice((float) $priceMapArray[4]);
            $areaPrice->setHousePrice((float) $priceMapArray[5]);
            $areaPrice->setLandPrice((float) $priceMapArray[6]);

            $this->entityManager->persist($areaPrice);

            if ($key % 25 === 0) {
                $this->entityManager->flush();
            }
        }
    }

    public function findPriceByZipAndType(string $zip, string $type): ?float
    {
        $priceMap = $this->findOneByZip($zip);
        if ($priceMap === null) {
            return null;
        }

        if ($type === 'flat') {
            return $priceMap->getFlatPrice();
        }

        if ($type === 'house') {
            return $priceMap->getHousePrice();
        }

        if ($type === 'land') {
            return $priceMap->getLandPrice();
        }

        return null;
    }

    private function findOneByZip(string $zip): ?PriceMap
    {
        // get rid of all spaces
        $zip = (int) Strings::replace($zip, '#(\s+)#');

        return $this->entityRepository->findOneBy([
            'cityCode' => $zip,
        ]);
    }
}
