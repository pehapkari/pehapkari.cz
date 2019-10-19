<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Yaml;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Symfony\Component\Yaml\Yaml;

final class YamlFileGenerator
{
    /**
     * @param mixed[] $data
     */
    public function generate(array $data, string $filePath): void
    {
        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        FileSystem::write($filePath, $this->createTimestampComment() . $yamlDump);
    }

    private function createTimestampComment(): string
    {
        return sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );
    }
}
