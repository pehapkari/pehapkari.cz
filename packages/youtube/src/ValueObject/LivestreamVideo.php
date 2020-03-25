<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObject;

use DateTimeInterface;

final class LivestreamVideo
{
    private string $title;

    private string $description;

    private string $videoId;

    private string $slug;

    private DateTimeInterface $month;

    private string $slides;

    private string $thumbnail;

    public function __construct(
        string $title,
        string $description,
        string $videoId,
        string $slug,
        DateTimeInterface $month,
        string $slides,
        string $thumbnail
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->videoId = $videoId;
        $this->slug = $slug;
        $this->month = $month;
        $this->slides = $slides;
        $this->thumbnail = $thumbnail;
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

    public function getMonth(): DateTimeInterface
    {
        return $this->month;
    }

    public function getSlides(): string
    {
        return $this->slides;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}
