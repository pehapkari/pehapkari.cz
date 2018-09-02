<?php declare(strict_types=1);

namespace OpenRealEstate\Lead;

use OpenRealEstate\Lead\DependencyInjection\LeadExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class LeadBundle extends Bundle
{
    protected function createContainerExtension()
    {
        return new LeadExtension();
    }
}
