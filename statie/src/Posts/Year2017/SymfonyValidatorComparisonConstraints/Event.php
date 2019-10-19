<?php

declare(strict_types=1);

namespace Pehapkari\Statie\Posts\Year2017\SymfonyValidatorComparisonConstraints;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

final class Event
{
    /**
     * @var \DateTime
     * @Assert\Type("DateTime")
     */
    private $startDate;

    /**
     * @var \DateTime
     * @Assert\Type("DateTime")
     * @Assert\Expression("value >= this.getStartDate()")
     */
    private $endDate;

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }
}
