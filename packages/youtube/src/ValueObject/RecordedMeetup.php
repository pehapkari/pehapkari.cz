<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObject;

use DateTimeInterface;
use Nette\Utils\Strings;

final class RecordedMeetup
{
    private string $title;

    private DateTimeInterface $month;

    /**
     * @var Video[]
     */
    private array $videos = [];

    public function __construct(string $title, DateTimeInterface $month)
    {
        $this->title = $title;
        $this->month = $month;
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

    public function getMonth(): DateTimeInterface
    {
        return $this->month;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getWebalizedTitle(): string
    {
        return Strings::webalize($this->title);
    }
}
