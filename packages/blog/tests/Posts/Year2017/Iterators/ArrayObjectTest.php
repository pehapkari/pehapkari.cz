<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Tests\Posts\Year2017\Iterators;

use ArrayObject;
use PHPUnit\Framework\TestCase;

final class ArrayObjectTest extends TestCase
{
    public function test(): void
    {
        // Arrange
        $object = new ArrayObject();
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach ($object as $value1) {
            foreach ($object as $value2) {
                $accumulator[] = [$value1, $value2];
            }
        }

        // Assert
        $this->assertCount(count($object) * count($object), $accumulator); // cartesian product
        $this->assertSame([
            ['first-value', 'first-value'],
            ['first-value', 'second-value'],
            ['second-value', 'first-value'],
            ['second-value', 'second-value'],
        ], $accumulator);
    }

    public function testNewIteratorIsReturnedEveryTime(): void
    {
        // Arrange
        $object = new ArrayObject();

        // Act
        $iterator1 = $object->getIterator();
        $iterator2 = $object->getIterator();

        // Assert
        $this->assertNotSame($iterator1, $iterator2);
    }
}
