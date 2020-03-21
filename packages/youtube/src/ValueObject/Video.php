<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObject;

final class Video
{
    private string $title;

    private string $description;

    private string $videoId;

    private string $slug;

    private string $thumbnail;

    private string $kind;

    private string $speaker;

    private string $slides;

    private string $month;

    private string $link;

    public function __construct(
        string $title,
        string $speaker,
        string $description,
        string $slug,
        string $thumbnail,
        string $kind,
        string $slides,
        string $month,
        // youtube specific
        string $videoId = '',
        // facebook
        string $link = ''
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->slug = $slug;
        $this->thumbnail = $thumbnail;
        $this->kind = $kind;

        $this->month = $month;
        $this->speaker = $speaker;
        $this->slides = $slides;

        // youtube specific
        $this->videoId = $videoId;

        // facebook specific
        $this->link = $link;
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

    public function getMonth(): string
    {
        return $this->month;
    }

    public function getSpeaker(): string
    {
        return $this->speaker;
    }

    public function getSlides(): string
    {
        return $this->slides;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
