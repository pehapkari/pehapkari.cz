<?php

declare(strict_types=1);

namespace Pehapkari\Blog\ValueObject;

use DateTimeInterface;

final class Post
{
    private int $id;

    private string $title;

    private string $slug;

    private DateTimeInterface $dateTime;

    private string $perex;

    private string $htmlContent;

    private string $sourceRelativePath;

    private Author $author;

    public function __construct(
        int $id,
        Author $author,
        string $title,
        string $slug,
        DateTimeInterface $dateTime,
        string $perex,
        string $htmlContent,
        string $sourceRelativePath
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->dateTime = $dateTime;
        $this->perex = $perex;
        $this->htmlContent = $htmlContent;
        $this->sourceRelativePath = $sourceRelativePath;
        $this->author = $author;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getPerex(): string
    {
        return $this->perex;
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function getSourceRelativePath(): string
    {
        return $this->sourceRelativePath;
    }

    public function getWordCount(): int
    {
        $rawContent = strip_tags($this->htmlContent);

        return str_word_count($rawContent);
    }

    public function getAuthorId(): int
    {
        return $this->author->getId();
    }

    public function getAuthorName(): string
    {
        return $this->author->getName();
    }
}
