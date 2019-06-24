<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Contract\YoutubeVideosProvider;

interface YoutubeVideosProviderInterface
{
    public function getName(): string;

    /**
     * @return mixed[]
     */
    public function providePlaylists(): array;
}
