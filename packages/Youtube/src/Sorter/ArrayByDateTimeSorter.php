<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Sorter;

use Nette\Utils\DateTime;

final class ArrayByDateTimeSorter
{
    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function sortByKey(array $items, string $key): array
    {
        usort($items, function (array $firstItem, array $secondItem) use ($key): int {
            return DateTime::from($secondItem[$key]) <=> DateTime::from($firstItem[$key]);
        });

        return $items;
    }
}
