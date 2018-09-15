<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests\Doctrine;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use OpenProject\AutoDiscovery\Tests\AbstractContainerAwareTestCase;
use OpenProject\AutoDiscovery\Tests\KernelProjectDir\Entity\Product;

final class DoctrineEntityAutodiscoverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var MappingDriver
     */
    private $metadataDriver;

    protected function setUp(): void
    {
        /** @var EntityManager|ObjectManager $entityManager */
        $entityManager = $this->container->get('doctrine')->getManager();
        $configuration = $entityManager->getConfiguration();

        $this->metadataDriver = $configuration->getMetadataDriverImpl();
    }

    public function test(): void
    {
        $this->assertInstanceOf(MappingDriverChain::class, $this->metadataDriver);

        $this->assertNotEmpty($this->metadataDriver->getAllClassNames());

        $this->assertFalse($this->metadataDriver->isTransient(Product::class));
        $this->assertTrue($this->metadataDriver->isTransient('NonExisting'));
    }
}
