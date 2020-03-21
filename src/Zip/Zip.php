<?php

declare(strict_types=1);

namespace Pehapkari\Zip;

use Chumper\Zipper\Zipper;
use Nette\Utils\FileSystem;

final class Zip
{
    private string $zipOutputDirectory;

    private Zipper $zipper;

    public function __construct(Zipper $zipper, string $zipOutputDirectory)
    {
        $this->zipper = $zipper;
        $this->zipOutputDirectory = $zipOutputDirectory;
    }

    /**
     * @param string[] $filePaths
     */
    public function saveZipFileWithFiles(string $filename, array $filePaths): string
    {
        if (! FileSystem::isAbsolute($filename)) {
            $filename = $this->zipOutputDirectory . '/' . $filename;
        }

        $this->zipper->make($filename);
        $this->zipper->add($filePaths);
        $this->zipper->close();

        return $filename;
    }
}
