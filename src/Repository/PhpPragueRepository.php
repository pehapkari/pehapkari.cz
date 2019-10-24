<?php

declare(strict_types=1);

namespace Pehapkari\Repository;

use Pehapkari\Exception\ShouldNotHappenException;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class PhpPragueRepository
{
    /**
     * @var mixed[]
     */
    private $phpPrague = [];

    /**
     * @param mixed[] $phpPrague
     */
    public function __construct(array $phpPrague)
    {
        $this->phpPrague = $phpPrague;
    }

    /**
     * @return mixed[]
     */
    public function findByYear(int $year): array
    {
        $this->ensureYearIsConfigured($year);

        return $this->phpPrague[$year];
    }

    private function ensureYearIsConfigured(int $year): void
    {
        if (isset($this->phpPrague[$year])) {
            return;
        }

        $conferencesFileInfo = new SmartFileInfo(__DIR__ . '/../../config/_data/conferences.yaml');

        throw new ShouldNotHappenException(sprintf(
            'Year "%d" was not found. Add it to "%s" file',
            $year,
            $conferencesFileInfo->getRelativeFilePathFromDirectory(getcwd())
        ));
    }
}
