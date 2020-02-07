<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\DataTransformer;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;

final class VideosFactory
{
    /**
     * @param mixed[] $videoItems
     * @return mixed[]
     */
    public function createVideos(array $videoItems): array
    {
        $videos = [];

        foreach ($videoItems['items'] as $videoItem) {
            $videoTitle = $videoItem['snippet']['title'];
            if ($this->shouldSkipVideo($videoTitle)) {
                continue;
            }

            $video = [
                'title' => $videoTitle,
                'description' => $this->normalizeDescription($videoItem['snippet']['description']),
                'video_id' => $videoItem['snippet']['resourceId']['videoId'],
                'slug' => Strings::webalize($videoTitle),

                // relevant for livestreams
                'month' => (DateTime::from($videoItem['snippet']['publishedAt'])->format('Y-m')),
            ];

            $video['slides'] = $this->resolveSlides($videoItem['snippet']['description']);
            $video['thumbnail'] = $this->resolveThumbnail($videoItem);

            $videos[] = $video;
        }

        return $videos;
    }

    private function shouldSkipVideo(string $videoTitle): bool
    {
        if ($videoTitle === 'Private video') {
            return true;
        }

        if ($videoTitle === 'Deleted video') {
            return true;
        }

        // These are short promo-videos by DigitalSolutions, not talks that people look for
        if (Strings::match($videoTitle, '#\d+\. sraz#i')) {
            return true;
        }

        return false;
    }

    /**
     * Remove #hashtags, which would cause markdown to render headlines
     */
    private function normalizeDescription(string $description): string
    {
        return Strings::replace($description, '#\#([a-z]+)\s+#i');
    }

    private function resolveSlides(string $description): string
    {
        $match = Strings::match($description, '#(Slides|Slajdy|Slidy)(.*?): (?<slides>[\w:\/\.\-\_]+)#s');

        $match2 = Strings::match($description, '#(?<slides>https:\/\/(www\.)?slideshare[\w\-\d\.\/]+)#s');

        return $match['slides'] ?? $match2['slides'] ?? '';
    }

    /**
     * @param mixed[] $videoItem
     */
    private function resolveThumbnail(array $videoItem): string
    {
        $thumbnails = $videoItem['snippet']['thumbnails'] ?? null;

        if (isset($thumbnails['standard'])) {
            return $thumbnails['standard']['url'];
        }

        if (isset($thumbnails['high'])) {
            return $thumbnails['high']['url'];
        }

        return '';
    }
}
