<?php declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObject;

use DateTimeInterface;

final class Video
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $videoId;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var string
     */
    private $kind;

    /**
     * @var string
     */
    private $speaker;

    /**
     * @var DateTimeInterface
     */
    private $publishedAt;

    public function __construct(
        string $title,
        string $speaker,
        string $description,
        string $videoId,
        string $slug,
        string $thumbnail,
        string $kind,
        DateTimeInterface $publishedAt
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->videoId = $videoId;
        $this->slug = $slug;
        $this->thumbnail = $thumbnail;
        $this->kind = $kind;

        $this->publishedAt = $publishedAt;
        $this->speaker = $speaker;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVideoId(): string
    {
        return $this->videoId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getPublishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function getSpeaker(): string
    {
        return $this->speaker;
    }
}
