<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Php;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;

final class NamespaceDetector
{
    public function detectFromDirectory(SplFileInfo $directoryInfo): ?string
    {
        $filesInDirectory = glob($directoryInfo->getRealPath() . '/*.php');
        if (! count($filesInDirectory)) {
            return null;
        }

        $entityFilePath = array_pop($filesInDirectory);

        return $this->detectFromFile($entityFilePath);
    }

    private function detectFromFile(string $filePath): ?string
    {
        $fileContent = FileSystem::read($filePath);

        $match = Strings::match($fileContent, '#namespace(\s+)(?<namespace>[\w\\\\]*?);#');
        if (! isset($match['namespace'])) {
            return null;
        }

        return $match['namespace'];
    }
}
