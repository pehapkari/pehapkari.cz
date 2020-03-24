<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2017\SymfonyValidatorComparisonConstraints;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Event
{
    /**
     * @Assert\Type("DateTime")
     */
    private DateTimeInterface $startDate;

    /**
     * @Assert\Type("DateTime")
     * @Assert\Expression("value >= this.getStartDate()")
     */
    private DateTimeInterface $endDate;

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }
}
