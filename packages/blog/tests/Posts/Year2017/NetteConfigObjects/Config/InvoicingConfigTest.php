<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Tests\Posts\Year2017\NetteConfigObjects\Config;

use Nette\DI\Container;
use Pehapkari\Blog\Posts\Year2017\NetteConfigObjects\Config\InvoicingConfig;
use Pehapkari\Blog\Tests\Posts\Year2017\NetteConfigObjects\ContainerFactory;
use PHPUnit\Framework\TestCase;

final class InvoicingConfigTest extends TestCase
{
    /**
     * @var string
     */
    private const PDF_PATH = 'tests/Posts/Year2017/NetteConfigObjects/../invoices';

    private Container $container;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory())->create();
    }

    public function testBasicRequest(): void
    {
        /** @var InvoicingConfig $config */
        $config = $this->container->getByType(InvoicingConfig::class);

        $this->assertSame(7, $config->defaultMaturity);
        $this->assertStringContainsString(self::PDF_PATH, $config->pdfDirectory);
        $this->assertStringContainsString(self::PDF_PATH . '/2017001.pdf', $config->getPdfPath(2_017_001));
    }
}
