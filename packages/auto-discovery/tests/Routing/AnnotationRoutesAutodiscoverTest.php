<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests\Routing;

use OpenProject\AutoDiscovery\Tests\AbstractContainerAwareTestCase;

final class AnnotationRoutesAutodiscoverTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $router = $this->container->get('router');
        $annotationNames = array_keys($router->getRouteCollection()->all());

        $this->assertContains('it-works', $annotationNames);
        $this->assertContains('also-works', $annotationNames);
    }
}
