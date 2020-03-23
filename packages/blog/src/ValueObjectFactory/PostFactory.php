<?php

declare(strict_types=1);

namespace Pehapkari\Blog\ValueObjectFactory;

use Nette\Utils\Strings;
use ParsedownExtra;
use Pehapkari\Blog\FileSystem\PathAnalyzer;
use Pehapkari\Blog\Repository\AuthorRepository;
use Pehapkari\Blog\ValueObject\Post;
use Pehapkari\Exception\ShouldNotHappenException;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PostFactory
{
    /**
     * @var string
     */
    private const SLASHES_WITH_SPACES_PATTERN = '(?:---[\s]*[\r\n]+)';

    /**
     * @var string
     */
    private const CONFIG_CONTENT_PATTERN = '#^\s*' . self::SLASHES_WITH_SPACES_PATTERN . '?(?<config>.*?)' . self::SLASHES_WITH_SPACES_PATTERN . '(?<content>.*?)$#s';

    private ParsedownExtra $parsedownExtra;

    private PathAnalyzer $pathAnalyzer;

    private AuthorRepository $authorRepository;

    public function __construct(
        ParsedownExtra $parsedownExtra,
        PathAnalyzer $pathAnalyzer,
        AuthorRepository $authorRepository
    ) {
        $this->parsedownExtra = $parsedownExtra;
        $this->pathAnalyzer = $pathAnalyzer;
        $this->authorRepository = $authorRepository;
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): Post
    {
        $matches = Strings::match($smartFileInfo->getContents(), self::CONFIG_CONTENT_PATTERN);

        if (! isset($matches['config'])) {
            throw new ShouldNotHappenException();
        }

        $configuration = Yaml::parse($matches['config']);

        $id = $configuration['id'];
        $title = $configuration['title'];
        $perex = $configuration['perex'];

        $slug = $this->pathAnalyzer->getSlug($smartFileInfo);

        $dateTime = $this->pathAnalyzer->detectDate($smartFileInfo);
        if ($dateTime === null) {
            throw new ShouldNotHappenException();
        }

        if (! isset($matches['content'])) {
            throw new ShouldNotHappenException();
        }
        $htmlContent = $this->parsedownExtra->parse($matches['content']);

        $sourceRelativePath = $this->getSourceRelativePath($smartFileInfo);

        $authorId = (int) $configuration['author'];

        $author = $this->authorRepository->get($authorId);

        return new Post($id, $author, $title, $slug, $dateTime, $perex, $htmlContent, $sourceRelativePath, );
    }

    private function getSourceRelativePath(SmartFileInfo $smartFileInfo): string
    {
        $relativeFilePath = $smartFileInfo->getRelativeFilePath();
        return ltrim($relativeFilePath, './');
    }
}
