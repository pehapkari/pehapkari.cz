<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Contract\FacebookVideosProvider;

interface FacebookVideosProviderInterface
{
    public function getName(): string;

    /**
     * @return mixed[]
     */
    public function providePlaylists(): array;
}
