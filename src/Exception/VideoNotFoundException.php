<?php

declare(strict_types=1);

namespace Pehapkari\Exception;

use RuntimeException;

final class VideoNotFoundException extends RuntimeException
{
    public function __construct(string $slug)
    {
        parent::__construct(sprintf('Video with slug "%s" was not found', $slug));
    }
}
