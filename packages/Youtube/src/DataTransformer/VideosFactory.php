<?php declare(strict_types=1);

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
            // skip private and deleted videos
            if ($videoItem['snippet']['title'] === 'Private video' || $videoItem['snippet']['title'] === 'Deleted video') {
                continue;
            }

            $videoTitle = $videoItem['snippet']['title'];

            $video = [
                'title' => $videoTitle,
                'description' => $videoItem['snippet']['description'],
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
