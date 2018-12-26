<?php declare(strict_types=1);

namespace OpenProject\BetterEasyAdmin\Command;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class DebugConfigPassCommand extends Command
{
    /**
     * @var array
     */
    private $configPasses = [];

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(ConfigManager $configManager, SymfonyStyle $symfonyStyle)
    {
        parent::__construct();
        $this->configManager = $configManager;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(sprintf('Show "%s" implementation in their priority order', ConfigPassInterface::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $configPasses = (new PrivatesAccessor())->getPrivateProperty($this->configManager, 'configPasses');

        $this->symfonyStyle->section('Active config passes');

        foreach ($configPasses as $configPass) {
            $bareClass = Strings::after(get_class($configPass), '\\', -1);
            $mainNamespace = Strings::before(get_class($configPass), '\\', 1);

            $this->symfonyStyle->writeln(sprintf(' * %s (%s)', $bareClass, $mainNamespace));
        }

        $this->symfonyStyle->newLine();
    }
}
