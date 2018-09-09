<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Contract;

interface AutodiscovererInterface
{
    public function autodiscover(): void;
}
