<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObject;

final class RecordedConference
{
    private string $title;

    /**
     * @var Video[]
     */
    private array $videos = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getName(): string
    {
        return $this->title;
    }

    /**
     * @return Video[]
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    /**
     * @param Video[] $videos
     */
    public function setVideos(array $videos): void
    {
        $this->videos = $videos;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
