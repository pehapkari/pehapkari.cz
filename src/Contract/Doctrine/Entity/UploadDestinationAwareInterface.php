<?php

declare(strict_types=1);

namespace Pehapkari\Contract\Doctrine\Entity;

interface UploadDestinationAwareInterface
{
    public function setUploadDestination(string $uploadDestination): void;
}
