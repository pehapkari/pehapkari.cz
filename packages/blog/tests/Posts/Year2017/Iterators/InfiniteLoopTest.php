<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Tests\Posts\Year2017\Iterators;

use PHPUnit\Framework\TestCase;
use SplFixedArray;

final class InfiniteLoopTest extends TestCase
{
    public function test(): void
    {
        // Arrange
        $object = new SplFixedArray(2);
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $i = 0;

        // Act
        $collected = [];
        foreach ($object as $value1) {
            foreach ($object as $value2) {
                $collected[] = $value1;
                $collected[] = $value2;

                if ($i >= 1_000) {
                    continue;
                } // prevent looping to infinity
                ++$i;

                // this is how you make this loop infinite:
                // Task: rewrite this loops as a while loops (see above) and get the idea what is happening
                break;
            }
        }

        // Assert
        $this->assertSame(1_000, $i);
    }
}
