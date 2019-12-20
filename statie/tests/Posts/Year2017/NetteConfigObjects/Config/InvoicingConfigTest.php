<?php

declare(strict_types=1);

namespace Pehapkari\Statie\Tests\Posts\Year2017\NetteConfigObjects\Config;

use Nette\DI\Container;
use Pehapkari\Statie\Posts\Year2017\NetteConfigObjects\Config\InvoicingConfig;
use Pehapkari\Statie\Tests\Posts\Year2017\NetteConfigObjects\ContainerFactory;
use PHPUnit\Framework\TestCase;

final class InvoicingConfigTest extends TestCase
{
    /**
     * @var string
     */
    private const PDF_PATH = 'tests/Posts/Year2017/NetteConfigObjects/../invoices';

    /**
     * @var Container
     */
    private $container;

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
        $this->assertStringContainsString(self::PDF_PATH . '/2017001.pdf', $config->getPdfPath(2017001));
    }
}
