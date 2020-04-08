<?php

declare(strict_types=1);

namespace Pehapkari\Exception;

use RuntimeException;

final class PostNotFoundException extends RuntimeException
{
    public function __construct(string $slug)
    {
        parent::__construct(sprintf('Post with slug "%s" was not found', $slug));
    }
}
