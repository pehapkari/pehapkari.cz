<?php

declare(strict_types=1);

namespace Pehapkari\Training\ValueObject;

final class Font
{
    /**
     * @var string[]
     */
    public const ALL_FONTS = [self::BUNDAY_ITALIC, self::BUNDAY_BOLD];

    /**
     * @var string
     */
    public const BUNDAY_BOLD = 'BundaySlab-Bold';

    /**
     * @var string
     */
    public const BUNDAY_ITALIC = 'BundaySlab-ThinIt';
}
